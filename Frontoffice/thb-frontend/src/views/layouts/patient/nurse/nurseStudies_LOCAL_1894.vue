<template>
    <div>
        <patientMenu selectedMenu="menu5" />
        <div class="label-head my-16">
            <span class="px-16 text-bold-24 text-neutral-700">Pengkajian Perawat</span>
        </div>
        <PatientBanner />
        <div class="card-body shadow-sm">
            <div class="row">
                <div class="col-12 col-lg-6">
                </div>
                <div class="col-12 col-lg-6 mt-16 mt-lg-0">
                    <div class="d-flex flex-column flex-lg-row justify-content-lg-end">
                        <!-- <router-link :to="{ path: '/patient/nurse/signature/'+ this.reg_des_id}" target="_blank">
                            <b-button pill variant="w3green" size="sm" class="px-16 mr-2">
                                <b-icon icon="plus" /> <span>TTD Pasien</span>
                            </b-button>
                        </router-link> -->
                        <template v-if="regData.health_facility_id === '3cb626f5-9148-4fb1-8663-3d7f028b01a6' && regData.registration_insurance[0].insurance_id === 2 && regData.registration_insurance[0].company_id === '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8'">
                            <template v-if="regData.registration_insurance[0].sep">
                                <b-button @click="onNurseModal" pill variant="info-600" size="sm" class="px-16 mt-8 mt-lg-0">
                                    <b-icon icon="plus" /> <span>Tambah Kajian Keperawatan</span>
                                </b-button>
                            </template>
                        </template>
                        <template v-else>
                            <b-button @click="onNurseModal" pill variant="info-600" size="sm" class="px-16 mt-8 mt-lg-0">
                                <b-icon icon="plus" /> <span>Tambah Kajian Keperawatan</span>
                            </b-button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
       
        <template v-for="(result,index) in nurseData" >
            <section class="card mt-32">
                <div class="card-body shadow-sm">
                    <ModernLabel :label="getTypeNurseName(result.type_nurse_id)" :small="`Added on ${result.date_added} Oleh ${result.employee_nurse_name}`" />
                    <div class="d-flex mt-12">
                        <router-link :to="{ path: '/patient/nurse/print/'+result.id}" target="_blank">
                            <b-button pill size="sm" variant="w3green" class="px-16 mr-2">
                                <b-icon icon="printer" /> <span>Cetak kajian</span>
                            </b-button>
                        </router-link>
                        <b-button pill size="sm" variant="primary" class="px-16 mr-2" @click="onEditNurse(result)">
                            <i><img src="@/@core/assets/icons/nurse/file-eye.svg" alt=""></i> <span>Lihat kajian</span>
                        </b-button>
                        <b-button @click="openModalTransaction('add', { nurse_id: result.id})" pill variant="outline-success" size="sm" class="px-16">
                            <b-icon icon="plus" /> <span>Tindakan Perawat</span>
                        </b-button>
                    </div>
                    <div class="row mt-16">
                        <div class="col-lg-4">
                            <div>
                                <div class="text-uppercase text-bold-14 text-primary-500">TTV :</div>
                                <div class="mt-8" style="font-size: 13px;">
                                    Systolic = {{ result?.systolic_blood_pressure || '-' }},
                                    Diastolic = {{ result?.diastolic_blood_pressure || '-' }},
                                    Frekuensi Nadi = {{ result?.pulse_frequency || '-' }},
                                    Frekuensi Nafas = {{ result?.breath_frequency || '-' }},
                                    Saturasi Oksigen = {{ result?.oximetry_frequency || '-' }},
                                    Suhu = {{ result?.temperature || '-' }},
                                    <!-- Skor Nyeri = {{ result.sore_score ? result.sore_score : '-' }},
                                    EWS = {{ result.ews ? result.ews : '-' }}, -->
                                    <!-- Kesadaran = {{ getVerbalName(result?.verbal_response || '-') }}, -->
                                    <!-- Gula Darah = {{ result?.blood_sugar || '-' }} -->
                                    Oksigen = {{ getOxygenName(result?.oxygen_type_id || '-') }},
                                    Kesadaran = {{ getAwareName(result?.aware) }},
                                    GCS Mata = {{ getGcsEyeName(result?.eye_score || '-') }},
                                    GCS Verbal = {{ getGcsVerbalName(result?.verbal_score || '-') }},
                                    GCS Motorik = {{ getGcsMotoricName(result?.motor_score || '-') }},
                                    <!-- GCS Pupil = {{ getGcsPupilName(result?.pupil_score) }}, -->
                                </div>
                            </div>
                            <div class="mt-16">
                                <div class="text-uppercase text-bold-14 text-primary-500">Antropometri :</div>
                                <div class="mt-8" style="font-size: 13px;">
                                    Berat Badan = {{ result?.weight || '-' }},
                                    Tinggi Badan = {{ result?.height || '-' }},
                                    IMT = {{ result?.imt || '-' }},
                                    Lingkar Kepala = {{ result?.head_circumference || '-' }},
                                    Lingkar Dada = {{ result?.chest_circumference || '-' }},
                                    Lingkar Lengan Atas = {{ result?.arm_circumference || '-' }},
                                    Lingkar Perut = {{ result?.stomach_circumference || '-' }},
                                    Lingkar Lutut = {{ result?.knee_circumference || '-' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div>
                                <div class="text-uppercase text-bold-14 text-primary-500">Anamnesa :</div>
                                <div class="mt-8" style="font-size: 13px;">
                                    <table class="w-100">
                                        <tr>
                                            <td class="align-top" style="width: 200px">Anamnesa</td>
                                            <td clas="align-top px-3">:</td>
                                            <td class="align-top">{{ result && result.anamesa_type_id == 2 ? 'Allo Anamnesa' : 'Auto Anamnesa' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top">Keluarga</td>
                                            <td clas="align-top px-3">:</td>
                                            <td class="align-top">{{ result && result.family_name ? result.family_name : '-' }} {{ result && result.family_relation_id ? getFamilyRelation(result.family_relation_id) : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top">Keluhan Utama</td>
                                            <td clas="align-top px-3">:</td>
                                            <td class="align-top">
                                                <div v-html="parsedBody(result.main_complaint ? result.main_complaint : '-')"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-top">Riwayat Penyakit Sekarang</td>
                                            <td clas="align-top px-3">:</td>
                                            <td class="align-top">
                                                <div v-html="parsedBody(result.history_disease_now ? result.history_disease_now : '-')"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-top">Riwayat Penyakit Dahulu</td>
                                            <td clas="align-top px-3">:</td>
                                            <td class="align-top">
                                                <div v-html="parsedBody(result.history_disease_past ? result.history_disease_past : '-')"></div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-if="result && result.transaction_nurse.length > 0" class="mt-16 mt-lg-28" style="font-size: 13px">
                        <b-table-simple bordered hover resposive>
                            <b-thead>
                                <b-tr>
                                    <b-th>Tanggal & Jam</b-th>
                                    <b-th>Tindakan</b-th>
                                    <b-th>Jumlah</b-th>
                                    <b-th>Keterangan</b-th>
                                    <b-th>Aksi</b-th>
                                </b-tr>
                            </b-thead>
                            <b-tbody>
                                <template v-for="(itemsResult,i) in result.transaction_nurse" >
                                    <b-tr v-if="itemsResult.nurse_id === result.id">
                                        <b-td>{{ itemsResult.date_added }}</b-td>
                                        <b-td>{{ itemsResult.costing_name }}</b-td>
                                        <b-td>{{ itemsResult.var_1 }}</b-td>
                                        <b-td>{{ itemsResult.notes }}</b-td>
                                        <b-td>
                                            <b-button @click="openModalTransaction('edit', itemsResult)" size="sm" variant="warning-500" class="mr-2">
                                                <feather-icon icon="EditIcon" />
                                            </b-button>
                                            <b-button @click="rowOnDelete(itemsResult)" size="sm" variant="danger-700">
                                                <feather-icon icon="TrashIcon" />
                                            </b-button>
                                        </b-td>
                                    </b-tr>
                                </template>
                            </b-tbody>
                        </b-table-simple>
                    </div>
                </div>
            </section>
        </template>
        
        <b-modal v-model="open_modal.nurse" title="Tambah Rincian" hide-footer no-close-on-backdrop>
            <div>
                <div class="mt-12">
                    <label class="c-form-label">Tindakan Perawat <span class="text-danger">*</span></label>
                    <v-select v-model="form_transaction.costing_id" aria-readonly="true" :options="OptionsCosting" :reduce="OptionsCosting => OptionsCosting" label="title" placeholder="-- Tindakan Perawat --"></v-select>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Jumlah <span class="text-danger">*</span></label>
                    <b-form-input type="number" min="1" v-model="form_transaction.var_1" />
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Keterangan</label>
                    <b-textarea v-model="form_transaction.notes" rows="3"></b-textarea>
                </div>
                <div class="d-flex justify-content-end mt-24">
                    <b-button @click.prevent="validationFormTransaction" :disabled="disabled.transaction" variant="indigo-500" class="px-3">Simpan</b-button>
                </div>
            </div>
        </b-modal>
        <b-modal v-model="open_modal.main_modal" scrollable size="lg" title="Pengkajian perawat" hide-footer @close="onNurseModal">
            <section class="card">
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
            </section>
            <!-- /row 1 -->
            <section class="mt-20 mt-lg-32">
                <div class="card">
                    <div class="card-header bg-indigo-200">
                        <span class="text-bold-18">Anamnesa</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 d-flex">
                                <label class="my-auto text-semi-14">Anamnesa</label>
                            </div>
                            <div class="col">
                                <v-select @change="saveChangedNurse($event)" v-model="form_nurse.anamesa_type_id" aria-readonly="true" :options="OptionsAnamesaType" :reduce="OptionsAnamesaType => OptionsAnamesaType.value" label="title" placeholder="-- Pilih Anamnesa --"></v-select>
                            </div>
                        </div>
                        <div class="row" v-if="form_nurse.anamesa_type_id === 2">
                            <div class="col-lg-6 px-0">
                                <div class="row mx-0 mt-10">
                                    <div class="col-lg-6 d-lg-flex">
                                        <label class="my-auto text-semi-14">Nama Keluarga</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <b-form-input type="text" @change="saveChangedNurse($event)" v-model="form_nurse.family_name" autocomplete="off"></b-form-input>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 px-0">
                                <div class="row mx-0 mt-10">
                                    <div class="col-lg-6 d-lg-flex">
                                        <label class="m-lg-auto text-semi-14">Hubungan Keluarga</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <v-select @change="saveChangedNurse($event)" v-model="form_nurse.family_relation_id" aria-readonly="true" :options="OptionsFamilyRelation" :reduce="OptionsFamilyRelation => OptionsFamilyRelation.value" label="title" placeholder="-- Pilih Hubungan Keluarga --"></v-select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="my-auto text-semi-14">Keluhan Utama</label>
                            </div>
                            <div class="col">
                                <b-form-textarea rows="3" @change="saveChangedNurse($event)" v-model="form_nurse.main_complaint" autocomplete="off"></b-form-textarea>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="my-auto text-semi-14">Riwayat Penyakit Sekarang</label>
                            </div>
                            <div class="col">
                                <b-form-textarea rows="3" @change="saveChangedNurse($event)" v-model="form_nurse.history_disease_now" autocomplete="off"></b-form-textarea>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="my-auto text-semi-14">Riwayat Penyakit Dahulu</label>
                            </div>
                            <div class="col">
                                <b-form-textarea rows="3" @change="saveChangedNurse($event)" v-model="form_nurse.history_disease_past" autocomplete="off"></b-form-textarea>
                            </div>
                        </div>
                        <!-- <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="my-auto text-semi-14">Riwayat Tensi</label>
                            </div>
                            <div class="col">
                                <b-form-textarea rows="3" @change="saveChangedNurse($event)" v-model="form_nurse.history_tension" autocomplete="off"></b-form-textarea>
                            </div>
                        </div> -->
                    </div>
                </div>
            </section>
            <!-- /row 2 -->
            <section class="mt-20 mt-lg-32">
                <div class="card">
                    <div class="card-header bg-indigo-200">
                        <span class="text-bold-18">Pemeriksaan Fisik</span>
                    </div>
                    <div class="card-body">
                        <div class="col px-0">
                            <ModernLabel label="Tanda Vital" />
                            <div class="row mt-12 c-form">
                                <div class="col-lg-4">
                                    <label>Systolic <span class="text-danger">*</span></label>
                                    <b-input-group append="mmHg">
                                        <b-form-input type="number" autocomplete="off" @change="saveChangedNurse($event)" v-model="form_nurse.systolic_blood_pressure"></b-form-input>
                                    </b-input-group>
                                </div>
                                <div class="col-lg-4 mt-2 mt-lg-0">
                                    <label>Diastolic <span class="text-danger">*</span></label>
                                    <b-input-group append="mmHg">
                                        <b-form-input type="number" autocomplete="off" @change="saveChangedNurse($event)" v-model="form_nurse.diastolic_blood_pressure"></b-form-input>
                                    </b-input-group>
                                </div>
                                <div class="col-lg-4 mt-2 mt-lg-0">
                                    <label>Frekuensi Nadi <span class="text-danger">*</span></label>
                                    <b-input-group append="x/mnt">
                                        <b-form-input type="number" autocomplete="off" @change="saveChangedNurse($event)" v-model="form_nurse.pulse_frequency"></b-form-input>
                                    </b-input-group>
                                </div>
                                <div class="col-lg-4 mt-2">
                                    <label>Frekuensi Nafas <span class="text-danger">*</span></label>
                                    <b-input-group append="x/mnt">
                                        <b-form-input type="number" autocomplete="off" @change="saveChangedNurse($event)" v-model="form_nurse.breath_frequency"></b-form-input>
                                    </b-input-group>
                                </div>
                                <div class="col-lg-4 mt-2">
                                    <label>Saturasi Oksigen <span class="text-danger">*</span></label>
                                    <b-input-group append="%">
                                        <b-form-input type="number" autocomplete="off" @change="saveChangedNurse($event)" v-model="form_nurse.oximetry_frequency"></b-form-input>
                                    </b-input-group>
                                </div>
                                <div class="col-lg-4 mt-2">
                                    <label>Suhu <span class="text-danger">*</span></label>
                                    <b-input-group append="ᵒC">
                                        <b-form-input type="number" autocomplete="off" @change="saveChangedNurse($event)" v-model="form_nurse.temperature"></b-form-input>
                                    </b-input-group>
                                </div>
                                <div class="col-lg-4 mt-2">
                                    <label class="c-form-label">Oksigen</label>
                                    <v-select v-model="form_nurse.oxygen_type_id" :options="OptionsOxygen" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                </div>
                                <div class="col-lg-4 mt-2">
                                    <label class="c-form-label">Kesadaran</label>
                                    <v-select v-model="form_nurse.aware" :options="OptionsAware" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                </div>
                                <div class="col-lg-4 mt-2">
                                    <label class="c-form-label">GCS Mata</label>
                                    <v-select v-model="form_nurse.eye_score" :options="OptionGcsEye" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                </div>
                                <div class="col-lg-4 mt-2">
                                    <label class="c-form-label">GCS verbal</label>
                                    <v-select v-model="form_nurse.verbal_score" :options="OptionGcsVerbal" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                </div>
                                <div class="col-lg-4 mt-2">
                                    <label class="c-form-label">GCS Motorik</label>
                                    <v-select v-model="form_nurse.motor_score" :options="OptionGcsMotoric" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                </div>
                                <!-- <div class="col-lg-4 mt-2">
                                    <label class="c-form-label">GCS Pupil</label>
                                    <v-select v-model="form_nurse.pupil_score" :options="OptionGcsPupil" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                </div> -->
                                <!-- <div class="col-lg-4">
                                    <div class="mt-1">
                                        <label class="c-form-label">Skor Nyeri <span class="text-danger">*</span></label>
                                        <b-form-input autocomplete="off" v-model="form_nurse.sore_score"></b-form-input>
                                    </div>
                                    <div class="mt-1">
                                        <label class="c-form-label">EWS</label>
                                        <b-form-input autocomplete="off" v-model="form_nurse.ews"></b-form-input>
                                    </div>
                                    <div class="mt-1">
                                        <label>Gula Darah</label>
                                        <b-input-group append="mg/dL">
                                            <b-form-input type="number" autocomplete="off" @change="saveChangedNurse($event)" v-model="form_nurse.blood_sugar"></b-form-input>
                                        </b-input-group>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                        <div class="col mt-32 px-0">
                            <ModernLabel label="Antropometri" />
                            
                            <div class="row mt-12 c-form">
                                <div class="col-12 col-lg-4">
                                    <label>Berat Badan <span class="text-danger">*</span></label>
                                    <b-input-group append="kg">
                                        <b-form-input type="number" v-model="form_nurse.weight" @input="imtChanged($event)"></b-form-input>
                                    </b-input-group>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <label>Tinggi Badan <span class="text-danger">*</span></label>
                                    <b-input-group append="cm">
                                        <b-form-input type="number" v-model="form_nurse.height" @input="imtChanged($event)"></b-form-input>
                                    </b-input-group>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <label>IMT</label>
                                    <b-input-group append="kg/m²">
                                        <b-form-input type="number" disabled v-model="form_nurse.imt"></b-form-input>
                                    </b-input-group>
                                </div>
                            </div>
                            <b-collapse id="collapse-anto" v-model="active.advance.antopometri" class="mb-8 mt-12">
                                <div class="row c-form">                                    
                                    <div class="col-12 col-lg-4">
                                        <label>Lingkar Kepala</label>
                                        <b-input-group append="cm">
                                            <b-form-input type="number" @change="saveChangedNurse($event)" v-model="form_nurse.head_circumference"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <label>Lingkar Dada</label>
                                        <b-input-group append="cm">
                                            <b-form-input type="number" @change="saveChangedNurse($event)" v-model="form_nurse.chest_circumference"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <label>Lingkar Lengan Atas</label>
                                        <b-input-group append="cm">
                                            <b-form-input type="number" @change="saveChangedNurse($event)" v-model="form_nurse.arm_circumference"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <label>Lingkar Perut</label>
                                        <b-input-group append="cm">
                                            <b-form-input type="number" @change="saveChangedNurse($event)" v-model="form_nurse.stomach_circumference"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <label>Lingkar Lutut</label>
                                        <b-input-group append="cm">
                                            <b-form-input type="number" @change="saveChangedNurse($event)" v-model="form_nurse.knee_circumference"></b-form-input>
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
                            <ModernLabel label="Fungsional" />
                            
                            <div class="row mt-12 c-form">
                                <div class="col-12 col-lg-4">
                                    <label>Alat Bantu</label>
                                    <b-form-input @change="saveChangedNurse($event)" v-model="form_nurse.aids"></b-form-input>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <label>Prothesa</label>
                                    <b-form-input @change="saveChangedNurse($event)" v-model="form_nurse.prothesa"></b-form-input>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <label>Cacat Tubuh</label>
                                    <b-form-input @change="saveChangedNurse($event)" v-model="form_nurse.disabled"></b-form-input>
                                </div>
                            </div>
                            <div class="row mt-16 mt-lg-20">
                                <div class="col-12 col-lg-6">
                                    <ModernLabel label="Activity Daily Living" />
                                    <b-form-radio-group @change="saveChangedNurse($event)" v-model="form_nurse.adl_type_id" class="mt-8 mt-lg-20">
                                        <b-form-radio class="my-1" value="1">Mandiri</b-form-radio>
                                        <b-form-radio class="my-1" value="2">Dibantu</b-form-radio>
                                        <b-form-radio class="my-1" value="3">Dibantu Sebagian</b-form-radio>
                                    </b-form-radio-group>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- <section v-if="this.regData.health_facility_item_id == 'cca59ec4-d77a-45fd-b51f-aae069da22ea'" class="mt-20 mt-lg-32"> -->
            <section v-if="this.view_condition.health_facility_item_id == 'cca59ec4-d77a-45fd-b51f-aae069da22ea'" class="mt-20 mt-lg-32">
                <div class="card">
                    <div class="card-header text-white font-weight-bold bg-primary">PENGKAJIAN AWAL IGD</div>
                    <div class="card-body">
                        <section>
                            <div class="card-header text-semi-16 rounded bg-neutral-200 border-bottom-0"
                                style="margin-left:-16px; margin-right:-16px;">Riwayat Kesehatan
                            </div>
                            <div class="row mt-12">
                                <div class="col-lg-6">
                                    <div>
                                        <ModernLabel label="Pernah Dirawat sebelumnya" />
                                        <b-form-radio-group v-model="form_igd.been_hospitalized_type_id" class="mt-8 mt-lg-20">
                                            <b-form-radio value="0">Tidak</b-form-radio>
                                            <b-form-radio value="1">Ya, dimana?</b-form-radio>
                                        </b-form-radio-group>
                                        <b-form-input v-model="form_igd.been_hospitalized_answer" v-if="form_igd.been_hospitalized_type_id == '1'" class="mt-10 col-lg-8" />
                                    </div>
                                    <div class="mt-32">
                                        <ModernLabel label="Riwayat Penyakit Keluarga" />
                                        <b-form-radio-group v-model="form_igd.any_history_family_disease_type_id" class="mt-8 mt-lg-20">
                                            <b-form-radio value="0">Tidak</b-form-radio>
                                            <b-form-radio value="1">Ya</b-form-radio>
                                        </b-form-radio-group>
                                        <b-form-checkbox-group size="sm" v-model="form_igd.any_history_family_answer_arry" class="mt-10" v-if="form_igd.any_history_family_disease_type_id == '1'">
                                            <b-form-checkbox value="a" class="col-lg-3 my-1 mr-0">DM</b-form-checkbox>
                                            <b-form-checkbox value="b" class="col-lg-3 my-1 mr-0">Hipertensi</b-form-checkbox>
                                            <b-form-checkbox value="c" class="col-lg-3 my-1 mr-0">Cancer</b-form-checkbox>
                                            <b-form-checkbox value="d" class="col-lg-3 my-1 mr-0">Jantung</b-form-checkbox>
                                            <b-form-checkbox value="e" class="col-lg-3 my-1 mr-0">TBC</b-form-checkbox>
                                            <b-form-checkbox value="f" class="col-lg-3 my-1 mr-0">Anemia</b-form-checkbox>
                                            <b-form-checkbox value="g" class="col-lg-6 my-1 mr-0">Lain-lain, sebutkan:</b-form-checkbox>
                                        </b-form-checkbox-group>
                                        <b-form-input v-model="form_igd.any_history_family_answer" class="mt-10 col-lg-8" v-if="form_igd.any_history_family_disease_type_id == '1' && form_igd.any_history_family_answer_arry.indexOf('g') !== -1" />
                                    </div>
                                </div>
                                <div class="col-lg-6 mt-32 mt-lg-0">
                                    <div>
                                        <ModernLabel label="Operasi Yang Pernah Di Alami" />
                                        <b-form-radio-group v-model="form_igd.any_surgery_type_id" class="mt-8 mt-lg-20">
                                            <b-form-radio value="0">Tidak Pernah</b-form-radio>
                                            <b-form-radio value="1">Ya, Pernah</b-form-radio>
                                        </b-form-radio-group>
                                        <div v-if="form_igd.any_surgery_type_id == '1'" class="mt-10">
                                            <label class="c-form-label">Kapan</label>
                                            <b-form-input type="date" v-model="form_igd.any_surgery_date" />
                                        </div>
                                    </div>
                                    <div class="mt-32">
                                        <ModernLabel label="Riwayat Alergi" />
                                        <b-form-radio-group v-model="form_igd.history_allergy_type_id" class="mt-8 mt-lg-20">
                                            <b-form-radio value="0">Tidak</b-form-radio>
                                            <b-form-radio value="1">Ya, sebutkan</b-form-radio>
                                        </b-form-radio-group>
                                        <b-form-input v-model="form_igd.history_allergy" class="mt-10 col-lg-8" v-if="form_igd.history_allergy_type_id == '1'" />
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="mt-32">
                            <div class="text-semi-16 card-header rounded bg-neutral-200 border-bottom-0"
                                style="margin-left:-16px; margin-right:-16px;">Riwayat Psikologis
                            </div>
                            <ModernLabel label="Pilih Riwayat" class="mt-12" />
                            <b-form-checkbox-group v-model="form_igd.history_psychological_arry" size="sm" class="mt-10 mt-lg-20">
                                <b-form-checkbox value="a" class="col-lg-3 my-1 mr-0">Tidak Semangat</b-form-checkbox>
                                <b-form-checkbox value="b" class="col-lg-3 my-1 mr-0">Cepat Lelah</b-form-checkbox>
                                <b-form-checkbox value="c" class="col-lg-3 my-1 mr-0">Menggunakan Obat Penenang</b-form-checkbox>
                                <b-form-checkbox value="d" class="col-lg-3 my-1 mr-0">Rasa Tertekan</b-form-checkbox>
                                <b-form-checkbox value="e" class="col-lg-3 my-1 mr-0">Sulit Berbicara</b-form-checkbox>
                                <b-form-checkbox value="f" class="col-lg-3 my-1 mr-0">Merasa Bersalah</b-form-checkbox>
                                <b-form-checkbox value="g" class="col-lg-3 my-1 mr-0">Depresi</b-form-checkbox>
                                <b-form-checkbox value="h" class="col-lg-3 my-1 mr-0">Kurang Nafsu Makan</b-form-checkbox>
                                <b-form-checkbox value="i" class="col-lg-3 my-1 mr-0">Tenang</b-form-checkbox>
                                <b-form-checkbox value="j" class="col-lg-3 my-1 mr-0">Sulit Tidur</b-form-checkbox>
                                <b-form-checkbox value="k" class="col-lg-3 my-1 mr-0">Sulit Konsentrasi</b-form-checkbox>
                                <b-form-checkbox value="l" class="col-lg-3 my-1 mr-0">Tidak Ada Keluhan</b-form-checkbox>
                            </b-form-checkbox-group>
                        </section>

                        <section class="mt-32">
                            <div class="text-semi-16 card-header rounded bg-neutral-200 border-bottom-0"
                                style="margin-left:-16px; margin-right:-16px;">Skrining Gizi
                            </div>
                            <div class="row mt-16 mt-lg-20">
                                <div class="col-lg-6">
                                    <div class="mb-lg-10">
                                        <div class="c-form-label pb-0">Indeks Masa Tubuh</div>
                                        <small class="text-muted font-italic">(IMT = Berat Badan/ Tinggi Badan dalam meter)</small>
                                    </div>
                                    <b-form-select @change="handleNutritionScore" disabled v-model="form_igd.body_mass_index" :options="options_body_mass"></b-form-select>                                     
                                </div>
                                <div class="col-lg-6 mt-10 mt-lg-0">
                                    <div class="mb-lg-10">
                                        <div class="c-form-label pb-0">Asupan Makan Pasien</div>
                                        <small class="text-muted font-italic">Dalam 5 Hari Terakhir</small>
                                    </div>
                                    <b-form-select @change="handleNutritionScore" v-model="form_igd.patient_food_intake" :options="options_food_intake"></b-form-select>
                                </div>
                            </div>
                            <div class="row mt-lg-20 align-items-lg-end">
                                <div class="col-lg-6 mt-10 mt-lg-0">
                                    <div class="mb-lg-10">
                                        <div class="c-form-label pb-0">Kehilangan Berat Badan</div>
                                        <small class="text-muted font-italic">Dalam Waktu 6 Bulan Terakhir</small>
                                    </div>
                                    <b-form-select @change="handleNutritionScore" v-model="form_igd.six_month_lose_weight" :options="options_six_months"></b-form-select>
                                </div>
                                <div v-if="form_igd.six_month_lose_weight == '3'" class="col-lg-6 mt-10 mt-lg-0">
                                    <div class="mb-lg-10">
                                        <div class="c-form-label pb-0">Kehilangan Berat Badan</div>
                                    </div>
                                    <b-form-select @change="handleNutritionScore" v-model="form_igd.lose_weight" :options="options_lose_weight"></b-form-select>
                                </div>
                            </div>
                            <div class="row mt-lg-20 align-items-lg-end">
                                <div class="col-lg-6 mt-10 mt-lg-0">
                                    <div class="mb-lg-10">
                                        <div class="c-form-label pb-0">Kondisi Penyakit Pasien</div>
                                        <small class="text-muted font-italic">Yang Mempunyai Risiko Nutrisi</small>
                                    </div>
                                    <b-form-select @change="handleNutritionScore" v-model="form_igd.patient_disease_condition" :options="options_disease_condition"></b-form-select>
                                </div>
                                <div class="col-lg-6 mt-10 mt-lg-0">
                                    <div class="mb-lg-10">
                                        <div class="c-form-label pb-0">Pasien Sedang Mendapat Diet Makanan Tertentu</div>
                                    </div>
                                    <b-form-select @change="handleNutritionScore" v-model="form_igd.patient_certain_diets" :options="options_certain_diets"></b-form-select>
                                </div>
                                
                            </div>
                            <div class="col px-0 mt-32">
                                <div class="row">
                                    <div class="col-lg-6 mt-10 mt-lg-0">
                                        <b-input-group prepend="TOTAL SKOR">
                                            <b-form-input v-model="form_igd.nutrition_total_score" disabled></b-form-input>
                                        </b-input-group>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="mt-32">
                            <div class="text-semi-16 card-header rounded bg-neutral-200 border-bottom-0"
                                style="margin-left:-16px; margin-right:-16px;">Pengkajian Risiko Jatuh
                            </div>
                            <div class="mt-20">
                                <b-table-simple bordered responsive>
                                    <b-thead>
                                        <b-tr>
                                            <b-th>Komponen Penilaian</b-th>
                                            <b-th>Tidak</b-th>
                                            <b-th>Ya</b-th>
                                        </b-tr>
                                    </b-thead>
                                    <b-tbody>
                                        <b-tr>
                                            <b-td>
                                                <ol type="a">
                                                    <li>Perhatikan cara jalan pasien saat akan duduk di kursi. Apakah pasien tampak seimbang (Sempoyong/ seimbang) ?</li>
                                                </ol>
                                            </b-td>
                                            <b-td style="width: 70px;">
                                                <b-form-radio-group v-model="form_igd.fall_risk_component_a">
                                                    <b-form-radio value="0" />
                                                </b-form-radio-group>
                                            </b-td>
                                            <b-td style="width: 70px;">
                                                <b-form-radio-group v-model="form_igd.fall_risk_component_a">
                                                    <b-form-radio value="1" />
                                                </b-form-radio-group>
                                            </b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td>
                                                <ol type="a" start="2">
                                                    <li>Apakah pasien memegang pinggiran kursi atau benda lain sebagai penopang saat akan duduk ?</li>
                                                </ol>
                                            </b-td>
                                            <b-td>
                                                <b-form-radio-group v-model="form_igd.fall_risk_component_b">
                                                    <b-form-radio value="0" />
                                                </b-form-radio-group>
                                            </b-td>
                                            <b-td>
                                                <b-form-radio-group v-model="form_igd.fall_risk_component_b">
                                                    <b-form-radio value="1" />
                                                </b-form-radio-group>
                                            </b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td colspan="3">
                                                <b-form-radio-group v-model="form_igd.fall_risk_type_id" stacked>
                                                    <b-form-radio class="my-1" value="1">Tidak Beresiko (Tidak ditemukan a dan b)</b-form-radio>
                                                    <b-form-radio class="my-1" value="2">Beresiko rendah (Ditemukan  a atau b)</b-form-radio>
                                                    <b-form-radio class="my-1" value="3">Beresiko Tinggi (Ditemukan a dan b)</b-form-radio>
                                                </b-form-radio-group>
                                            </b-td>
                                        </b-tr>
                                    </b-tbody>
                                </b-table-simple>
                            </div>
                            <!-- <div class="row">
                                <div class="col-lg-6 mt-20">
                                    <ModernLabel label="Metode" />
                                    <div class="mt-10 mt-lg-20">
                                        <b-form-radio-group v-model="form_igd.fall_risk_method_type_id" size="sm">
                                            <b-form-radio class="my-1" value="1">Morse Fall Scale</b-form-radio>
                                            <b-form-radio class="my-1" value="2">Humpty Dumpty</b-form-radio>
                                            <b-form-radio class="my-1" value="3">Geriatri</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                </div>
                                <div class="col-lg mt-20">
                                    <ModernLabel label="Risiko" />
                                    <b-form-radio-group v-model="form_igd.fall_risk_type_id" class="mt-8 mt-lg-20">
                                        <b-form-radio class="my-1" value="1">Rendah</b-form-radio>
                                        <b-form-radio class="my-1" value="2">Sedang</b-form-radio>
                                        <b-form-radio class="my-1" value="3">Tinggi</b-form-radio>
                                    </b-form-radio-group>
                                </div>
                            </div> -->
                        </section>
                        <section class="mt-32">
                            <div class="text-semi-16 card-header rounded bg-neutral-200 border-bottom-0"
                                style="margin-left:-16px; margin-right:-16px;">Daftar Masalah Keperawatan
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mt-20">
                                    <div>
                                        <ModernLabel label="Diagnosa Keperawatan" />
                                        <b-form-checkbox-group v-model="form_igd.nursing_diagnoses_arry" size="sm" class="mt-10">
                                            <b-form-checkbox value="a" class="col-lg-6 my-1 mr-0">Bersihkan jalan nafas</b-form-checkbox>
                                            <b-form-checkbox value="b" class="col-lg-6 my-1 mr-0">Cemas/ Ansietas</b-form-checkbox>
                                            <b-form-checkbox value="c" class="col-lg-6 my-1 mr-0">Ketidakefektifan jalan nafas</b-form-checkbox>
                                            <b-form-checkbox value="d" class="col-lg-6 my-1 mr-0">Hipertemi</b-form-checkbox>
                                            <b-form-checkbox value="e" class="col-lg-6 my-1 mr-0">Gangguan mobilitas fisik</b-form-checkbox>
                                            <b-form-checkbox value="f" class="col-lg-6 my-1 mr-0">Keterlambatan pertumbuhan dan perkembangan</b-form-checkbox>
                                            <b-form-checkbox value="g" class="col-lg-6 my-1 mr-0">Intoleransi aktifitas</b-form-checkbox>
                                            <b-form-checkbox value="h" class="col-lg-6 my-1 mr-0">Risiko tinggi infeksi</b-form-checkbox>
                                            <b-form-checkbox value="i" class="col-lg-6 my-1 mr-0">Gangguan persepsi sensori pendengaran</b-form-checkbox>
                                            <b-form-checkbox value="j" class="col-lg-6 my-1 mr-0">Risiko tinggi cedera</b-form-checkbox>
                                            <b-form-checkbox value="k" class="col-lg-6 my-1 mr-0">Gangguan persepsi sensori penglihatan</b-form-checkbox>
                                            <b-form-checkbox value="l" class="col-lg-6 my-1 mr-0">Risiko kekurangan cairan</b-form-checkbox>
                                            <b-form-checkbox value="m" class="col-lg-6 my-1 mr-0">Gangguan integritas kulit</b-form-checkbox>
                                            <b-form-checkbox value="n" class="col-lg-6 my-1 mr-0">Hipertensi</b-form-checkbox>
                                            <b-form-checkbox value="o" class="col-lg-6 my-1 mr-0">Kurang pengetahuan</b-form-checkbox>
                                        </b-form-checkbox-group>
                                        
                                        <b-textarea v-model="form_igd.nursing_diagnoses" rows="3" placeholder="Catatan lainnya" class="mt-16" />
                                    </div>
                                </div>
                                <div class="col-lg-6 mt-20">
                                    <ModernLabel label="Implementasi" />
                                    <b-form-checkbox-group v-model="form_igd.implementation_arry" size="sm" class="mt-10">
                                        <b-form-checkbox value="a" class="col-lg-6 my-1 mr-0">Menjelaskan cara pencegahan dan perawatan kulit</b-form-checkbox>
                                        <b-form-checkbox value="b" class="col-lg-6 my-1 mr-0">Sehat</b-form-checkbox>
                                        <b-form-checkbox value="c" class="col-lg-6 my-1 mr-0">Mengajarkan teknik nafas dalam</b-form-checkbox>
                                        <b-form-checkbox value="d" class="col-lg-6 my-1 mr-0">MCU</b-form-checkbox>
                                        <b-form-checkbox value="e" class="col-lg-6 my-1 mr-0">Mengajarkan batuk efektif</b-form-checkbox>
                                        <b-form-checkbox value="f" class="col-lg-6 my-1 mr-0">Menganjurkan untuk banyak makan buah segar dan kacang-kacangan</b-form-checkbox>
                                        <b-form-checkbox value="g" class="col-lg-6 my-1 mr-0">Menginformasikan tidak merokok dalam ruangan</b-form-checkbox>
                                        <b-form-checkbox value="h" class="col-lg-6 my-1 mr-0">SKPN dan Rapid test</b-form-checkbox>
                                        <b-form-checkbox value="i" class="col-lg-6 my-1 mr-0">Memantau tanda-tanda sianosis</b-form-checkbox>
                                        <b-form-checkbox value="j" class="col-lg-6 my-1 mr-0">MCU calon karyawan</b-form-checkbox>
                                        <b-form-checkbox value="k" class="col-lg-6 my-1 mr-0">Mengukur warna dan suhu kulit</b-form-checkbox>
                                        <b-form-checkbox value="l" class="col-lg-6 my-1 mr-0">Tidak menahan BAK</b-form-checkbox>
                                        <b-form-checkbox value="m" class="col-lg-6 my-1 mr-0">Mengatur posisi nyaman</b-form-checkbox>
                                        <b-form-checkbox value="n" class="col-lg-6 my-1 mr-0">Memberikan obat tetes mata</b-form-checkbox>
                                        <b-form-checkbox value="o" class="col-lg-6 my-1 mr-0">Menganjurkan banyak minum</b-form-checkbox>
                                        <b-form-checkbox value="p" class="col-lg-6 my-1 mr-0">Manganjurkan pola hidup sehat</b-form-checkbox>
                                        <b-form-checkbox value="q" class="col-lg-6 my-1 mr-0">Kompres air hangat pada daerah yang gatal</b-form-checkbox>
                                        <b-form-checkbox value="r" class="col-lg-6 my-1 mr-0">Swab PCR some day</b-form-checkbox>
                                        <b-form-checkbox value="s" class="col-lg-6 my-1 mr-0">Jaga kebersihan</b-form-checkbox>
                                        <b-form-checkbox value="t" class="col-lg-6 my-1 mr-0">Injeksi Vit. C</b-form-checkbox>
                                        <b-form-checkbox value="u" class="col-lg-6 my-1 mr-0">SKPN</b-form-checkbox>
                                        <b-form-checkbox value="v" class="col-lg-6 my-1 mr-0">Menganjurkan banyak istirahat</b-form-checkbox>
                                        <b-form-checkbox value="w" class="col-lg-6 my-1 mr-0">Anjurkan banyak minum air putih</b-form-checkbox>
                                        <b-form-checkbox value="x" class="col-lg-6 my-1 mr-0">Infus Vit. C</b-form-checkbox>
                                        <b-form-checkbox value="y" class="col-lg-6 my-1 mr-0">Makan porsi kecil tapi sering</b-form-checkbox>
                                        <b-form-checkbox value="z" class="col-lg-6 my-1 mr-0">Istirahatkan mata</b-form-checkbox>
                                        <b-form-checkbox value="aa" class="col-lg-6 my-1 mr-0">Nyeri</b-form-checkbox>
                                        <b-form-checkbox value="ab" class="col-lg-6 my-1 mr-0">Menganjurkan banyak makan sayur</b-form-checkbox>
                                        <b-form-checkbox value="ac" class="col-lg-6 my-1 mr-0">Anti HIV rapid</b-form-checkbox>
                                    </b-form-checkbox-group>
                                    <b-textarea v-model="form_igd.implementation" rows="3" placeholder="Catatan lainnya" class="mt-16" />
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </section>

            <!-- <section v-if="this.regData.health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f' && isAdult == true" class="mt-20 mt-lg-32"> -->
            <section v-if="this.view_condition.health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f' && view_condition.isAdult == true" class="mt-20 mt-lg-32">
                <div class="card">
                    <div class="card-header text-white font-weight-bold bg-primary">PENGKAJIAN AWAL RAWAT INAP DEWASA</div>
                    <div class="card-body">
                        <section class="">
                            <ModernLabel label="DISCHARGE PLANNING" />
                            <div class="row mt-8">
                                <div class="col-lg-4">
                                    <label class="text-semi-14">Perkiraan lama perawatan</label>
                                    <b-input-group append="hari">
                                        <b-form-input v-model="form_inpatientAdult.discharge_treatment_duration" type="number" autocomplete="off" />
                                    </b-input-group>
                                </div>
                                <div class="col-12 mt-12">
                                    <label class="text-semi-14">Perawatan lanjutan yang dibutuhkan di rumah</label>
                                    <b-form-checkbox-group v-model="form_inpatientAdult.discharge_further_care_arry" size="sm">
                                        <b-form-checkbox value="a">Pemberian obat</b-form-checkbox>
                                        <b-form-checkbox value="b">Penggunaan teknologi / alat kesehatan</b-form-checkbox>
                                        <b-form-checkbox value="c">Tata laksana diet</b-form-checkbox>
                                        <b-form-checkbox value="d">Tata laksana nyeri</b-form-checkbox>
                                        <b-form-checkbox value="e">Teknik rehabilitasi</b-form-checkbox>
                                        <b-form-checkbox value="f">Perawatan luka</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </div>
                            </div>
                        </section>
                        <section class="mt-32">
                            <ModernLabel label="RENCANA PERAWATAN INTERDISIPLIN / REFFERAL" />
                            <div class="row mt-8">
                                <div class="col-lg-4">
                                    <label class="c-form-label">Diet dan Nutrisi</label>
                                    <b-form-radio-group v-model="form_inpatientAdult.care_plan_diet_nutrition_type_id" size="sm">
                                        <b-form-radio value="0">Tidak</b-form-radio>
                                        <b-form-radio value="1">Ya</b-form-radio>
                                    </b-form-radio-group>
                                    <b-form-input v-if="form_inpatientAdult.care_plan_diet_nutrition_type_id == 1" v-model="form_inpatientAdult.care_plan_diet_nutrition" class="mt-10" />
                                </div>
                                <div class="col-lg-4 mt-10 mt-lg-0">
                                    <label class="c-form-label">Rehabilitasi Medik</label>
                                    <b-form-radio-group v-model="form_inpatientAdult.care_plan_rehabilitation_type_id" size="sm">
                                        <b-form-radio value="0">Tidak</b-form-radio>
                                        <b-form-radio value="1">Ya</b-form-radio>
                                    </b-form-radio-group>
                                    <b-form-input v-if="form_inpatientAdult.care_plan_rehabilitation_type_id == 1" v-model="form_inpatientAdult.care_plan_rehabilitation" class="mt-10" />
                                </div>
                                <div class="col-lg-4 mt-10 mt-lg-0">
                                    <label class="c-form-label">Wound Care</label>
                                    <b-form-radio-group v-model="form_inpatientAdult.care_plan_wound_type_id" size="sm">
                                        <b-form-radio value="0">Tidak</b-form-radio>
                                        <b-form-radio value="1">Ya</b-form-radio>
                                    </b-form-radio-group>
                                    <b-form-input v-if="form_inpatientAdult.care_plan_wound_type_id == 1" v-model="form_inpatientAdult.care_plan_wound" class="mt-10" />
                                </div>
                                <div class="col-lg-4 mt-10">
                                    <label class="c-form-label">Pain Management</label>
                                    <b-form-radio-group v-model="form_inpatientAdult.care_plan_pain_management_type_id" size="sm">
                                        <b-form-radio value="0">Tidak</b-form-radio>
                                        <b-form-radio value="1">Ya</b-form-radio>
                                    </b-form-radio-group>
                                    <b-form-input v-if="form_inpatientAdult.care_plan_pain_management_type_id == 1" v-model="form_inpatientAdult.care_plan_pain_management" class="mt-10" />
                                </div>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Lain-lain</label>
                                <b-textarea v-model="form_inpatientAdult.care_plan_other" />                                    
                            </div>
                        </section>
                        <section class="mt-32">
                            <ModernLabel label="Assesment Keperawatan Anamnesa" />
                            <div class="mt-16">
                                <div class="card">
                                    <div class="card-body">
                                        <div>
                                            <label class="c-form-label">Alat kesehatan yang terpasang</label>
                                            <b-form-radio-group v-model='form_inpatientAdult.medical_devices_type_id' size="sm">
                                                <b-form-radio value="0">Tidak ada</b-form-radio>
                                                <b-form-radio value="1">Ya</b-form-radio>
                                            </b-form-radio-group>
                                        </div>
                                        <div v-if="form_inpatientAdult.medical_devices_type_id == 1" class="mt-16">
                                            <b-table fixed bordered :fields="ToolFields" :items="form_inpatientAdult.medical_devices" style="font-size: 13px">
                                                <template #cell(no)="row">{{ row.index+1 }}</template>
                                                <template #cell(action)="row">
                                                    <div class="d-flex">
                                                        <b-button variant="danger-700" size="sm" >
                                                            <feather-icon icon="TrashIcon" @click="removeMedical(row.index)" />
                                                        </b-button>
                                                    </div>
                                                </template>
                                            </b-table>
                                            <div class="mt-24">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <label class="c-form-label">Jenis</label>
                                                        <b-textarea v-model="input_medical.type" rows="2" />                                    
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="c-form-label">Keterangan</label>
                                                        <b-textarea v-model="input_medical.notes" rows="2" />                                    
                                                    </div>
                                                </div>
                                                <b-button @click="addMedical" size="sm" variant="primary" class="mt-12">Tambah</b-button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mt-16">
                                    <div class="card-body">
                                        <div>
                                            <label class="c-form-label">Riwayat pemeriksaan yang pernah dilakukan</label>
                                            <b-table responsive bordered :fields="CheckedFields" :items="form_inpatientAdult.examination_history" style="font-size: 13px">
                                                <template #cell(no)="row">{{ row.index+1 }}</template>
                                                <template #cell(action)="row">
                                                    <div class="d-flex">
                                                        <b-button variant="danger-700" size="sm" class="ml-2" >
                                                            <feather-icon icon="TrashIcon" @click="removeExamination(row.index)"/>
                                                        </b-button>
                                                    </div>
                                                </template>
                                            </b-table>
                                            <div class="mt-24">
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <label class="c-form-label">Jenis Pemeriksaan</label>
                                                        <b-textarea v-model="input_examination.type" rows="2" />                                    
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <label class="c-form-label">Lembar</label>
                                                        <b-textarea v-model="input_examination.page" rows="2" />                                    
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <label class="c-form-label">Keterangan</label>
                                                        <b-textarea v-model="input_examination.explanation" rows="2" />                                    
                                                    </div>
                                                </div>
                                                <b-button @click="addExamination" size="sm" variant="primary" class="mt-12">Tambah</b-button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <section class="mt-32">
                            <ModernLabel label="Penilaian Per Sistem" />
                            <div class="mt-16">
                                <!-- <section class="">
                                    <label class="text-uppercase text-bold-14" >Skala Nyeri: </label>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <label class="c-form-label">Apakah terdapat keluhan nyeri?</label>
                                            <b-form-radio-group v-model="form_inpatientAdult.sore_complaints_type_id" class="">
                                                <b-form-radio value="0">Tidak</b-form-radio>
                                                <b-form-radio value="1">Ya, dimana?</b-form-radio>
                                            </b-form-radio-group>
                                            <b-form-input v-model="form_inpatientAdult.sore_complaints" v-if="form_inpatientAdult.sore_complaints_type_id == '1'" class="mt-10" />
                                        </div>
                                        <div v-if="form_inpatientAdult.sore_complaints_type_id == '1'" class="col-lg-8">
                                            <label class="c-form-label">Nyeri Hilang Bila</label>
                                            <b-form-checkbox-group v-model="form_inpatientAdult.sore_goes_away_arry" size="sm" class="">
                                                <b-form-checkbox value="a" class="my-1">Minum Obat</b-form-checkbox>
                                                <b-form-checkbox value="b" class="my-1">Mendengar Musik</b-form-checkbox>
                                                <b-form-checkbox value="c" class="my-1">Istirahat</b-form-checkbox>
                                                <b-form-checkbox value="d" class="my-1">Berubah Posisi Tidur</b-form-checkbox>
                                                <b-form-checkbox value="e" class="my-1">Lain-lain</b-form-checkbox>
                                            </b-form-checkbox-group>
                                            <b-form-input v-model="form_inpatientAdult.sore_goes_away" v-if="form_inpatientAdult.sore_complaints_type_id == '1' && form_inpatientAdult.sore_goes_away_arry.indexOf('e') !== -1" />
                                        </div>
                                    </div>
                                    <div v-if="form_inpatientAdult.sore_complaints_type_id == '1'" class="mt-12">
                                        <label class="text-semi-14">Metoda</label>
                                        <b-form-radio-group v-model="form_inpatientAdult.sore_method_type_id" size="sm">
                                            <b-form-radio value="1">NIPS</b-form-radio>
                                            <b-form-radio value="2">FLACC</b-form-radio>
                                            <b-form-radio value="3">VAS</b-form-radio>
                                            <b-form-radio value="4">BPS</b-form-radio>
                                            <b-form-radio value="5">Wong-Baker</b-form-radio>
                                            <b-form-radio value="6">NRS</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                    <div v-if="form_inpatientAdult.sore_method_type_id == '3'" class="mt-20">
                                        <b-table-simple fixed responsive small head-variant="light" class="mt-12">
                                            <b-thead class="text-bold-14">
                                                <b-tr>
                                                    <b-th>-</b-th>
                                                    <b-th>0</b-th>
                                                    <b-th>1</b-th>
                                                    <b-th>2</b-th>
                                                    <b-th>Total</b-th>
                                                </b-tr>
                                            </b-thead>
                                            <b-tbody class="text-regular-12">
                                                <b-tr>
                                                    <b-td>FACE</b-td>
                                                    <b-td>Tidak ekspresisi atau dalam keadaan tersenyum</b-td>
                                                    <b-td>Menyeringai, mengerutkan dahi, tampak tidak tertarik</b-td>
                                                    <b-td>Dagi gemetar, gigi gemeretak, rahang terkancing</b-td>
                                                    <b-td><b-form-input type="number" v-model="form_inpatientAdult.face_score" /></b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>LEGS</b-td>
                                                    <b-td>Normal, rileks</b-td>
                                                    <b-td>Gelisah, tegang, resah</b-td>
                                                    <b-td>Menendang, kaki tertekuk atau membengkokan kaki</b-td>
                                                    <b-td><b-form-input type="number" v-model="form_inpatientAdult.legs_score" /></b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>ACTIVITY</b-td>
                                                    <b-td>Berbaring terlentang, posisi normal, gerakan mudah</b-td>
                                                    <b-td>Menggeliat, tidak bisa diam, tegang</b-td>
                                                    <b-td>Kaku, kejang</b-td>
                                                    <b-td><b-form-input type="number" v-model="form_inpatientAdult.activity_score" /></b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>CRY</b-td>
                                                    <b-td>Tidak menangis</b-td>
                                                    <b-td>Merintih, merengek</b-td>
                                                    <b-td>Terus menangis, berteriak, sering mengeluh</b-td>
                                                    <b-td><b-form-input type="number" v-model="form_inpatientAdult.cry_score" /></b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>CONSOLABILITY</b-td>
                                                    <b-td>Rileks</b-td>
                                                    <b-td>Kadang mengeluh, dapat ditenangkan dengan sentuhan, pelukan, bujukan, dapat dialihkan</b-td>
                                                    <b-td>-</b-td>
                                                    <b-td><b-form-input type="number" v-model="form_inpatientAdult.consolability_score" /></b-td>
                                                </b-tr>
                                            </b-tbody>
                                        </b-table-simple>
                                    </div>
                                    <div v-if="form_inpatientAdult.sore_method_type_id == '5'" class="mt-20">
                                        <b-form-radio-group v-model="form_inpatientAdult.wong_baker_score" @change="handleSoreMethodScore">
                                            <b-form-radio value="0" class="col-2 mr-0">
                                                <div class="text-center">
                                                    <img src="@/@core/assets/images/nurse/wong_baker/0.png" width="50%">
                                                    <div class="c-form-label mt-10">
                                                        <div>0</div>
                                                        <div>Tidak Nyeri</div>
                                                    </div>
                                                </div>
                                            </b-form-radio>
                                            <b-form-radio value="2" class="col-2 mr-0">
                                                <div class="text-center">
                                                    <img src="@/@core/assets/images/nurse/wong_baker/2.png" width="50%">
                                                    <div class="c-form-label mt-10">
                                                        <div>2</div>
                                                        <div>Sedikit Nyeri</div>
                                                    </div>
                                                </div>
                                            </b-form-radio>
                                            <b-form-radio value="4" class="col-2 mr-0">
                                                <div class="text-center">
                                                    <img src="@/@core/assets/images/nurse/wong_baker/4.png" width="50%">
                                                    <div class="c-form-label mt-10">
                                                        <div>4</div>
                                                        <div>Sedikit Lebih Nyeri</div>
                                                    </div>
                                                </div>
                                            </b-form-radio>
                                            <b-form-radio value="6" class="col-2 mr-0">
                                                <div class="text-center">
                                                    <img src="@/@core/assets/images/nurse/wong_baker/6.png" width="50%">
                                                    <div class="c-form-label mt-10">
                                                        <div>6</div>
                                                        <div>Lebih Nyeri</div>
                                                    </div>
                                                </div>
                                            </b-form-radio>
                                            <b-form-radio value="8" class="col-2 mr-0">
                                                <div class="text-center">
                                                    <img src="@/@core/assets/images/nurse/wong_baker/8.png" width="50%">
                                                    <div class="c-form-label mt-10">
                                                        <div>8</div>
                                                        <div>Sangat Nyeri</div>
                                                    </div>
                                                </div>
                                            </b-form-radio>
                                            <b-form-radio value="10" class="col-2 mr-0">
                                                <div class="text-center">
                                                    <img src="@/@core/assets/images/nurse/wong_baker/10.png" width="50%">
                                                    <div class="c-form-label mt-10">
                                                        <div>10</div>
                                                        <div>Nyeri Sangat Hebat</div>
                                                    </div>
                                                </div>
                                            </b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                    <div v-if="form_inpatientAdult.sore_method_type_id == '6'" class="mt-20 position-relative">
                                        <div class="timeline-wrapper p-16 rounded">
                                            <b-form-radio-group v-model="form_inpatientAdult.nrs_score" @change="handleSoreMethodScore" class="w-100">
                                                <div class="d-flex justify-content text-center">
                                                    <div class="col-2">
                                                        <div class="text-bold-12" style="height:40px">Tidak Nyeri</div>
                                                        <b-form-radio value="0" class="col-1 d-inline-block mr-0"><div class="w-100">0</div></b-form-radio>
                                                    </div>
                                                    <div class="col devider">
                                                        <div class="text-bold-12" style="height:40px">Nyeri Ringan</div>
                                                        <div class="d-flex justify-content-between">
                                                            <b-form-radio value="1" class="col d-inline-block mr-0">1</b-form-radio>
                                                            <b-form-radio value="2" class="col d-inline-block mr-0">2</b-form-radio>
                                                            <b-form-radio value="3" class="col d-inline-block mr-0">3</b-form-radio>
                                                        </div>
                                                    </div>
                                                    <div class="col devider">
                                                        <div class="text-bold-12" style="height:40px">Nyeri Sedang</div>
                                                        <div class="d-flex justify-content-between">
                                                            <b-form-radio value="4" class="col d-inline-block mr-0">4</b-form-radio>
                                                            <b-form-radio value="5" class="col d-inline-block mr-0">5</b-form-radio>
                                                            <b-form-radio value="6" class="col d-inline-block mr-0">6</b-form-radio>
                                                        </div>
                                                    </div>
                                                    <div class="col devider">
                                                        <div class="text-bold-12" style="height:40px">Nyeri Terkontrol</div>
                                                        <div class="d-flex justify-content-between">
                                                            <b-form-radio value="7" class="col d-inline-block mr-0">7</b-form-radio>
                                                            <b-form-radio value="8" class="col d-inline-block mr-0">8</b-form-radio>
                                                            <b-form-radio value="9" class="col d-inline-block mr-0">9</b-form-radio>
                                                        </div>
                                                    </div>
                                                    <div class="col-2">
                                                        <div class="text-bold-12" style="height:40px">Nyeri Berat Tidak Terkontrol</div>
                                                        <b-form-radio value="10" class="col-2 d-inline-block mr-0">10</b-form-radio>
                                                    </div>
                                                </div>
                                            </b-form-radio-group>
                                        </div>
                                        <div class="row mt-16">
                                            <div class="col-lg-4">
                                                <b-form-checkbox-group v-model="form_inpatientAdult.next_nrs_score" size="sm">
                                                    <b-form-checkbox value="a">Nyeri Kronis</b-form-checkbox>
                                                    <b-form-checkbox value="b">Nyeri Akut</b-form-checkbox>
                                                </b-form-checkbox-group>
                                            </div>
                                            <div class="col-lg-4 mt-12 mt-lg-0">
                                                <label class="c-form-label">Diberitahukan ke dokter</label>
                                                <b-form-radio-group v-model="form_inpatientAdult.tell_doctor_type_id" size="sm">
                                                    <b-form-radio value="0">Tidak</b-form-radio>
                                                    <b-form-radio value="1">Ya, Pukul</b-form-radio>
                                                </b-form-radio-group>
                                                <b-form-input v-if="form_inpatientAdult.tell_doctor_type_id == 1" v-model="form_inpatientAdult.tell_doctor_date" type="datetime-local" class="mt-10"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-20">
                                        <div class="col-lg-4">
                                            <label class="c-form-label">Skor</label>
                                            <b-input-group prepend="Skor">
                                                <b-form-input v-model="form_inpatientAdult.sore_total_score" type="number" />
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-4 mt-10 mt-lg-0">
                                            <label class="c-form-label">Durasi</label>
                                            <b-form-input v-model="form_inpatientAdult.duration" type="number" />
                                        </div>
                                        <div class="col-lg-4 mt-10 mt-lg-0">
                                            <label class="c-form-label">Lokasi</label>
                                            <b-form-input v-model="form_inpatientAdult.location" />
                                        </div>
                                        <div class="col-lg-4 mt-10">
                                            <label class="c-form-label">Frekuensi Nyeri</label>
                                            <b-input-group append="x/hari">
                                                <b-form-input v-model="form_inpatientAdult.frequency" type="number" />
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-4 mt-10">
                                            <label class="c-form-label">Kualitas Nyeri</label>
                                            <b-form-checkbox-group v-model="form_inpatientAdult.sore_quality_arry" size="sm">
                                                <b-form-checkbox value="a">Tertusuk</b-form-checkbox>
                                                <b-form-checkbox value="b">Terbakar</b-form-checkbox>
                                                <b-form-checkbox value="c">Tertekan</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </div>
                                        <div class="col-lg-4 mt-10">
                                            <label class="c-form-label">Nyeri Mempengaruhi</label>
                                            <b-form-checkbox-group v-model="form_inpatientAdult.sore_affects_arry" size="sm" stacked>
                                                <b-form-checkbox value="a">Nyeri Mempengaruhi</b-form-checkbox>
                                                <b-form-checkbox value="b">Tidur</b-form-checkbox>
                                                <b-form-checkbox value="c">Aktifitas Fisik</b-form-checkbox>
                                                <b-form-checkbox value="d">Emosi</b-form-checkbox>
                                                <b-form-checkbox value="e">Nafsu Makan</b-form-checkbox>
                                                <b-form-checkbox value="f">Lain-lain</b-form-checkbox>
                                            </b-form-checkbox-group>
                                            <b-form-input v-if="form_inpatientAdult.sore_affects_arry.indexOf('f') !== -1" v-model="form_inpatientAdult.sore_affects" class="mt-10"/>
                                        </div>
                                    </div>
                                </section> -->
                                <div class="mt-16">
                                    <label class="text-uppercase text-bold-14">Riwayat merokok: </label>
                                    <div class="row">
                                        <b-form-radio-group v-model="form_inpatientAdult.evaluation_smoke_type_id" class="col-4 mt-8">
                                            <b-form-radio value="0">Tidak</b-form-radio>
                                            <b-form-radio value="1">Ya</b-form-radio>
                                        </b-form-radio-group>
                                        <template v-if="form_inpatientAdult.evaluation_smoke_type_id == 1">
                                            <div class="col-4">
                                                <label class="c-form-label">Jumlah per hari</label>
                                                <b-input-group append="batang">
                                                    <b-form-input v-model="form_inpatientAdult.evaluation_smoke_amount" type="number" autocomplete="off" />
                                                </b-input-group>
                                            </div>
                                            <div class="col-4">
                                                <label class="c-form-label">Lamanya</label>
                                                <b-form-input v-model="form_inpatientAdult.evaluation_smoke_long" autocomplete="off" />
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div class="mt-16">
                                    <label class="text-uppercase text-bold-14">Riwayat minum minuman keras: </label>
                                    <div class="row">
                                        <b-form-radio-group v-model="form_inpatientAdult.evaluation_alcohol_type_id" class="col-4 mt-8">
                                            <b-form-radio value="0">Tidak</b-form-radio>
                                            <b-form-radio value="1">Ya</b-form-radio>
                                        </b-form-radio-group>
                                        <template v-if="form_inpatientAdult.evaluation_alcohol_type_id == 1">
                                            <div class="col-4">
                                                <label class="c-form-label">Jenis</label>
                                                <b-form-input v-model="form_inpatientAdult.evaluation_alcohol_type" autocomplete="off" />
                                            </div>
                                            <div class="col-4">
                                                <label class="c-form-label">Jumlah per hari</label>
                                                <b-form-input v-model="form_inpatientAdult.evaluation_alcohol_amount" autocomplete="off" />
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div class="mt-16">
                                    <label class="text-uppercase text-bold-14">Riwayat penggunaan obat penenang: </label>
                                    <div class="row">
                                        <b-form-radio-group v-model="form_inpatientAdult.evaluation_sedative_type_id" class="col-4 mt-8">
                                            <b-form-radio value="0">Tidak</b-form-radio>
                                            <b-form-radio value="1">Ya</b-form-radio>
                                        </b-form-radio-group>
                                        <template v-if="form_inpatientAdult.evaluation_sedative_type_id == 1">
                                            <div class="col-4">
                                                <label class="c-form-label">Jenis</label>
                                                <b-form-input v-model="form_inpatientAdult.evaluation_sedative_type" autocomplete="off" />
                                            </div>
                                            <div class="col-4">
                                                <label class="c-form-label">Jumlah per hari</label>
                                                <b-form-input v-model="form_inpatientAdult.evaluation_sedative_amount" autocomplete="off" />
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div class="mt-16">
                                    <label class="text-uppercase text-bold-14">Gangguan pola istirahat / tidur: </label>
                                    <div class="row">
                                        <b-form-radio-group v-model="form_inpatientAdult.evaluation_sleep_type_id" class="col-lg-4 mt-8">
                                            <b-form-radio value="0">Tidak</b-form-radio>
                                            <b-form-radio value="1">Ya</b-form-radio>
                                        </b-form-radio-group>
                                        <template v-if="form_inpatientAdult.evaluation_sleep_type_id == 1">
                                            <div class="col-lg-4">
                                                <b-form-input v-model="form_inpatientAdult.evaluation_sleep" autocomplete="off"/>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div class="mt-20">
                                    <label class="text-uppercase text-bold-14" >Asesmen fungsi: </label>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <label class="">A. kemampuan melakukan aktivitas sehari-hari</label>
                                            <b-form-radio-group v-model="form_inpatientAdult.evaluation_daily_type_id" stacked>
                                                <b-form-radio value="1">Mandiri</b-form-radio>
                                                <b-form-radio value="2">Bantuan Minimal</b-form-radio>
                                                <b-form-radio value="3">Bantuan Sebagian</b-form-radio>
                                                <b-form-radio value="4">Ketergantungan Total</b-form-radio>
                                            </b-form-radio-group>
                                        </div>
                                        <div class="col-lg-4 mt-12 mt-lg-0">
                                            <label class="">B. Aktivitas</label>
                                            <b-form-radio-group v-model="form_inpatientAdult.evaluation_activity_type_id" stacked>
                                                <b-form-radio value="1">Tirah Baring</b-form-radio>
                                                <b-form-radio value="2">Duduk</b-form-radio>
                                                <b-form-radio value="3">Berjalan</b-form-radio>
                                            </b-form-radio-group>
                                        </div>
                                        <div class="col-lg-4 mt-12 mt-lg-0">
                                            <label class="">C. Ekstremitas Atas</label>
                                            <b-form-radio-group v-model="form_inpatientAdult.evaluation_extremities_top_type_id" stacked>
                                                <b-form-radio value="1">Tidak ada kelainan</b-form-radio>
                                                <b-form-radio value="2">Lemah</b-form-radio>
                                            </b-form-radio-group>
                                        </div>
                                        <div class="col-lg-4 mt-12">
                                            <label class="">D. Kemampuan Menggenggam</label>
                                            <b-form-radio-group v-model="form_inpatientAdult.evaluation_grip_type_id" stacked>
                                                <b-form-radio value="1">Tidak ada kesulitan</b-form-radio>
                                                <b-form-radio value="2">Terkilir</b-form-radio>
                                                <b-form-radio value="3">
                                                    <div class="d-flex">
                                                        <div class="mr-1">Lainnya</div>
                                                        <div v-if="form_inpatientAdult.evaluation_grip_type_id == 3">
                                                            <b-form-input v-model="form_inpatientAdult.evaluation_grip_other" />
                                                        </div>
                                                    </div>
                                                </b-form-radio>
                                            </b-form-radio-group>
                                        </div>
                                        <div class="col-lg-4 mt-12">
                                            <label class="">E. Kemampuan koordinasi</label>
                                            <b-form-radio-group v-model="form_inpatientAdult.evaluation_coordination_type_id" stacked>
                                                <b-form-radio value="1">Tidak ada kesulitan</b-form-radio>
                                                <b-form-radio value="2">
                                                    <div class="d-flex">
                                                        <div class="mr-1">Ada Masalah</div>
                                                        <div v-if="form_inpatientAdult.evaluation_coordination_type_id == 2">
                                                            <b-form-input v-model="form_inpatientAdult.evaluation_coordination_other" />
                                                        </div>
                                                    </div>
                                                </b-form-radio>
                                            </b-form-radio-group>
                                        </div>
                                        <div class="col-lg-4 mt-12">
                                            <label class="">F. Alat Ambulasi</label>
                                            <b-form-radio-group v-model="form_inpatientAdult.evaluation_ambulation_type_id" stacked>
                                                <b-form-radio value="1">Walker</b-form-radio>
                                                <b-form-radio value="2">Tongkat</b-form-radio>
                                                <b-form-radio value="3">Kursi Roda</b-form-radio>
                                            </b-form-radio-group>
                                        </div>
                                        <div class="col-lg-4 mt-12">
                                            <label class="">G. Ekstremitas Bawah</label>
                                            <b-form-radio-group v-model="form_inpatientAdult.evaluation_extremities_bottom_type_id" stacked>
                                                <b-form-radio value="1">Tidak ada kesulitan</b-form-radio>
                                                <b-form-radio value="2">Penurunan kekuatan / Range of Motion</b-form-radio>
                                                <b-form-radio value="3">Paralisis</b-form-radio>
                                                <b-form-radio value="4">Sering jatuh</b-form-radio>
                                                <b-form-radio value="5">Hilang keseimbangan</b-form-radio>
                                                <b-form-radio value="6">Deformitas</b-form-radio>
                                                <b-form-radio value="7">
                                                    <div class="mr-1">Riwayat patah tulang</div>
                                                    <div v-if="form_inpatientAdult.evaluation_extremities_bottom_type_id == 7">
                                                        <b-form-input v-model="form_inpatientAdult.evaluation_extremities_bottom_bone" />
                                                    </div>
                                                </b-form-radio>
                                            </b-form-radio-group>
                                        </div>
                                    </div>                                    
                                </div>
                                <section class="mt-16">
                                    <label class="text-uppercase text-bold-14" >Keadaan Mental dan Psikologis: </label>
                                    <div class="row">
                                        <div class="col-lg-6">                                            
                                            <label class="text-semi-14">Status Psikologis</label>
                                            <b-form-checkbox-group v-model="form_inpatientAdult.psychological_status_arry" size="sm">
                                                <b-form-checkbox value="a">Cemas</b-form-checkbox>
                                                <b-form-checkbox value="b">Takut</b-form-checkbox>
                                                <b-form-checkbox value="c">Marah</b-form-checkbox>
                                                <b-form-checkbox value="d">Sedih</b-form-checkbox>
                                                <b-form-checkbox value="e">Cenderung ingin bunuh diri</b-form-checkbox>
                                                <b-form-checkbox value="f">Lain-lain</b-form-checkbox>
                                            </b-form-checkbox-group>
                                            <b-form-input v-model="form_inpatientAdult.psychological_status" v-if="form_inpatientAdult.psychological_status_arry.indexOf('f') !== -1" class="mt-10 col-lg-8"  />
                                        </div>
                                        <div class="col-lg-6 mt-32 mt-lg-0">
                                            <label class="text-semi-14">Status Mental</label>
                                            <b-form-checkbox-group v-model="form_inpatientAdult.mental_status_arry" stacked size="sm">
                                                <b-form-checkbox value="a" class="my-1">Sadar dan orientasi baik</b-form-checkbox>
                                                <b-form-checkbox value="b" class="my-1 my-lg-auto">Ada masalah perilaku, sebutkan</b-form-checkbox>
                                                <b-form-input v-model="form_inpatientAdult.mental_status_problems" v-if="form_inpatientAdult.mental_status_arry.indexOf('b') !== -1" class="mt-10 col-lg" />
                                                <b-form-checkbox value="c" class="my-1">Perilaku kekerasan yang dialami pasien sebelumnya</b-form-checkbox>
                                                <b-form-input v-model="form_inpatientAdult.mental_status_violence" v-if="form_inpatientAdult.mental_status_arry.indexOf('c') !== -1" class="mt-10 col-lg" />
                                            </b-form-checkbox-group>
                                        </div>
                                    </div>
                                </section>
                                <section class="mt-16">
                                    <label class="text-uppercase text-bold-14" >Eliminasi: </label>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <label class="text-semi-14">Frekuensi BAB</label>
                                            <b-input-group append="x/hari" class="">
                                                <b-form-input v-model="form_inpatientAdult.elimination_defecate" type="number" ></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="col mt-12 mt-lg-0">
                                            <label class="text-semi-14">Konsistensi</label>
                                            <b-form-radio-group v-model='form_inpatientAdult.elimination_consistency_type_id' size="sm" >
                                                <b-form-radio value="1">Lunak</b-form-radio>
                                                <b-form-radio value="2">Keras</b-form-radio>
                                                <b-form-radio value="3">Cair</b-form-radio>
                                                <b-form-radio value="4">Ampas</b-form-radio>
                                                <b-form-radio value="5">Tidak</b-form-radio>
                                            </b-form-radio-group>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4 mt-12">
                                            <label class="text-semi-14">Frekuensi BAK</label>
                                            <b-input-group append="x/hari">
                                                <b-form-input v-model="form_inpatientAdult.elimination_urination" type="number" ></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="col mt-12">
                                            <label class="text-semi-14">Warna</label>
                                            <b-form-radio-group v-model="form_inpatientAdult.elimination_color_type_id" size="sm">
                                                <b-form-radio value="1">Kuning Jernih</b-form-radio>
                                                <b-form-radio value="2">Kuning Keruh</b-form-radio>
                                                <b-form-radio value="3">Lainnya</b-form-radio>
                                            </b-form-radio-group>
                                            <div class="col mt-10">
                                                <b-form-input v-model="form_inpatientAdult.elimination_color" v-if="form_inpatientAdult.elimination_color_type_id == '3'" />
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </section>
                    </div>
                </div>
            </section>

            <template v-if="dummy_condition == true">

                <!-- pengkajian awal neonatus -->
                <section v-if="this.view_condition.health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f' && view_condition.isNeonatus == true" class="mt-20 mt-lg-32">
                    <div class="card">
                        <div class="card-header text-white font-weight-bold bg-primary">PENGKAJIAN AWAL RAWAT INAP NEONATUS</div>
                        <div class="card-body">
                            <!-- riwayat kesehatan bayi ibu-->
                            <div class="col px-0">
                                <ModernLabel label="Riwayat Kesehatan dahulu bayi dan ibu" class="mb-8" />
                                <div class="row mt-16 c-form">
                                    <div class="col-lg-4">
                                        <div class="mt-1">
                                            <label>Hasil Laboratorium Ibu</label>
                                            <v-select @change="saveChangedNurse($event)"
                                                v-model="OptionsNeonatusHasilLabIbuSelect"
                                                aria-readonly="true"
                                                :options="OptionsNeonatusHasilLabIbu"
                                                :reduce="OptionsNeonatusHasilLabIbu => OptionsNeonatusHasilLabIbu.value"
                                                label="title"
                                                placeholder="-- Hasil Lab Ibu --"></v-select>
                                            <b-form-input
                                                v-if="OptionsNeonatusHasilLabIbuSelect === 4"
                                                type="text" autocomplete="off"
                                                @change="saveChangedNurse($event)"
                                                v-model="form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.hasil_laboratorium_ibu"></b-form-input>
                                        </div>
                                        <div class="mt-1">
                                            <label>Golongan Darah Ibu</label>
                                            <v-select @change="saveChangedNurse($event)"
                                                v-model="form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.golongan_darah_ibu"
                                                aria-readonly="true"
                                                :options="OptionsNeonatusGolonganDarahIbu"
                                                :reduce="OptionsNeonatusGolonganDarahIbu => OptionsNeonatusGolonganDarahIbu.value"
                                                label="title"
                                                placeholder="-- Pilih Golongan Darah --"></v-select>
                                        </div>
                                        <div class="mt-1">
                                            <label>Riwayat Persalinan</label>
                                            <v-select @change="saveChangedNurse($event)"
                                                v-model="form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.riwayat_persalinan"
                                                aria-readonly="true"
                                                :options="OptionsNeonatusRiwayatPersalinan"
                                                :reduce="OptionsNeonatusRiwayatPersalinan => OptionsNeonatusRiwayatPersalinan.value"
                                                label="title"
                                                placeholder="-- Riwayat Persalinan --"></v-select>
                                        </div>

                                    </div>
                                    <div class="col-lg-4 border border-top-0 border-bottom-0">
                                        <div class="mt-1">
                                            <label>Riwayat Kelahiran</label>
                                            <v-select @change="saveChangedNurse($event)"
                                                v-model="form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.riwayat_kelahiran"
                                                aria-readonly="true"
                                                :options="OptionsNeonatusRiwayatKelahiran"
                                                :reduce="OptionsNeonatusRiwayatKelahiran => OptionsNeonatusRiwayatKelahiran.value"
                                                label="title"
                                                placeholder="-- Riwayat Kelahiran --"></v-select>
                                        </div>
                                        <div class="mt-1">
                                            <label>Jenis Kelamin Bayi</label>
                                            <v-select @change="saveChangedNurse($event)"
                                                v-model="form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.jenis_kelamin_bayi"
                                                aria-readonly="true"
                                                :options="OptionsNeonatusJenisKelaminBayi"
                                                :reduce="OptionsNeonatusJenisKelaminBayi => OptionsNeonatusJenisKelaminBayi.value"
                                                label="title"
                                                placeholder="-- Jenis Kelamin Bayi --"></v-select>
                                        </div>
                                        <div class="mt-1">
                                            <label>Pemberian Obat Setelah Lahir</label>
                                            <v-select @change="saveChangedNurse($event)"
                                                v-model="OptionsNeonatusPemberianObatSetelahLahirSelect"
                                                aria-readonly="true"
                                                :options="OptionsNeonatusPemberianObatSetelahLahir"
                                                :reduce="OptionsNeonatusPemberianObatSetelahLahir => OptionsNeonatusPemberianObatSetelahLahir.value"
                                                label="title"
                                                placeholder="-- Pemberian Obat Setelah Lahir --"></v-select>
                                            <b-form-input
                                                v-if="OptionsNeonatusPemberianObatSetelahLahirSelect === 3"
                                                type="text" autocomplete="off"
                                                @change="saveChangedNurse($event)"
                                                v-model="form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.pemberian_obat_setelah_lahir"></b-form-input>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mt-1">
                                            <label>Riwayat Penyakit Ibu Setelah Hamil</label>
                                            <b-input-group>
                                                <b-form-input type="text" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.riwayat_penyakit_ibu_setelah_hamil"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-1">
                                            <label>Diagnosa Partus</label>
                                            <b-input-group>
                                                <b-form-input type="text" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.diagnosa_partus"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-1">
                                            <label>Riwayat Kehamilan</label>
                                            <v-select @change="saveChangedNurse($event)"
                                                v-model="OptionRiwayatKehamilanSelect"
                                                aria-readonly="true"
                                                :options="OptionRiwayatKehamilan"
                                                :reduce="OptionRiwayatKehamilan => OptionRiwayatKehamilan.value"
                                                label="title"
                                                placeholder="-- Riwayat Kehamilan --"></v-select>
                                            <b-input-group
                                                v-if="this.OptionRiwayatKehamilanSelect === 1 || this.OptionRiwayatKehamilanSelect === 2">
                                                <b-form-input type="text" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.riwayat_kehamilan"
                                                    :placeholder="this.OptionRiwayatKehamilanSelect === 1 ? 'Isi usia gestasi...' : 'Isi usia koreksi...'"></b-form-input>
                                            </b-input-group>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- kebutuhan edukasi orang tua -->
                            <div class="col mt-32 px-0">
                                <ModernLabel label="Kebutuhan Edukasi Orang tua" class="mb-8" />
                                <div class="row">
                                    <div class="col-lg-4">
                                        <v-select @change="saveChangedNurse($event)"
                                            v-model="form_inpatientNeonatus.neonatus_kebutuhan_edukasi_orang_tua.bicara"
                                            aria-readonly="true"
                                            :options="OptionsNeonatusKebutuhanEdukasiOrangTuaBicara"
                                            :reduce="OptionsNeonatusKebutuhanEdukasiOrangTuaBicara => OptionsNeonatusKebutuhanEdukasiOrangTuaBicara.value"
                                            label="title"
                                            placeholder="-- Bicara --"></v-select>
                                    </div>
                                    <div class="col-lg-4 mt-12 mt-lg-0">
                                        <v-select @change="saveChangedNurse($event)"
                                            v-model="form_inpatientNeonatus.neonatus_kebutuhan_edukasi_orang_tua.bahasa_sehari_hari"
                                            aria-readonly="true"
                                            :options="OptionsNeonatusKebutuhanEdukasiOrangTuaBahasaSehariHari"
                                            :reduce="OptionsNeonatusKebutuhanEdukasiOrangTuaBahasaSehariHari => OptionsNeonatusKebutuhanEdukasiOrangTuaBahasaSehariHari.value"
                                            label="title"
                                            placeholder="-- Bahasa Sehari-Hari --"></v-select>
                                    </div>
                                    <div class="col-lg-4 mt-12 mt-lg-0">
                                        <v-select @change="saveChangedNurse($event)"
                                            v-model="form_inpatientNeonatus.neonatus_kebutuhan_edukasi_orang_tua.perlu_penerjemah"
                                            aria-readonly="true"
                                            :options="OptionsNeonatusKebutuhanEdukasiOrangTuaBahasaPerluPenerjemah"
                                            :reduce="OptionsNeonatusKebutuhanEdukasiOrangTuaBahasaPerluPenerjemah => OptionsNeonatusKebutuhanEdukasiOrangTuaBahasaPerluPenerjemah.value"
                                            label="title"
                                            placeholder="-- Perlu Penerjemah --"></v-select>
                                    </div>
                                    <div class="col-lg-4 mt-12">
                                        <v-select @change="saveChangedNurse($event)"
                                            v-model="form_inpatientNeonatus.neonatus_kebutuhan_edukasi_orang_tua.tingkat_pendidikan"
                                            aria-readonly="true"
                                            :options="OptionsNeonatusKebutuhanEdukasiOrangTuaBahasaTingkatPendidikan"
                                            :reduce="OptionsNeonatusKebutuhanEdukasiOrangTuaBahasaTingkatPendidikan => OptionsNeonatusKebutuhanEdukasiOrangTuaBahasaTingkatPendidikan.value"
                                            label="title"
                                            placeholder="-- Tingkat Pendidikan --"></v-select>
                                    </div> 
                                    <div class="col-12 mt-12">
                                        <ModernLabel
                                            label="Keluarga Menginginkan Informasi Tentang Proses Penyakit" />
                                        <div class="mt-8 mt-lg-20">
                                            <b-form-textarea rows="3"
                                                @change="saveChangedNurse($event)"
                                                v-model="form_inpatientNeonatus.neonatus_kebutuhan_edukasi_orang_tua.keluarga_menginginkan_informasi_tentang_proses_penyakit"
                                                autocomplete="off"></b-form-textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- spiritual -->
                            <div class="col mt-32 px-0">
                                <ModernLabel label="Spiritual" class="mb-8" />
                                <div class="row">
                                    <div class="col-lg-4">
                                        <v-select @change="saveChangedNurse($event)"
                                            v-model="form_inpatientNeonatus.neonatus_spiritual.agama"
                                            aria-readonly="true"
                                            :options="OptionsNeonatusSpiritualAgama"
                                            :reduce="OptionsNeonatusSpiritualAgama => OptionsNeonatusSpiritualAgama.value"
                                            label="title"
                                            placeholder="-- Agama --"></v-select>
                                    </div>
                                    <div class="col-12 mt-12">
                                        <ModernLabel
                                            label="Permintaan khusus terkait dengan adat/keyakinan/kepercayaan" />
                                        <div class="mt-8 mt-lg-20">
                                            <b-form-textarea rows="3"
                                                @change="saveChangedNurse($event)"
                                                v-model="form_inpatientNeonatus.neonatus_spiritual.permintaan_khusus_terkait_dengan_adat"
                                                autocomplete="off"></b-form-textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- sosial budaya-->
                            <div class="col mt-32 px-0">
                                <ModernLabel label="Sosial Budaya" class="mb-8" />
                                <div class="row mt-16 c-form">
                                    <div class="col-lg-4">
                                        <div class="mt-1">
                                            <label>Suku Bangsa</label>
                                            <b-input-group>
                                                <b-form-input type="text" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientNeonatus.neonatus_sosial_budaya.suku_bangsa"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-1">
                                            <label>Warga Negara</label>
                                            <b-input-group>
                                                <b-form-input type="text" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientNeonatus.neonatus_sosial_budaya.warga_negara"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-1">
                                            <label>Tempat Tinggal</label>
                                            <v-select @change="saveChangedNurse($event)"
                                                v-model="form_inpatientNeonatus.neonatus_sosial_budaya.tempat_tinggal"
                                                aria-readonly="true"
                                                :options="OptionsNeonatusTempatTinggalBayi"
                                                :reduce="OptionsNeonatusTempatTinggalBayi => OptionsNeonatusTempatTinggalBayi.value"
                                                label="title"
                                                placeholder="-- Tempat Tinggal --"></v-select>
                                        </div>
                                        <div class="mt-1">
                                            <label>Yang Merawat Bayi</label>
                                            <v-select @change="saveChangedNurse($event)"
                                                v-model="form_inpatientNeonatus.neonatus_sosial_budaya.yang_merawat_bayi"
                                                aria-readonly="true"
                                                :options="OptionsNeonatusYangMerawatBayi"
                                                :reduce="OptionsNeonatusYangMerawatBayi => OptionsNeonatusYangMerawatBayi.value"
                                                label="title"
                                                placeholder="-- Yang Merawat Bayi --"></v-select>
                                        </div>

                                    </div>
                                    <div class="col-lg-4 border border-top-0 border-bottom-0">
                                        <div class="mt-1">
                                            <label>Orang Tua</label>
                                            <b-input-group>
                                                <b-form-input type="text" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientNeonatus.neonatus_sosial_budaya.orang_tua"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-1">
                                            <label>Berkunjung</label>
                                            <v-select @change="saveChangedNurse($event)"
                                                v-model="form_inpatientNeonatus.neonatus_sosial_budaya.berkunjung"
                                                aria-readonly="true"
                                                :options="OptionsNeonatusSosialBudayaTwoOption"
                                                :reduce="OptionsNeonatusSosialBudayaTwoOption => OptionsNeonatusSosialBudayaTwoOption.value"
                                                label="title"
                                                placeholder="-- Berkunjung --"></v-select>
                                        </div>
                                        <div class="mt-1">
                                            <label>Menyentuh</label>
                                            <v-select @change="saveChangedNurse($event)"
                                                v-model="form_inpatientNeonatus.neonatus_sosial_budaya.menyentuh"
                                                aria-readonly="true"
                                                :options="OptionsNeonatusSosialBudayaTwoOption"
                                                :reduce="OptionsNeonatusSosialBudayaTwoOption => OptionsNeonatusSosialBudayaTwoOption.value"
                                                label="title"
                                                placeholder="-- Menyentuh --"></v-select>
                                        </div>
                                        <div class="mt-1">
                                            <label>Menggendong</label>
                                            <v-select @change="saveChangedNurse($event)"
                                                v-model="form_inpatientNeonatus.neonatus_sosial_budaya.menggendong"
                                                aria-readonly="true"
                                                :options="OptionsNeonatusSosialBudayaTwoOption"
                                                :reduce="OptionsNeonatusSosialBudayaTwoOption => OptionsNeonatusSosialBudayaTwoOption.value"
                                                label="title"
                                                placeholder="-- Menggendong --"></v-select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mt-1">
                                            <label>Kontak Mata</label>
                                            <v-select @change="saveChangedNurse($event)"
                                                v-model="form_inpatientNeonatus.neonatus_sosial_budaya.kontak_mata"
                                                aria-readonly="true"
                                                :options="OptionsNeonatusSosialBudayaTwoOption"
                                                :reduce="OptionsNeonatusSosialBudayaTwoOption => OptionsNeonatusSosialBudayaTwoOption.value"
                                                label="title"
                                                placeholder="-- Kontak Mata --"></v-select>
                                        </div>
                                        <div class="mt-1">
                                            <label>Berbicara</label>
                                            <v-select @change="saveChangedNurse($event)"
                                                v-model="form_inpatientNeonatus.neonatus_sosial_budaya.berbicara"
                                                aria-readonly="true"
                                                :options="OptionsNeonatusSosialBudayaTwoOption"
                                                :reduce="OptionsNeonatusSosialBudayaTwoOption => OptionsNeonatusSosialBudayaTwoOption.value"
                                                label="title"
                                                placeholder="-- Berbicara --"></v-select>
                                        </div>
                                        <div class="mt-1">
                                            <label>Lainnya</label>
                                            <b-form-textarea rows="3"
                                                @change="saveChangedNurse($event)"
                                                v-model="form_inpatientNeonatus.neonatus_sosial_budaya.lainnya"
                                                autocomplete="off"></b-form-textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- neonatus check penanggung jawab -->
                            <div class="col mt-32 px-0">
                                <ModernLabel label="Penanggung Jawab" class="mb-8" />
                                <div class="row mt-16 c-form">
                                    <div class="col-lg-4">
                                        <div class="mt-1">
                                            <label>Wali</label>
                                            <b-input-group>
                                                <b-form-input type="text" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientNeonatus.neonatus_penanggung_jawab.wali"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-1">
                                            <label>Nama</label>
                                            <b-input-group>
                                                <b-form-input type="text" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientNeonatus.neonatus_penanggung_jawab.nama"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-1">
                                            <label>Usia</label>
                                            <b-input-group>
                                                <b-form-input type="text" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientNeonatus.neonatus_penanggung_jawab.usia"></b-form-input>
                                            </b-input-group>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 border border-top-0 border-bottom-0">
                                        <div class="mt-1">
                                            <label>Perkawinan Ke</label>
                                            <b-input-group>
                                                <b-form-input type="text" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientNeonatus.neonatus_penanggung_jawab.perkawinan_ke"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-1">
                                            <label>Pekerjaan</label>
                                            <b-input-group>
                                                <b-form-input type="text" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientNeonatus.neonatus_penanggung_jawab.pekerjaan"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-1">
                                            <label>Pendidikan Terakhir</label>
                                            <b-input-group>
                                                <b-form-input type="text" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientNeonatus.neonatus_penanggung_jawab.pendidikan_terakhir"></b-form-input>
                                            </b-input-group>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mt-1">
                                            <label>Alamat</label>
                                            <b-input-group>
                                                <b-form-input type="text" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientNeonatus.neonatus_penanggung_jawab.alamat"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-1">
                                            <label>Telepon</label>
                                            <b-input-group>
                                                <b-form-input type="text" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientNeonatus.neonatus_penanggung_jawab.telepon"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-1">
                                            <label>Penghasilan Perbulan</label>
                                            <b-input-group>
                                                <b-form-input type="text" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientNeonatus.neonatus_penanggung_jawab.penghasilan_perbulan"></b-form-input>
                                            </b-input-group>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <section class="col mt-32 px-0">
                            <div class="card">
                                <div class="card-header bg-indigo-200">
                                    <span class="text-bold-18">Pemeriksaan Fisik</span>
                                </div>
                                <div class="col mt-1">
                                    <!-- neonatus check paru -->
                                    <div class="col mt-32">
                                        <ModernLabel label="Paru" class="mb-8" />
                                        <div class="row mt-16 c-form">
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Pergerakan Paru</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_paru.pergerakan_paru"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Frekuensi Pernapasan Paru</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_paru.frekuensi_pernapasan_paru"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Retraksi</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_paru.retraksi"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Nch</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_paru.nch"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Wheezing Paru Kanan</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_paru.wheezing_paru_kanan"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Ronkhi Paru Kiri</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_paru.ronkhi_paru_kiri"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Grunting</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_paru.grunting"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Dyspnoe</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_paru.dyspnoe"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Tachynoe</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_paru.tachynoe"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Gargling Napas Tambahan</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_paru.gargling_napas_tambahan"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Lainnya Paru Kanan</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_paru.lainnya_paru_kanan"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Wheezing Paru Kiri</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_paru.wheezing_paru_kiri"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Snooring Napas Tambahan</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_paru.snooring_napas_tambahan"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Stridor Napas Tambahan</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_paru.stridor_napas_tambahan"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Peruksi Paru-Paru</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_paru.peruksi_paru_paru"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Ronkhi Paru Kanan</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_paru.ronkhi_paru_kanan"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Perkusi Paru Kiri</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_paru.perkusi_paru_kiri"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Lainnya Paru Kiri</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_paru.lainnya_paru_kiri"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- neonatus check downscore -->
                                    <div class="col mt-32">
                                        <ModernLabel label="Down Score" class="mb-8" />
                                        <div class="row mt-16 c-form">
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Frekuensi Napas</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_down_score.frekuensi_napas"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Retraksi</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_down_score.retraksi"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Sianosis</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_down_score.sianosis"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Air Entry</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_down_score.air_entry"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Merintih</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_down_score.merintih"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- neonatus check eliminasi -->
                                    <div class="col mt-32">
                                        <ModernLabel label="Eliminasi" class="mb-8" />
                                        <div class="row mt-16 c-form">
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Anus</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_eliminasi.anus"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Defaksi Anus</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_eliminasi.defaksi_anus"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Defaksi Stoma</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_eliminasi.defaksi_stoma"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Frekuensi Defaksi</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_eliminasi.frekuensi_defaksi"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Konsistensi Defaksi</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_eliminasi.konsistensi_defaksi"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Karakteristik Fases</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_eliminasi.karakteristik_fases"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Urin</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_eliminasi.urin"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Diuresis</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_eliminasi.diuresis"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- neonatus check genitalia -->
                                    <div class="col mt-32">
                                        <ModernLabel label="Genitalia" class="mb-8" />
                                        <div class="row mt-16 c-form">
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Jenis Kelamin</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_genitalia.jenis_kelamin"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Kelainan</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_genitalia.kelainan"></b-form-input>
                                                    </b-input-group>
                                                </div>

                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Penis Laki-Laki</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_genitalia.penis_laki_laki"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Labio Perempuan</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_genitalia.labio_perempuan"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Testis Laki-Laki</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_genitalia.testis_laki_laki"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Lainnya</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_genitalia.lainnya"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- neonatus check apgar -->
                                    <div class="col mt-32">
                                        <ModernLabel label="Apgar" class="mb-8" />
                                        <div class="row mt-16 c-form">
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Menit ke</label>
                                                    <b-input-group>
                                                        <b-form-input type="number"
                                                            autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_apgar_score.menit_ke"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Denyut Jantung</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_apgar_score.denyut_jantung"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Usaha Napas</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_apgar_score.usaha_napas"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Tonus Otot</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_apgar_score.tonus_otot"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Refleks</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_apgar_score.refleks"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- neonatus check kardiologi -->
                                    <div class="col mt-32">
                                        <ModernLabel label="Kardiologi" class="mb-8" />
                                        <div class="row mt-16 c-form">
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Kelainan Jantung</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_kardiologi.kelainan_jantung"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Denyut Jantung</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_kardiologi.denyut_jantung"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Tipe Denyut Jantung</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_kardiologi.type_denyut_jantung"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Murmur</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_kardiologi.murmur"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Keterangan Murmur</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_kardiologi.keterangan_murmur"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Gallop</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_kardiologi.gallop"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Keterangan Gallop</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_kardiologi.keterangan_gallop"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Sianosis</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_kardiologi.sianosis"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Pucat</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_kardiologi.pucat"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Crt</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_kardiologi.crt"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Akral</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_kardiologi.akral"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- neonatus check skala nyeri -->
                                    <div class="col mt-32">
                                        <ModernLabel label="Skala Nyeri" class="mb-8" />
                                        <div class="row mt-16 c-form">
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Nama</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_skala_nyeri.nama"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Skor</label>
                                                    <b-input-group>
                                                        <b-form-input type="number"
                                                            autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_skala_nyeri.skor"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- neonatus check trauma kulit -->
                                    <div class="col mt-32">
                                        <ModernLabel label="Trauma Kulit" class="mb-8" />
                                        <div class="row mt-16 c-form">
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Usia Gestasi</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_trauma_kulit.usia_gestasi"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Status Mental</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_trauma_kulit.status_mental"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Mobilisasi</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_trauma_kulit.mobilisasi"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Aktivitas</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_trauma_kulit.aktivitas"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Nutrisi</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_trauma_kulit.nutrisi"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Kelembaban</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_trauma_kulit.kelembaban"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- neonatus check reaksi alergi -->
                                    <div class="col mt-32">
                                        <ModernLabel label="Reaksi Alergi" class="mb-8" />
                                        <div class="row mt-16 c-form">
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Makanan</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_alergi_reaksi.makanan"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Makanan Reaksi</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_alergi_reaksi.makanan_reaksi"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Obat</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_alergi_reaksi.obat"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Obat Reaksi</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_alergi_reaksi.obat_reaksi"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Lainnya</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_alergi_reaksi.lainnya"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Lainnya Reaksi</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_alergi_reaksi.lainnya_reaksi"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Klip Tanda Alergi</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_alergi_reaksi.klip_tanda_alergi"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Beritahu Dokter</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_alergi_reaksi.beritahu_dokter"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Beritahu Farmasi</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_alergi_reaksi.beritahu_farmasi"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Beritahu Dietisien</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_alergi_reaksi.beritahu_dietisien"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- neonatus check muskuloskeletal -->
                                    <div class="col mt-32">
                                        <ModernLabel label="Muskuloskeletal" class="mb-8" />
                                        <div class="row mt-16 c-form">
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Tonus</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_muskuloskeletal.tonus"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Gerakan Bayi</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_muskuloskeletal.gerakan_bayi"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Kelainan Tulang</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_muskuloskeletal.kelainan_tulang"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- neonatus check gastrointesnital -->
                                    <div class="col mt-32">
                                        <ModernLabel label="Gastrointesnital" class="mb-8" />
                                        <div class="row mt-16 c-form">
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Stomatitis</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_gastrointesnital.stomatitis"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Labio Palatochiziz</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_gastrointesnital.labio_palatochiziz"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Mukosa Kering</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_gastrointesnital.mukosa_kering"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Pendarahan Gusi</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_gastrointesnital.pendarahan_gusi"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Muntah</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_gastrointesnital.muntah"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Frekuensi Muntah</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_gastrointesnital.frekuensi_muntah"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Residu</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_gastrointesnital.residu"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Asites</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_gastrointesnital.asites"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Bising Usus</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_gastrointesnital.bising_usus"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Nyeri Tekan</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_gastrointesnital.nyeri_tekan"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Limpa</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_gastrointesnital.limpa"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Hepar</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_gastrointesnital.hepar"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Lokasi Nyeri Tekan</label>
                                                    <b-input-group>
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientNeonatus.neonatus_gastrointesnital.lokasi_nyeri_tekan"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col mt-32"></div>
                                </div>
                            </div>
                        </section>
                    </div>
                </section>

                <!-- pengkajian awal anak -->
                <section v-if="this.view_condition.health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f' && view_condition.isChild == true" class="mt-20 mt-lg-32">
                    <div class="card">
                        <div class="card-header text-white font-weight-bold bg-primary">PENGKAJIAN AWAL RAWAT INAP ANAK</div>
                        <div class="card-body">
                            <!-- riwayat kesehatan dahulu -->
                            <div class="col px-0">
                                <ModernLabel label="Riwayat kesehatan Dahulu" class="mb-8" />
                                <div class="row mt-16 c-form">
                                    <div class="col-lg-4">
                                        <div class="mt-1">
                                            <label>Jenis Persalinan</label>
                                            <v-select @change="saveChangedNurse($event)"
                                                v-model="form_inpatientChild.child_riwayat_kesehatan_dahulu.jenis_persalinan"
                                                aria-readonly="true"
                                                :options="options_anak_jenis_persalinan"
                                                :reduce="options_anak_jenis_persalinan => options_anak_jenis_persalinan.value"
                                                label="title"
                                                placeholder="-- Jenis Persalinan --"></v-select>
                                        </div>
                                        <div class="mt-1">
                                            <label>Berat Badan Lahir</label>
                                            <b-input-group append="kg">
                                                <b-form-input type="number" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientChild.child_riwayat_kesehatan_dahulu.berat_badan_lahir"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-1">
                                            <label>Tinggi Badan Lahir</label>
                                            <b-input-group append="cm">
                                                <b-form-input type="number" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientChild.child_riwayat_kesehatan_dahulu.tinggi_badan_lahir"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-1">
                                            <label>Status Gizi</label>
                                            <v-select @change="saveChangedNurse($event)"
                                                v-model="form_inpatientChild.child_riwayat_kesehatan_dahulu.status_gizi"
                                                aria-readonly="true"
                                                :options="options_anak_status_gizi"
                                                :reduce="options_anak_status_gizi => options_anak_status_gizi.value"
                                                label="title"
                                                placeholder="-- Status Gizi --"></v-select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 border border-top-0 border-bottom-0">
                                        <div class="mt-1">
                                            <label>Keterangan Lahir</label>
                                            <b-input-group>
                                                <b-form-input type="text" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientChild.child_riwayat_kesehatan_dahulu.keterangan_lahir"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-1">
                                            <label>Tumbuh Kembang</label>
                                            <b-input-group>
                                                <b-form-input type="text" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientChild.child_riwayat_kesehatan_dahulu.tumbuh_kembang"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-1">
                                            <label>Tinggi Badan</label>
                                            <b-input-group append="cm">
                                                <b-form-input type="number" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientChild.child_riwayat_kesehatan_dahulu.tinggi_badan"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-1">
                                            <label>Berat Badan</label>
                                            <b-input-group append="kg">
                                                <b-form-input type="number" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientChild.child_riwayat_kesehatan_dahulu.berat_badan"></b-form-input>
                                            </b-input-group>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mt-1">
                                            <label>Lingkar Kepala</label>
                                            <b-input-group append="cm">
                                                <b-form-input type="number" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientChild.child_riwayat_kesehatan_dahulu.lingkar_kepala"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-1">
                                            <label>Lila</label>
                                            <b-input-group>
                                                <b-form-input type="number" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientChild.child_riwayat_kesehatan_dahulu.lila"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-1">
                                            <label>IMT</label>
                                            <b-input-group append="kg/m²">
                                                <b-form-input type="number" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientChild.child_riwayat_kesehatan_dahulu.imt"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-1">
                                            <label>Perawakan</label>
                                            <b-input-group>
                                                <b-form-input type="text" autocomplete="off"
                                                    @change="saveChangedNurse($event)"
                                                    v-model="form_inpatientChild.child_riwayat_kesehatan_dahulu.perawakan"></b-form-input>
                                            </b-input-group>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- riwayat tumbuh kembang -->
                            <div class="col mt-32 px-0">
                                <ModernLabel label="Riwayat Tumbuh Kembang" class="mb-8" />
                                <div class="row mt-28 c-form">
                                    <div class="col-12 col-lg-4">
                                        <label>BB anak saat lahir</label>
                                        <b-input-group append="Gr">
                                            <b-form-input type="number" autocomplete="off"
                                                @change="saveChangedNurse($event)"
                                                v-model="form_inpatientChild.child_riwayat_tumbuh_kembang.bb_anak_saat_lahir"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <label>PB anak saat lahir</label>
                                        <b-input-group append="Cm">
                                            <b-form-input type="number" autocomplete="off"
                                                @change="saveChangedNurse($event)"
                                                v-model="form_inpatientChild.child_riwayat_tumbuh_kembang.pb_anak_saat_lahir"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <label>Asi,sampai umur</label>
                                        <b-input-group append="Bln - Tahun">
                                            <b-form-input type="text" autocomplete="off"
                                                @change="saveChangedNurse($event)"
                                                v-model="form_inpatientChild.child_riwayat_tumbuh_kembang.asi_sampai_umur"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <label>Susu Formula,dimulai umur</label>
                                        <b-input-group append="Bln - Tahun">
                                            <b-form-input type="text" autocomplete="off"
                                                @change="saveChangedNurse($event)"
                                                v-model="form_inpatientChild.child_riwayat_tumbuh_kembang.susu_formula"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <label>Makanan padat,dimulai umur</label>
                                        <b-input-group append="Bln - Tahun">
                                            <b-form-input type="text" autocomplete="off"
                                                @change="saveChangedNurse($event)"
                                                v-model="form_inpatientChild.child_riwayat_tumbuh_kembang.makanan_padat"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <label>Makanan tambahan,dimulai umur</label>
                                        <b-input-group append="Bln - Tahun">
                                            <b-form-input type="text" autocomplete="off"
                                                @change="saveChangedNurse($event)"
                                                v-model="form_inpatientChild.child_riwayat_tumbuh_kembang.makanan_tambahan"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <label>Tengkurap</label>
                                        <b-input-group append="Bln - Tahun">
                                            <b-form-input type="text" autocomplete="off"
                                                @change="saveChangedNurse($event)"
                                                v-model="form_inpatientChild.child_riwayat_tumbuh_kembang.tengkurap"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <label>Duduk</label>
                                        <b-input-group append="Bln - Tahun">
                                            <b-form-input type="text" autocomplete="off"
                                                @change="saveChangedNurse($event)"
                                                v-model="form_inpatientChild.child_riwayat_tumbuh_kembang.duduk"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <label>Merangkak</label>
                                        <b-input-group append="Bln - Tahun">
                                            <b-form-input type="text" autocomplete="off"
                                                @change="saveChangedNurse($event)"
                                                v-model="form_inpatientChild.child_riwayat_tumbuh_kembang.merangkak"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <label>Berdiri</label>
                                        <b-input-group append="Bln - Tahun">
                                            <b-form-input type="text" autocomplete="off"
                                                @change="saveChangedNurse($event)"
                                                v-model="form_inpatientChild.child_riwayat_tumbuh_kembang.berdiri"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <label>Berjalan</label>
                                        <b-input-group append="Bln - Tahun">
                                            <b-form-input type="text" autocomplete="off"
                                                @change="saveChangedNurse($event)"
                                                v-model="form_inpatientChild.child_riwayat_tumbuh_kembang.berjalan"></b-form-input>
                                        </b-input-group>
                                    </div>
                                </div>
                            </div>
                            <!-- riwayat imunisasi -->
                            <!-- <div class="col mt-32 px-0">
                                <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                                    style="margin-left:-16px; margin-right:-16px;">Riwayat
                                    Imunisasi</div>
                                <div class="row mt-28 c-form">
                                    <div class="col-12 col-lg-12">
                                        <table class="table mt-12">
                                            <tr>
                                                <th>Nama Vaksin</th>
                                                <th width="10%">Qty</th>
                                                <th>Aksi</th>
                                            </tr>
                                            <tr class="item"
                                                v-for="(item, index) in form_inpatientChild.child_riwayat_imunisasi.nama_vaksin">
                                                <td>
                                                    <b-form-input style="display:none"
                                                        v-model="item.value" />
                                                    <b-form-input style="display:none"
                                                        v-model="item.var_1" />
                                                    <b-form-input style="display:none"
                                                        v-model="item.qty" />
                                                    {{ item.title }}
                                                </td>
                                                <td><b-form-input type="number"
                                                        v-model="item.qty"
                                                        aria-readonly="true" /></td>
                                                <td>
                                                    <b-button variant="danger-600" size="sm"
                                                        @click="OnDeleteRiwayatVaksin(index)"
                                                        class="mr-2">
                                                        <feather-icon icon="TrashIcon" />
                                                    </b-button>
                                                </td>
                                            </tr>
                                        </table>
                                        <v-select
                                            v-model="form_inpatientChild.child_riwayat_imunisasi.vaksin_select"
                                            @input="OnRiwayatVaksin($event)" aria-readonly="true"
                                            :options="option_riwayat_vaksin"
                                            :reduce="option_riwayat_vaksin => option_riwayat_vaksin"
                                            label="title"
                                            placeholder="-- Cari Vaksin --"></v-select>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                        <section class="col mt-32 px-0">
                            <div class="card">
                                <div class="card-header bg-indigo-200">
                                    <span class="text-bold-18">Pemeriksaan Fisik</span>
                                </div>
                                <div class="col mt-1">
                                    <!-- neonatus check paru -->
                                    <div class="col mt-32">
                                        <ModernLabel label="Tanda Vital" class="mb-8" />
                                        <div class="row mt-16 c-form">
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Tekanan Darah</label>
                                                    <b-input-group append="mmHg">
                                                        <b-form-input type="number"
                                                            autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_pemeriksaan_fisik_ttv.tekanan_darah"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Kesadaran</label>
                                                    <b-input-group append="x/menit">
                                                        <b-form-input type="number"
                                                            autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_pemeriksaan_fisik_ttv.kesadaran"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Suhu</label>
                                                    <b-input-group append="ᵒC">
                                                        <b-form-input type="number"
                                                            autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_pemeriksaan_fisik_ttv.suhu"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Nadi</label>
                                                    <b-input-group append="x/menit">
                                                        <b-form-input type="number"
                                                            autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_pemeriksaan_fisik_ttv.nadi"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col mt-32">
                                        <ModernLabel label="Berat badan" class="mb-8" />
                                        <div class="row mt-16 c-form">
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Berat Badan</label>
                                                    <b-input-group append="kg">
                                                        <b-form-input type="number"
                                                            autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_pemeriksaan_fisik_berat_badan.berat_badan"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Tinggi Badan</label>
                                                    <b-input-group append="cm">
                                                        <b-form-input type="number"
                                                            autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_pemeriksaan_fisik_berat_badan.tinggi_badan"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>LLA</label>
                                                    <b-input-group append="cm">
                                                        <b-form-input type="number"
                                                            autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_pemeriksaan_fisik_berat_badan.lla"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col mt-32">
                                        <ModernLabel label="Neorologi" class="mb-8" />
                                        <div class="row mt-16 c-form">
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Penglihatan</label>
                                                    <v-select
                                                        v-model="form_inpatientChild.child_pemeriksaan_fisik_neorologi.penglihatan"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_neorologi_penglihatan"
                                                        :reduce="option_anak_pemeriksaan_fisik_neorologi_penglihatan => option_anak_pemeriksaan_fisik_neorologi_penglihatan.value"
                                                        label="title"
                                                        placeholder="-- Penglihatan --"></v-select>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Bicara</label>
                                                    <v-select
                                                        v-model="option_anak_pemeriksaan_fisik_neorologi_bicara_artikulasi_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_neorologi_bicara_artikulasi"
                                                        :reduce="option_anak_pemeriksaan_fisik_neorologi_bicara_artikulasi => option_anak_pemeriksaan_fisik_neorologi_bicara_artikulasi.value"
                                                        label="title"
                                                        placeholder="-- Bicara --"></v-select>
                                                    <b-input-group
                                                        v-if="option_anak_pemeriksaan_fisik_neorologi_bicara_artikulasi_select === 2">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_pemeriksaan_fisik_neorologi.bicara"
                                                            placeholder="Sebutkan..."></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Kekuatan Otot</label>
                                                    <v-select
                                                        v-model="form_inpatientChild.child_pemeriksaan_fisik_neorologi.kekuatan_otot"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_neorologi_kekuatan_otot"
                                                        :reduce="option_anak_pemeriksaan_fisik_neorologi_kekuatan_otot => option_anak_pemeriksaan_fisik_neorologi_kekuatan_otot.value"
                                                        label="title"
                                                        placeholder="-- Kekuatan Otot --"></v-select>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Menggunakan Alat Bantu
                                                        Penglihatan</label>
                                                    <v-select
                                                        v-model="option_anak_pemeriksaan_fisik_neorologi_menggunakan_alat_bantu_penglihatan_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_neorologi_menggunakan_alat_bantu_penglihatan"
                                                        :reduce="option_anak_pemeriksaan_fisik_neorologi_menggunakan_alat_bantu_penglihatan => option_anak_pemeriksaan_fisik_neorologi_menggunakan_alat_bantu_penglihatan.value"
                                                        label="title"
                                                        placeholder="-- Alat Bantu Penglihatan --"></v-select>
                                                    <v-select
                                                        v-if="option_anak_pemeriksaan_fisik_neorologi_menggunakan_alat_bantu_penglihatan_select === 2"
                                                        v-model="form_inpatientChild.child_pemeriksaan_fisik_neorologi.menggunakan_alat_bantu_penglihatan"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_neorologi_menggunakan_alat_bantu_penglihatan_ya"
                                                        :reduce="option_anak_pemeriksaan_fisik_neorologi_menggunakan_alat_bantu_penglihatan_ya => option_anak_pemeriksaan_fisik_neorologi_menggunakan_alat_bantu_penglihatan_ya.value"
                                                        label="title"
                                                        placeholder="-- Pilih alat bantu penglihatan --"></v-select>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Sensorik</label>
                                                    <v-select
                                                        v-model="option_anak_pemeriksaan_fisik_neorologi_sensorik_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_neorologi_sensorik"
                                                        :reduce="option_anak_pemeriksaan_fisik_neorologi_sensorik => option_anak_pemeriksaan_fisik_neorologi_sensorik.value"
                                                        label="title"
                                                        placeholder="-- Sensorik --"></v-select>
                                                    <b-input-group
                                                        v-if="option_anak_pemeriksaan_fisik_neorologi_sensorik_select === 4">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_pemeriksaan_fisik_neorologi.sensorik"
                                                            placeholder="Lainnya..."></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Menggunakan Alat Bantu
                                                        Pendengaran</label>
                                                    <v-select
                                                        v-model="form_inpatientChild.child_pemeriksaan_fisik_neorologi.menggunakan_alat_bantu_pendengaran"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_neorologi_menggunakan_alat_bantu_pendengaran"
                                                        :reduce="option_anak_pemeriksaan_fisik_neorologi_menggunakan_alat_bantu_pendengaran => option_anak_pemeriksaan_fisik_neorologi_menggunakan_alat_bantu_pendengaran.value"
                                                        label="title"
                                                        placeholder="-- Alat Bantu Pendengaran --"></v-select>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Pendengaran</label>
                                                    <v-select
                                                        v-model="option_anak_pemeriksaan_fisik_neorologi_pendengaran_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_neorologi_pendengaran"
                                                        :reduce="option_anak_pemeriksaan_fisik_neorologi_pendengaran => option_anak_pemeriksaan_fisik_neorologi_pendengaran.value"
                                                        label="title"
                                                        placeholder="-- Pendengaran --"></v-select>
                                                    <b-input-group
                                                        v-if="option_anak_pemeriksaan_fisik_neorologi_pendengaran_select === 6">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form.child_pemeriksaan_fisik_neorologi.pendengaran"
                                                            placeholder="lainnya..."></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Motorik</label>
                                                    <v-select
                                                        v-model="option_anak_pemeriksaan_fisik_neorologi_motorik_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_neorologi_motorik"
                                                        :reduce="option_anak_pemeriksaan_fisik_neorologi_motorik => option_anak_pemeriksaan_fisik_neorologi_motorik.value"
                                                        label="title"
                                                        placeholder="-- Pendengaran --"></v-select>
                                                    <b-input-group
                                                        v-if="option_anak_pemeriksaan_fisik_neorologi_motorik_select === 5">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_pemeriksaan_fisik_neorologi.motorik"
                                                            placeholder="Lainnya..."></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col mt-32">
                                        <ModernLabel label="Integument" class="mb-8" />
                                        <div class="row mt-16 c-form">
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Warna Kulit</label>
                                                    <v-select
                                                        v-model="form_inpatientChild.child_pemeriksaan_fisik_integument.warna_kulit"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_integument_warna_kulit"
                                                        :reduce="option_anak_pemeriksaan_fisik_integument_warna_kulit => option_anak_pemeriksaan_fisik_integument_warna_kulit.value"
                                                        label="title"
                                                        placeholder="-- Warna Kulit --"></v-select>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Kriteria resiko dekubitus</label>
                                                    <v-select
                                                        v-model="form_inpatientChild.child_pemeriksaan_fisik_integument.kriteria_resiko_dekubitus"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_integument_kriteria_resiko_dekubitus"
                                                        :reduce="option_anak_pemeriksaan_fisik_integument_kriteria_resiko_dekubitus => option_anak_pemeriksaan_fisik_integument_kriteria_resiko_dekubitus.value"
                                                        label="title"
                                                        placeholder="-- Resiko Dekubitus --"></v-select>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Tugor</label>
                                                    <v-select
                                                        v-model="option_anak_pemeriksaan_fisik_integument_tugor_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_integument_tugor"
                                                        :reduce="option_anak_pemeriksaan_fisik_integument_tugor => option_anak_pemeriksaan_fisik_integument_tugor.value"
                                                        label="title"
                                                        placeholder="-- Tugor --"></v-select>
                                                    <b-input-group
                                                        v-if="option_anak_pemeriksaan_fisik_integument_tugor_select === 5">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_pemeriksaan_fisik_integument.tugor"
                                                            placeholder="lainnya..."></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Kulit</label>
                                                    <v-select
                                                        v-model="option_anak_pemeriksaan_fisik_integument_kulit_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_integument_kulit"
                                                        :reduce="option_anak_pemeriksaan_fisik_integument_kulit => option_anak_pemeriksaan_fisik_integument_kulit.value"
                                                        label="title"
                                                        placeholder="-- Kulit --"></v-select>
                                                    <b-input-group
                                                        v-if="option_anak_pemeriksaan_fisik_integument_kulit_select === 6">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_pemeriksaan_fisik_integument.kulit"
                                                            placeholder="lainnya..."></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col mt-32">
                                        <ModernLabel label="Kardiovaskuler" class="mb-8" />
                                        <div class="row mt-16 c-form">
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Warna Kulit</label>
                                                    <v-select
                                                        v-model="option_anak_pemeriksaan_fisik_kardiovaskuler_warna_kulit_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_kardiovaskuler_warna_kulit"
                                                        :reduce="option_anak_pemeriksaan_fisik_kardiovaskuler_warna_kulit => option_anak_pemeriksaan_fisik_kardiovaskuler_warna_kulit.value"
                                                        label="title"
                                                        placeholder="-- Warna Kulit --"></v-select>
                                                    <b-input-group
                                                        v-if="option_anak_pemeriksaan_fisik_kardiovaskuler_warna_kulit_select === 5">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_pemeriksaan_fisik_kardiovaskuler.warna_kulit"
                                                            placeholder="Lainnya..."></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Nyeri Dada</label>
                                                    <v-select
                                                        v-model="option_anak_pemeriksaan_fisik_kardiovaskuler_nyeri_dada_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_kardiovaskuler_nyeri_dada"
                                                        :reduce="option_anak_pemeriksaan_fisik_kardiovaskuler_nyeri_dada => option_anak_pemeriksaan_fisik_kardiovaskuler_nyeri_dada.value"
                                                        label="title"
                                                        placeholder="-- Nyeri Dada --"></v-select>
                                                    <b-input-group
                                                        v-if="option_anak_pemeriksaan_fisik_kardiovaskuler_nyeri_dada_select === 2">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_pemeriksaan_fisik_kardiovaskuler.nyeri_dada"
                                                            placeholder="sebutkan..."></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Denyut Nadi</label>
                                                    <v-select
                                                        v-model="form_inpatientChild.child_pemeriksaan_fisik_kardiovaskuler.denyut_nadi"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_kardiovaskuler_denyut_nadi"
                                                        :reduce="option_anak_pemeriksaan_fisik_kardiovaskuler_denyut_nadi => option_anak_pemeriksaan_fisik_kardiovaskuler_denyut_nadi.value"
                                                        label="title"
                                                        placeholder="-- Denyut Nadi --"></v-select>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Pulasi</label>
                                                    <v-select
                                                        v-model="option_anak_pemeriksaan_fisik_kardiovaskuler_pulasi_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_kardiovaskuler_pulasi"
                                                        :reduce="option_anak_pemeriksaan_fisik_kardiovaskuler_pulasi => option_anak_pemeriksaan_fisik_kardiovaskuler_pulasi.value"
                                                        label="title"
                                                        placeholder="-- Pulasi --"></v-select>
                                                    <b-input-group
                                                        v-if="option_anak_pemeriksaan_fisik_kardiovaskuler_pulasi_select === 3">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_pemeriksaan_fisik_kardiovaskuler.pulasi"
                                                            placeholder="Lainnya..."></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Sirkulasi</label>
                                                    <v-select
                                                        v-model="option_anak_pemeriksaan_fisik_kardiovaskuler_sirkulasi_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_kardiovaskuler_sirkulasi"
                                                        :reduce="option_anak_pemeriksaan_fisik_kardiovaskuler_sirkulasi => option_anak_pemeriksaan_fisik_kardiovaskuler_sirkulasi.value"
                                                        label="title"
                                                        placeholder="-- Sirkulasi --"></v-select>
                                                    <b-input-group
                                                        v-if="option_anak_pemeriksaan_fisik_kardiovaskuler_sirkulasi_select === 5">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_pemeriksaan_fisik_kardiovaskuler.sirkulasi"
                                                            placeholder="Sebutkan lokasi edema..."></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col mt-32">
                                        <ModernLabel label="Respirasi" class="mb-8" />
                                        <div class="row mt-16 c-form">
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Pola Nafas</label>
                                                    <v-select
                                                        v-model="form_inpatientChild.child_pemeriksaan_fisik_respirasi.pola_nafas"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_respirasi_pola_nafas"
                                                        :reduce="option_anak_pemeriksaan_fisik_respirasi_pola_nafas => option_anak_pemeriksaan_fisik_respirasi_pola_nafas.value"
                                                        label="title"
                                                        placeholder="-- Pola Nafas --"></v-select>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Volume Pernafasan</label>
                                                    <v-select
                                                        v-model="form_inpatientChild.child_pemeriksaan_fisik_respirasi.volume_pernafasan"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_respirasi_volume_pernafasan"
                                                        :reduce="option_anak_pemeriksaan_fisik_respirasi_volume_pernafasan => option_anak_pemeriksaan_fisik_respirasi_volume_pernafasan.value"
                                                        label="title"
                                                        placeholder="-- Volume Pernafasan --"></v-select>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Irama Nafas</label>
                                                    <v-select
                                                        v-model="form_inpatientChild.child_pemeriksaan_fisik_respirasi.irama_nafas"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_respirasi_irama_nafas"
                                                        :reduce="option_anak_pemeriksaan_fisik_respirasi_irama_nafas => option_anak_pemeriksaan_fisik_respirasi_irama_nafas.value"
                                                        label="title"
                                                        placeholder="-- Irama Nafas --"></v-select>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Kesulitan Bernafas</label>
                                                    <v-select
                                                        v-model="option_anak_pemeriksaan_fisik_respirasi_kesulitan_bernafas_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_respirasi_kesulitan_bernafas"
                                                        :reduce="option_anak_pemeriksaan_fisik_respirasi_kesulitan_bernafas => option_anak_pemeriksaan_fisik_respirasi_kesulitan_bernafas.value"
                                                        label="title"
                                                        placeholder="-- Kesulitan Bernafas --"></v-select>
                                                    <v-select
                                                        v-if="option_anak_pemeriksaan_fisik_respirasi_kesulitan_bernafas_select === 2"
                                                        v-model="form_inpatientChild.child_pemeriksaan_fisik_respirasi.kesulitan_bernafas"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_respirasi_kesulitan_bernafas_ya"
                                                        :reduce="option_anak_pemeriksaan_fisik_respirasi_kesulitan_bernafas_ya => option_anak_pemeriksaan_fisik_respirasi_kesulitan_bernafas_ya.value"
                                                        label="title"
                                                        placeholder="-- Pilih Jenis Kesulitan Bernafas --"></v-select>
                                                    <b-input-group
                                                        v-if="option_anak_pemeriksaan_fisik_respirasi_kesulitan_bernafas_select === 3">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_pemeriksaan_fisik_respirasi.kesulitan_bernafas"
                                                            placeholder="Lainnya..."></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Batuk</label>
                                                    <v-select
                                                        v-model="option_anak_pemeriksaan_fisik_respirasi_batuk_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_respirasi_batuk"
                                                        :reduce="option_anak_pemeriksaan_fisik_respirasi_batuk => option_anak_pemeriksaan_fisik_respirasi_batuk.value"
                                                        label="title"
                                                        placeholder="-- Batuk --"></v-select>
                                                    <v-select
                                                        v-if="option_anak_pemeriksaan_fisik_respirasi_batuk_select === 2"
                                                        v-model="form_inpatientChild.child_pemeriksaan_fisik_respirasi.batuk"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_respirasi_batuk_ya"
                                                        :reduce="option_anak_pemeriksaan_fisik_respirasi_batuk_ya => option_anak_pemeriksaan_fisik_respirasi_batuk_ya.value"
                                                        label="title"
                                                        placeholder="-- Pilih Jenis Batuk --"></v-select>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Jenis Pernafasan</label>
                                                    <v-select
                                                        v-model="option_anak_pemeriksaan_fisik_respirasi_jenis_pernafasan_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_respirasi_jenis_pernafasan"
                                                        :reduce="option_anak_pemeriksaan_fisik_respirasi_jenis_pernafasan => option_anak_pemeriksaan_fisik_respirasi_jenis_pernafasan.value"
                                                        label="title"
                                                        placeholder="-- Jenis Pernafasan --"></v-select>
                                                    <b-input-group
                                                        v-if="option_anak_pemeriksaan_fisik_respirasi_jenis_pernafasan_select === 3">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_pemeriksaan_fisik_respirasi.jenis_pernafasan"
                                                            placeholder="alat bantu pernafasan sebutkan..."></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col mt-32">
                                        <ModernLabel label="Gastrointestinal" class="mb-8" />
                                        <div class="row mt-16 c-form">
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Mulut</label>
                                                    <v-select
                                                        v-model="option_anak_pemeriksaan_fisik_gastrointestinal_mulut_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_gastrointestinal_mulut"
                                                        :reduce="option_anak_pemeriksaan_fisik_gastrointestinal_mulut => option_anak_pemeriksaan_fisik_gastrointestinal_mulut.value"
                                                        label="title"
                                                        placeholder="-- Mulut --"></v-select>
                                                    <b-input-group
                                                        v-if="option_anak_pemeriksaan_fisik_gastrointestinal_mulut_select === 7">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.mulut"
                                                            placeholder="lainnya..."></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Tenggorokan</label>
                                                    <v-select
                                                        v-model="option_anak_pemeriksaan_fisik_gastrointestinal_tenggorokan_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_gastrointestinal_tenggorokan"
                                                        :reduce="option_anak_pemeriksaan_fisik_gastrointestinal_tenggorokan => option_anak_pemeriksaan_fisik_gastrointestinal_tenggorokan.value"
                                                        label="title"
                                                        placeholder="-- Tenggorokan --"></v-select>
                                                    <b-input-group
                                                        v-if="option_anak_pemeriksaan_fisik_gastrointestinal_tenggorokan_select === 3">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.tenggorokan"
                                                            placeholder="Lainnya..."></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Gigi</label>
                                                    <v-select
                                                        v-model="option_anak_pemeriksaan_fisik_gastrointestinal_gigi_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_gastrointestinal_gigi"
                                                        :reduce="option_anak_pemeriksaan_fisik_gastrointestinal_gigi => option_anak_pemeriksaan_fisik_gastrointestinal_gigi.value"
                                                        label="title"
                                                        placeholder="-- Gigi --"></v-select>
                                                    <b-input-group
                                                        v-if="option_anak_pemeriksaan_fisik_gastrointestinal_gigi_select === 7">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.gigi"
                                                            placeholder="Lainnya..."></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Abdomen</label>
                                                    <v-select
                                                        v-model="option_anak_pemeriksaan_fisik_gastrointestinal_abdomen_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_gastrointestinal_abdomen"
                                                        :reduce="option_anak_pemeriksaan_fisik_gastrointestinal_abdomen => option_anak_pemeriksaan_fisik_gastrointestinal_abdomen.value"
                                                        label="title"
                                                        placeholder="-- Abdomen --"></v-select>
                                                    <b-input-group
                                                        v-if="option_anak_pemeriksaan_fisik_gastrointestinal_abdomen_select === 4">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.abdomen"
                                                            placeholder="Lainnya..."></b-form-input>
                                                    </b-input-group>
                                                    <b-input-group
                                                        v-if="option_anak_pemeriksaan_fisik_gastrointestinal_abdomen_select === 2 || option_anak_pemeriksaan_fisik_gastrointestinal_abdomen_select === 3"
                                                        append="cm">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.abdomen"
                                                            placeholder="LP..."></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Lidah</label>
                                                    <v-select
                                                        v-model="option_anak_pemeriksaan_fisik_gastrointestinal_lidah_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_pemeriksaan_fisik_gastrointestinal_lidah"
                                                        :reduce="option_anak_pemeriksaan_fisik_gastrointestinal_lidah => option_anak_pemeriksaan_fisik_gastrointestinal_lidah.value"
                                                        label="title"
                                                        placeholder="-- Lidah --"></v-select>
                                                    <b-input-group
                                                        v-if="option_anak_pemeriksaan_fisik_gastrointestinal_lidah_select === 4">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.lidah"
                                                            placeholder="lainnya..."></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col mt-32"></div>
                                </div>
                            </div>
                        </section>
                        <section class="col mt-32 px-0">
                            <div class="card">
                                <div class="card-header bg-indigo-200">
                                    <span class="text-bold-18">Penilaian Persistem</span>
                                </div>
                                <div class="col mt-1">
                                    <!-- neonatus check paru -->
                                    <div class="col mt-32">
                                        <ModernLabel label="Penilaian Umum" class="mb-8" />
                                        <div class="row mt-16 c-form">
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Apakah ada nyeri</label>
                                                    <v-select
                                                        v-model="form_inpatientChild.child_penilaian_persistem_umum.apakah_ada_nyeri"
                                                        aria-readonly="true"
                                                        :options="option_anak_penilaian_persistem_umum_kenyamanan"
                                                        :reduce="option_anak_penilaian_persistem_umum_kenyamanan => option_anak_penilaian_persistem_umum_kenyamanan.value"
                                                        label="title"
                                                        placeholder="-- Nyeri --"></v-select>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Riwayat Merokok</label>
                                                    <v-select
                                                        v-model="option_anak_penilaian_persistem_umum_riwayat_merokok_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_penilaian_persistem_umum_kenyamanan"
                                                        :reduce="option_anak_penilaian_persistem_umum_kenyamanan => option_anak_penilaian_persistem_umum_kenyamanan.value"
                                                        label="title"
                                                        placeholder="-- Riwayat Merokok --"></v-select>
                                                    <b-input-group
                                                        v-if="option_anak_penilaian_persistem_umum_riwayat_merokok_select === 2"
                                                        append="Batang">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_penilaian_persistem_umum.riwayat_merokok"
                                                            placeholder="jumlah per hari..."></b-form-input>
                                                    </b-input-group>
                                                    <b-input-group
                                                        v-if="option_anak_penilaian_persistem_umum_riwayat_merokok_select === 2">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_penilaian_persistem_umum.riwayat_merokok"
                                                            placeholder="Lama merokok..."></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Riwayat Minuman Keras</label>
                                                    <v-select
                                                        v-model="option_anak_penilaian_persistem_umum_riwayat_minuman_keras_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_penilaian_persistem_umum_riwayat_minuman_keras"
                                                        :reduce="option_anak_penilaian_persistem_umum_riwayat_minuman_keras => option_anak_penilaian_persistem_umum_riwayat_minuman_keras.value"
                                                        label="title"
                                                        placeholder="-- Riwayat Minuman Keras --"></v-select>
                                                    <b-input-group
                                                        v-if="option_anak_penilaian_persistem_umum_riwayat_minuman_keras_select === 2"
                                                        append="Jumlah/Hari">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_penilaian_persistem_umum.riwayat_minuman_keras"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Pola Istirahat/Tidur</label>
                                                    <v-select
                                                        v-model="form_inpatientChild.child_penilaian_persistem_umum.pola_istirahat"
                                                        aria-readonly="true"
                                                        :options="option_anak_penilaian_persistem_umum_pola_istirahat_tidur"
                                                        :reduce="option_anak_penilaian_persistem_umum_pola_istirahat_tidur => option_anak_penilaian_persistem_umum_pola_istirahat_tidur.value"
                                                        label="title"
                                                        placeholder="-- Riwayat Minuman Keras --"></v-select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Riwayat Penggunaan Obat Penenang</label>
                                                    <v-select
                                                        v-model="option_anak_penilaian_persistem_umum_riwayat_penggunaan_obat_penenang_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_penilaian_persistem_umum_riwayat_penggunaan_obat_penenang"
                                                        :reduce="option_anak_penilaian_persistem_umum_riwayat_penggunaan_obat_penenang => option_anak_penilaian_persistem_umum_riwayat_penggunaan_obat_penenang.value"
                                                        label="title"
                                                        placeholder="-- Riwayat Penggunaan Obat Penenang --"></v-select>
                                                    <b-input-group
                                                        v-if="option_anak_penilaian_persistem_umum_riwayat_penggunaan_obat_penenang_select === 2"
                                                        append="Jenis">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_penilaian_persistem_umum.riwayat_penggunaan_obat_penenang"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col mt-32">
                                        <ModernLabel label="Asesmen Fungsi" class="mb-8" />
                                        <div class="row mt-16 c-form">
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Kemampuan Melakukan Aktivitas
                                                        Sehari-Hari</label>
                                                    <v-select
                                                        v-model="form_inpatientChild.child_penilaian_persistem_asesmen_fungsi.kemampuan_melakukan_aktivitas_sehari"
                                                        aria-readonly="true"
                                                        :options="option_anak_penilaian_persistem_asesmen_fungsi_melakukan_aktivitas_sehari_hari"
                                                        :reduce="option_anak_penilaian_persistem_asesmen_fungsi_melakukan_aktivitas_sehari_hari => option_anak_penilaian_persistem_asesmen_fungsi_melakukan_aktivitas_sehari_hari.value"
                                                        label="title"
                                                        placeholder="-- Kemampuan Melakukan Aktivitas Sehari-hari --"></v-select>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Aktivitas</label>
                                                    <v-select
                                                        v-model="form_inpatientChild.child_penilaian_persistem_asesmen_fungsi.aktivitas"
                                                        aria-readonly="true"
                                                        :options="option_anak_penilaian_persistem_asesmen_fungsi_aktivitas"
                                                        :reduce="option_anak_penilaian_persistem_asesmen_fungsi_aktivitas => option_anak_penilaian_persistem_asesmen_fungsi_aktivitas.value"
                                                        label="title"
                                                        placeholder="-- Aktivitas --"></v-select>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Ekstremitas Atas</label>
                                                    <v-select
                                                        v-model="option_anak_penilaian_persistem_asesmen_fungsi_ekstremitas_atas_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_penilaian_persistem_asesmen_fungsi_ekstremitas_atas"
                                                        :reduce="option_anak_penilaian_persistem_asesmen_fungsi_ekstremitas_atas => option_anak_penilaian_persistem_asesmen_fungsi_ekstremitas_atas.value"
                                                        label="title"
                                                        placeholder="-- Ekstremitas Atas --"></v-select>
                                                    <b-input-group
                                                        v-if="option_anak_penilaian_persistem_asesmen_fungsi_ekstremitas_atas_select === 6">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_penilaian_persistem_asesmen_fungsi.ekstremitas_atas"
                                                            placeholder="Isi Riwayat patah tulang..."></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label>Kemampuan Menggenggam</label>
                                                    <v-select
                                                        v-model="option_anak_penilaian_persistem_asesmen_fungsi_kemampuan_menggenggam_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_penilaian_persistem_asesmen_fungsi_kemampuan_menggenggam"
                                                        :reduce="option_anak_penilaian_persistem_asesmen_fungsi_kemampuan_menggenggam => option_anak_penilaian_persistem_asesmen_fungsi_kemampuan_menggenggam.value"
                                                        label="title"
                                                        placeholder="-- Kemampuan Menggenggam --"></v-select>
                                                    <b-input-group
                                                        v-if="option_anak_penilaian_persistem_asesmen_fungsi_kemampuan_menggenggam_select === 3">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_penilaian_persistem_asesmen_fungsi.kemampuan_menggenggam"
                                                            placeholder="Lainnya..."></b-form-input>
                                                    </b-input-group>
                                                    <b-input-group
                                                        v-if="option_anak_penilaian_persistem_asesmen_fungsi_kemampuan_menggenggam_select === 4">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_penilaian_persistem_asesmen_fungsi.kemampuan_menggenggam"
                                                            placeholder="Masalah menggenggam..."></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Alat Ambulasi</label>
                                                    <v-select
                                                        v-model="form_inpatientChild.child_penilaian_persistem_asesmen_fungsi.alat_ambulasi"
                                                        aria-readonly="true"
                                                        :options="option_anak_penilaian_persistem_asesmen_fungsi_alat_ambulasi"
                                                        :reduce="option_anak_penilaian_persistem_asesmen_fungsi_alat_ambulasi => option_anak_penilaian_persistem_asesmen_fungsi_alat_ambulasi.value"
                                                        label="title"
                                                        placeholder="-- Alat Ambulasi --"></v-select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col mt-32">
                                        <ModernLabel label="Keadaan mental dan psikologis" class="mb-8" />
                                        <div class="row mt-16 c-form">
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Status Mental</label>
                                                    <v-select
                                                        v-model="form_inpatientChild.child_penilaian_persistem_keadaan_mental_dan_psikologis.status_mental"
                                                        aria-readonly="true"
                                                        :options="option_anak_penilaian_persistem_keadaan_mental_psikologis_status_mental"
                                                        :reduce="option_anak_penilaian_persistem_keadaan_mental_psikologis_status_mental => option_anak_penilaian_persistem_keadaan_mental_psikologis_status_mental.value"
                                                        label="title"
                                                        placeholder="-- Status Mental --"></v-select>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Status Psikologi</label>
                                                    <v-select
                                                        v-model="option_anak_penilaian_persistem_keadaan_mental_psikologis_status_psikologis_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_penilaian_persistem_keadaan_mental_psikologis_status_psikologis"
                                                        :reduce="option_anak_penilaian_persistem_keadaan_mental_psikologis_status_psikologis => option_anak_penilaian_persistem_keadaan_mental_psikologis_status_psikologis.value"
                                                        label="title"
                                                        placeholder="-- Status Psikologis --"></v-select>
                                                    <b-input-group
                                                        v-if="option_anak_penilaian_persistem_keadaan_mental_psikologis_status_psikologis_select === 7">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_penilaian_persistem_keadaan_mental_dan_psikologis.status_psikologi"
                                                            placeholder="Lainnya..."></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col mt-32">
                                        <ModernLabel label="Eliminasi" class="mb-8" />
                                        <div class="row mt-16 c-form">
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Bab</label>
                                                    <v-select
                                                        v-model="option_anak_penilaian_persistem_eliminasi_bab_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_penilaian_persistem_eliminasi_bab"
                                                        :reduce="option_anak_penilaian_persistem_eliminasi_bab => option_anak_penilaian_persistem_eliminasi_bab.value"
                                                        label="title"
                                                        placeholder="-- Bab --"></v-select>
                                                    <b-input-group
                                                        v-if="option_anak_penilaian_persistem_eliminasi_bab_select === 7"
                                                        append="Frekuensi">
                                                        <b-form-input type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_penilaian_persistem_eliminasi.bab"
                                                            placeholder="Diare..."></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Bak</label>
                                                    <v-select
                                                        v-model="form_inpatientChild.child_penilaian_persistem_eliminasi.bak"
                                                        aria-readonly="true"
                                                        :options="option_anak_penilaian_persistem_eliminasi_bak"
                                                        :reduce="option_anak_penilaian_persistem_eliminasi_bak => option_anak_penilaian_persistem_eliminasi_bak.value"
                                                        label="title"
                                                        placeholder="-- Bak --"></v-select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col mt-32">
                                        <ModernLabel label="Seksual" class="mb-8" />
                                        <div class="row mt-16 c-form">
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label>Wanita,haid</label>
                                                    <v-select
                                                        v-model="form_inpatientChild.child_penilaian_persistem_seksual.wanita_haid"
                                                        aria-readonly="true"
                                                        :options="option_anak_penilaian_persistem_seksual_wanita_haid"
                                                        :reduce="option_anak_penilaian_persistem_seksual_wanita_haid => option_anak_penilaian_persistem_seksual_wanita_haid.value"
                                                        label="title"
                                                        placeholder="-- Wanita,haid --"></v-select>
                                                </div>
                                            </div>
                                            <div
                                                class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label>Laki-laki</label>
                                                    <v-select
                                                        v-model="option_anak_penilaian_persistem_seksual_laki_laki_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_penilaian_persistem_seksual_laki_laki"
                                                        :reduce="option_anak_penilaian_persistem_seksual_laki_laki => option_anak_penilaian_persistem_seksual_laki_laki.value"
                                                        label="title"
                                                        placeholder="-- Laki-laki --"></v-select>
                                                    <!-- Sirkumsisi laki-laki -->
                                                    <v-select
                                                        v-if="option_anak_penilaian_persistem_seksual_laki_laki_select === 1"
                                                        v-model="option_anak_penilaian_persistem_seksual_laki_laki_sirkumsisi_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_penilaian_persistem_seksual_laki_laki_sirkumsisi"
                                                        :reduce="option_anak_penilaian_persistem_seksual_laki_laki_sirkumsisi => option_anak_penilaian_persistem_seksual_laki_laki_sirkumsisi.value"
                                                        label="title"
                                                        placeholder="-- Sirkumsisi --"></v-select>
                                                    <!-- Masalah lain laki-laki -->
                                                    <v-select
                                                        v-if="option_anak_penilaian_persistem_seksual_laki_laki_select === 2"
                                                        v-model="option_anak_penilaian_persistem_seksual_laki_laki_masalah_lain_select"
                                                        aria-readonly="true"
                                                        :options="option_anak_penilaian_persistem_seksual_laki_laki_masalah_lain"
                                                        :reduce="option_anak_penilaian_persistem_seksual_laki_laki_masalah_lain => option_anak_penilaian_persistem_seksual_laki_laki_masalah_lain.value"
                                                        label="title"
                                                        placeholder="-- Masalah Lain --"></v-select>
                                                    <!-- sirkumsisi input -->
                                                    <b-input-group>
                                                        <b-form-input
                                                            v-if="option_anak_penilaian_persistem_seksual_laki_laki_sirkumsisi_select === 2"
                                                            type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_penilaian_persistem_seksual.laki_laki"
                                                            placeholder="Isi Sirkumsisi..."></b-form-input>
                                                        <!-- masalah lain input -->
                                                        <b-form-input
                                                            v-if="option_anak_penilaian_persistem_seksual_laki_laki_masalah_lain_select === 2"
                                                            type="text" autocomplete="off"
                                                            @change="saveChangedNurse($event)"
                                                            v-model="form_inpatientChild.child_penilaian_persistem_seksual.laki_laki"
                                                            placeholder="Isi Masalah Lain..."></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col mt-32"></div>
                                </div>
                            </div>
                        </section>
                    </div>
                </section>
            </template>

            <section v-if="this.view_condition.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e'" class="mt-20 mt-lg-32">
                <div class="card">
                    <div class="card-header text-white font-weight-bold bg-primary">PENGKAJIAN AWAL HEMODIALISA</div>
                    <div class="card-body">
                        <ModernLabel label="Preskripsi Hemodialisis" />
                        <section class="row mt-24">
                            <div class="col-lg-6">
                                <div>
                                    <label class="c-form-label">Tanggal HD Inisiasi</label>
                                    <b-form-input v-model="form_hemodialys.hemodialysis_date" type="date" />
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Jenis Hemodialisis</label>
                                    <b-form-radio-group v-model="form_hemodialys.hemodialysis_type_id">
                                        <b-form-radio class="my-1" value="1">Rutin</b-form-radio>
                                        <b-form-radio class="my-1" value="2">Cito</b-form-radio>
                                        <b-form-radio class="my-1" value="3">Ekstra</b-form-radio>
                                    </b-form-radio-group>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Frekuensi Hemodialisis</label>
                                    <b-form-radio-group v-model="form_hemodialys.frequency_type_id">
                                        <b-form-radio class="my-1" value="1">1x/minggu</b-form-radio>
                                        <b-form-radio class="my-1" value="2">2x/minggu</b-form-radio>
                                        <b-form-radio class="my-1" value="3">3x/minggu</b-form-radio>
                                        <b-form-radio class="my-1" value="4">Tidak Tentu</b-form-radio>
                                    </b-form-radio-group>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Jenis Dialisat</label>
                                    <b-form-radio-group v-model="form_hemodialys.dialysate_type_id" stacked class="w-100">
                                        <b-form-radio class="my-1" value="1">Asetat</b-form-radio>
                                        <b-form-radio class="my-1" value="2">Bikarbonat</b-form-radio>
                                        <b-form-radio class="my-1" value="3">
                                            <div>Suhu Dialisat</div>
                                            <div v-if="form_hemodialys.dialysate_type_id == 3">
                                                <b-input-group append="ᵒC">
                                                    <b-form-input v-model="form_hemodialys.dialysate_temperature" type="number" autocomplete="off"></b-form-input>
                                                </b-input-group>
                                            </div>
                                        </b-form-radio>
                                    </b-form-radio-group>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Akses Vaskuler</label>
                                    <b-form-radio-group v-model="form_hemodialys.vascular_type_id" stacked>
                                        <b-form-radio class="my-1" value="1">Cimino</b-form-radio>
                                        <b-form-radio class="my-1" value="2">Femoral</b-form-radio>
                                        <b-form-radio class="my-1" value="3">CDL Subciavia</b-form-radio>
                                        <b-form-radio class="my-1" value="4">
                                            <div>CDL Jugularis</div>
                                            <div v-if="form_hemodialys.vascular_type_id == 4">
                                                <b-form-input v-model="form_hemodialys.vascular_jugularis" autocomplete="off"></b-form-input>
                                            </div>
                                        </b-form-radio>
                                    </b-form-radio-group>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="">
                                    <label class="c-form-label">Lama HD</label>
                                    <b-input-group append="jam">
                                        <b-form-input v-model="form_hemodialys.long_hours" type="number" autocomplete="off"></b-form-input>
                                    </b-input-group>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Kec. Darah (QB)</label>
                                    <b-input-group append="Ml/mnt">
                                        <b-form-input v-model="form_hemodialys.blood_velocity" type="number" autocomplete="off"></b-form-input>
                                    </b-input-group>
                                </div>
                                <div class="mt-12">
                                    <label>Heparinisasi</label>
                                    <v-select v-model="form_hemodialys.heparin_type_id" aria-readonly="true" :options="options_heparin" :reduce="options_heparin => options_heparin.value" label="title" placeholder="-- Pilih --"></v-select>
                                </div>
                                <template v-if="form_hemodialys.heparin_type_id == 1">
                                    <div class="mt-12">
                                        <label class="c-form-label">Dosis Awal</label>
                                        <b-input-group append="IU">
                                            <b-form-input v-model="form_hemodialys.initial_dose" type="number" autocomplete="off"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Intermitten</label>
                                        <b-input-group append="IU">
                                            <b-form-input v-model="form_hemodialys.intermittent" type="number" autocomplete="off"></b-form-input>
                                        </b-input-group>
                                    </div>
                                </template>
                                <template v-if="form_hemodialys.heparin_type_id == 2">
                                    <div class="mt-12">
                                        <label class="c-form-label">Tanpa Heparin</label>
                                        <b-form-input v-model="form_hemodialys.without_heparin" autocomplete="off"></b-form-input>
                                    </div>
                                </template>
                                <div class="mt-12">
                                    <label class="c-form-label">UFG</label>
                                    <b-input-group append="ml">
                                        <b-form-input v-model="form_hemodialys.ufg" type="number" autocomplete="off"></b-form-input>
                                    </b-input-group>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Kec. Dialisit (QD)</label>
                                    <b-input-group append="ml/mnt">
                                        <b-form-input v-model="form_hemodialys.dialysate_velocity" type="number" autocomplete="off"></b-form-input>
                                    </b-input-group>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Maintenance</label>
                                    <b-input-group append="IU">
                                        <b-form-input v-model="form_hemodialys.maintenance" type="number" autocomplete="off"></b-form-input>
                                    </b-input-group>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">LMWH</label>
                                    <b-input-group append="IU">
                                        <b-form-input v-model="form_hemodialys.lmwh" type="number" autocomplete="off"></b-form-input>
                                    </b-input-group>
                                </div>
                            </div>
                            
                            <div class="col">
                                <div class="mt-12">
                                    <label class="c-form-label">Program Profiling</label>
                                    <b-form-radio-group v-model="form_hemodialys.profiling_type_id">
                                        <b-form-radio class="my-1 col-lg-3" value="1">
                                            <span>Ultrafiltrasi Mode</span>
                                            <span v-if="form_hemodialys.profiling_type_id == 1">
                                                <b-form-input v-model="form_hemodialys.ultrafiltration" autocomplete="off"></b-form-input>
                                            </span>
                                        </b-form-radio>
                                        <b-form-radio class="my-1 col-lg-3" value="2">
                                            <span>Natriuum Mode</span>
                                            <span v-if="form_hemodialys.profiling_type_id == 2">
                                                <b-form-input v-model="form_hemodialys.natrium" autocomplete="off"></b-form-input>
                                            </span>
                                        </b-form-radio>
                                        <b-form-radio class="my-1 col-lg-3" value="3">
                                            <span>Bikarbonat Mode</span>
                                            <span v-if="form_hemodialys.profiling_type_id == 3">
                                                <b-form-input v-model="form_hemodialys.bicarbonate" autocomplete="off"></b-form-input>
                                            </span>
                                        </b-form-radio>
                                    </b-form-radio-group>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Terapi</label>
                                    <b-textarea v-model="form_hemodialys.therapy" rows="3"></b-textarea>                                    
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Obat Rutin</label>
                                    <b-textarea v-model="form_hemodialys.medicine" rows="3"></b-textarea>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Injeksi</label>
                                    <b-textarea v-model="form_hemodialys.injection" rows="3"></b-textarea>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </section>

            <!-- DONT DELETE THIS COMMENTED CODE BELOW -->
            <!-- <section class="mt-20 mt-lg-32">
                <div class="card">
                    <div class="card-body">
                        <section id="studies-menu">
                            <div class="d-flex flex-wrap">
                                <li class="m-2">
                                    <a @click="goToTab('1')" size="sm" class="px-16 py-8 rounded-pill" :class="{ active: form_nurse.type_nurse_id === '1' }">
                                        <span class="text-semi-14-on">Pengkajian Awal Dewasa</span>
                                    </a>
                                </li>
                                <li class="m-2">
                                    <a @click="goToTab('2')" class="px-16 py-8 rounded-pill" :class="{ active: form_nurse.type_nurse_id === '2' }">
                                        <span class="text-semi-14-on">Pengkajian Awal Anak</span>
                                    </a>
                                </li>
                                <li class="m-2">
                                    <a @click="goToTab('3')" class="px-16 py-8 rounded-pill" :class="{ active: form_nurse.type_nurse_id === '3' }">
                                        <span class="text-semi-14-on">Pengkajian Awal Obgyn</span>
                                    </a>
                                </li>
                                <li class="m-2">
                                    <a @click="goToTab('4')" class="px-16 py-8 rounded-pill" :class="{ active: form_nurse.type_nurse_id === '4' }">
                                        <span class="text-semi-14-on">Pengkajian Awal IGD</span>
                                    </a>
                                </li>
                                <li class="m-2">
                                    <a @click="goToTab('5')" class="px-16 py-8 rounded-pill" :class="{ active: form_nurse.type_nurse_id === '5' }">
                                        <span class="text-semi-14-on">Triase</span>
                                    </a>
                                </li>
                                <li class="m-2">
                                    <a @click="goToTab('6')" class="px-16 py-8 rounded-pill" :class="{ active: form_nurse.type_nurse_id === '6' }">
                                        <span class="text-semi-14-on">Pengkajian Awal Rawat Inap Anak</span>
                                    </a>
                                </li>
                                <li class="m-2">
                                    <a @click="goToTab('7')" class="px-16 py-8 rounded-pill" :class="{ active: form_nurse.type_nurse_id === '7' }">
                                        <span class="text-semi-14-on">Pengkajian Awal Rawat Inap Dewasa</span>
                                    </a>
                                </li>
                                <li class="m-2">
                                    <a @click="goToTab('8')" class="px-16 py-8 rounded-pill" :class="{ active: form_nurse.type_nurse_id === '8' }">
                                        <span class="text-semi-14-on">Pengkajian Awal Rawat Inap Lansia</span>
                                    </a>
                                </li>
                                <li class="m-2">
                                    <a @click="goToTab('9')" class="px-16 py-8 rounded-pill" :class="{ active: form_nurse.type_nurse_id === '9' }">
                                        <span class="text-semi-14-on">Pengkajian Awal Fisioterapi</span>
                                    </a>
                                </li>
                                <li class="m-2">
                                    <a @click="goToTab('10')" class="px-16 py-8 rounded-pill" :class="{ active: form_nurse.type_nurse_id === '10' }">
                                        <span class="text-semi-14-on">Pengkajian SOAP</span>
                                    </a>
                                </li>
                                <li class="m-2">
                                    <a @click="goToTab('11')" class="px-16 py-8 rounded-pill" :class="{ active: form_nurse.type_nurse_id === '11' }">
                                        <span class="text-semi-14-on">SDKI SLKI SIKI</span>
                                    </a>
                                </li>
                            </div>
                        </section>
                        <section v-show="form_nurse.type_nurse_id == '1'" class="mt-32">
                            <nurse_adult :trigger_clear_subnurse="trigger_clear_subnurse" />
                        </section>
                        <section v-show="form_nurse.type_nurse_id == '2'" class="mt-32">
                            <nurse_child :trigger_clear_subnurse="trigger_clear_subnurse" />
                        </section>
                        <section v-show="form_nurse.type_nurse_id == '3'" class="mt-32">
                            <nurse_obgyn />
                        </section>
                        <section v-show="form_nurse.type_nurse_id == '4'" class="mt-32">
                            <nurse_IGD />
                        </section>
                        <section v-show="form_nurse.type_nurse_id == '5'" class="mt-32">
                            <nurse_triase />
                        </section>
                        <section v-show="form_nurse.type_nurse_id == '6'" class="mt-32">
                            <nurse_inpatient_child />
                        </section>
                        <section v-show="form_nurse.type_nurse_id == '7'" class="mt-32">
                            <nurse_inpatient_adult />
                        </section>
                        <section v-show="form_nurse.type_nurse_id == '8'" class="mt-32">
                            <nurse_inpatient_elderly />
                        </section>
                        <section v-show="form_nurse.type_nurse_id == '9'" class="mt-32">
                            <nurse_physiotherapy />
                        </section>
                        <section v-show="form_nurse.type_nurse_id == '10'" class="mt-32">
                            <nurse_SOAP />
                        </section>
                        <section v-show="form_nurse.type_nurse_id == '11'" class="mt-32">
                            <nurse_SDKI />
                        </section>
                    </div>
                </div>
            </section> -->
            
            <section class="px-3 mt-20 mt-lg-32">
                <div class="row mx-0 text-center text-lg-left">
                    <div class="col-12 col-lg-2">
                        <img src="@/@core/assets/images/nurse/img-doctor.jpg" alt="your-photo" class="rounded-circle shadow-sm c-img">
                    </div>
                    <div class="col-12 col-lg px-0 px-lg-3 mt-16 mt-lg-0 d-flex">
                        <div class="col d-flex flex-column justify-content-center">
                            <div>
                                <p class="text-regular-12 text-neutral-400">Nama Perawat</p>
                                <p class="text-bold-24">{{userData.employee_name}}</p>
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
                                <div class="col-lg-3 mt-8 mt-lg-0">
                                    <p class="text-regular-12 text-neutral-400">Poli</p>
                                    <p class="text-semi-16">{{ regData.health_facility_item_name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <div class="px-3 mt-40 d-flex justify-content-lg-end">
                <b-button @click.prevent="validationForm" :disabled="disabled.main" pill size="sm" variant="indigo" class="px-3">
                    <feather-icon icon="SaveIcon" /> Simpan
                </b-button>
            </div>
        </b-modal>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import vSelect from 'vue-select'
import ModernLabel from '@/@core/components/label/ModernLabel.vue'
import moment from 'moment'

import administration from '@/views/layouts/patient/doctor/administration.vue'
import patientMenu from '@/@core/components/tab-menu/patientMenu.vue'

export default {
    name: 'DoctorStudies',
    components: {
        patientMenu, administration, vSelect, ModernLabel, 
    },
    data() {
        return {
            dummy_condition: false,
            view_condition: {
                health_facility_id: '',
                health_facility_item_id: '',
                isAdult: '',
                isNeonatus: false,
                isChild: false,
            },
            isDisabled: false,
            active: {
                advance: {
                    antopometri: false,
                    // main_list: false,
                }
            },
            disabled: {
                transaction: false,
                main: false,
            },
            reg_des_id: '',
            userData: JSON.parse(localStorage.getItem('userData')),
            regData: {},
            nurseData: {},
            nurseResume:{},
            nurseSubResume:{},
            dateModal:moment(new Date()).format('YYYY/MM/DD HH:MM'),
            open_modal: {
                main_modal: false,
                nurse: false,
            },
            // trigger_clear_subnurse: '',
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

            form_transaction: {
                id: '',
                view: 'add',
                costing_id: '',
                nurse_id: '',
                notes: '',
                var_1: 1,
                var_2: '',
                var_3: ''
            },
            form_nurse: {
                id:'', 
                employee_nurse_id:'',
                registration_destination_id:'',
                type_nurse_id: '1',

                // Anamnesa
                anamesa_type_id: 1, 
                family_relation_id: '',
                family_name: '',
                main_complaint: '',
                history_disease_now: '',
                history_disease_past: '',

                // Tanda Vital
                systolic_blood_pressure: '', 
                diastolic_blood_pressure: '', 
                pulse_frequency: '', 
                breath_frequency: '', 
                oximetry_frequency: '', 
                temperature: '', 
                // blood_sugar: '',
                // sore_score: '',
                // ews: '',                
                // verbal_response: '',
                oxygen_type_id: '',
                aware: '',
                eye_score: '',
                verbal_score: '',
                motor_score: '',
                // pupil_score: '',

                // Antopometri
                weight:  '',
                height: '',
                imt: '',
                head_circumference: '',
                chest_circumference: '',
                arm_circumference: '',
                stomach_circumference: '',
                knee_circumference: '',

                // Fungsional
                aids: '',
                prothesa: '',
                disabled: '',
                adl_type_id: '',
            },
            form_igd: {
                id: '',
                nurse_id: '',
                been_hospitalized_type_id: 0,
                been_hospitalized_answer: '',
                any_history_family_disease_type_id: 0,
                any_history_family_answer_arry: [],
                any_history_family_answer: '',
                any_surgery: '',
                any_surgery_date: '',
                complication: '',
                history_allergy_type_id: 0,
                history_allergy: '',

                history_psychological_arry: [],

                body_mass_index: '',
                patient_food_intake: '',
                patient_certain_diets: '',
                six_month_lose_weight: '',
                lose_weight: '',
                patient_disease_condition: '',
                nutrition_total_score: '0',

                fall_risk_method_type_id: '',
                fall_risk_type_id: '',

                nursing_diagnoses_arry: [],
                nursing_diagnoses: '',
                implementation_arry: [],
                implementation: '',

                any_surgery_type_id: 0,
                fall_risk_component_a: 0,
                fall_risk_component_b: 0,
            },
            options_body_mass:[
                { value: 1, text: '18,5-22.9' },
                { value: 2, text: '17-18.4 / 23-24.9' },
                { value: 3, text: '<17 / >=25' },
            ],
            options_six_months:[
                { value: 1, text: 'Tidak' },
                { value: 2, text: 'Tidak Yakin' },
                { value: 3, text: 'Ya' },
            ],
            options_lose_weight:[
                { value: 1, text: '1 - 5 kg' },
                { value: 2, text: '6 - 10 kg' },
                { value: 3, text: '11 - 15 kg' },
                { value: 4, text: '> 15 kg' },
                { value: 5, text: 'Tidak Tahu Berapa kg' },
            ],
            options_food_intake:[
                { value: 1, text: 'Baik' },
                { value: 2, text: 'Kurang' },
                { value: 3, text: 'Kurang Semangat' },
            ],
            options_disease_condition:[
                { value: 1, text: 'Tidak' },
                { value: 2, text: 'Ya' },
            ],
            options_certain_diets:[
                { value: 1, text: 'Tidak' },
                { value: 2, text: 'Ya' },
            ],

            // Ranap dewasa
            isAdult: false,
            ToolFields: [
                { key: 'no', label: 'No', thClass: 'text-center', thStyle: 'width:60px' },
                { key: 'type', label: 'Jenis' },    
                { key: 'explanation', label: 'Keterangan' },    
                { key: 'action', label: 'Aksi', thStyle: 'width:200px' },    
            ],
            ToolItems: [
                { type: 'Lorem Ipsum', notes: 'Lorem Ipsum' },
                { type: 'Lorem Ipsum', notes: 'Lorem Ipsum' },
            ],
            CheckedFields: [
                { key: 'no', label: 'No', thClass: 'text-center', thStyle: 'width:60px' },
                { key: 'type', label: 'Jenis Pemeriksaan' },    
                { key: 'page', label: 'Lembar' },    
                { key: 'explanation', label: 'Keterangan' },    
                { key: 'action', label: 'Aksi', thStyle: 'width:200px' },    
            ],
            CheckedItems: [
                { type: 'Lorem Ipsum', sheet: 'Lorem Ipsum', notes: 'Lorem Ipsum' },
                { type: 'Lorem Ipsum', sheet: 'Lorem Ipsum', notes: 'Lorem Ipsum' },
            ],
            form_inpatientAdult: {
                id: '',
                nurse_id: "",
                psychological_status_arry: [],
                psychological_status: "",
                mental_status_arry: [],
                mental_status_problems: "",
                mental_status_violence: "",
                elimination_defecate: "",
                elimination_urination: "",
                elimination_consistency_type_id: 0,
                elimination_color_type_id: 0,
                elimination_color: "",

                // sore_complaints_type_id: 0,
                // sore_complaints: "",
                // sore_goes_away_arry: [],
                // sore_goes_away: "",
                // sore_method_type_id: 0,
                // face_score: 0,
                // legs_score: 0,
                // activity_score: 0,
                // cry_score: 0,
                // consolability_score: 0,
                // tell_doctor_type_id: 0,
                // tell_doctor_date: "",
                // sore_total_score: "",
                // duration: "",
                // location: "",
                // sore_quality_arry: [],
                // frequency: "",
                // wong_baker_score: 0, // belom ada
                // nrs_score: "", // belom ada
                // next_nrs_score: [], // belom ada
                // sore_affects_arry: [],
                // sore_affects: "",

                care_plan_diet_nutrition_type_id: 0,
                care_plan_diet_nutrition: "",
                care_plan_rehabilitation_type_id: 0,
                care_plan_rehabilitation: "", 
                care_plan_wound_type_id: "",
                care_plan_wound: "",
                care_plan_pain_management_type_id: "",
                care_plan_pain_management: "",
                care_plan_other: "",
                evaluation_smoke_type_id: 0,
                evaluation_smoke_amount: "",
                evaluation_smoke_long: "",
                evaluation_alcohol_type_id: 0,
                evaluation_alcohol_type: "",
                evaluation_alcohol_amount: "",
                evaluation_sedative_type_id: 0,
                evaluation_sedative_type: "",
                evaluation_sedative_amount: "",
                evaluation_sleep_type_id: 0,
                evaluation_sleep: "",
                evaluation_daily_type_id: 0,
                evaluation_activity_type_id: 0,
                evaluation_extremities_top_type_id: 0,
                evaluation_grip_type_id: 0,
                evaluation_grip_other: "",
                evaluation_coordination_type_id: 0,
                evaluation_coordination_other: "",
                evaluation_extremities_bottom_type_id: 0,
                evaluation_extremities_bottom_bone: "",
                evaluation_ambulation_type_id: 0,

                medical_devices_type_id: 0,
                medical_devices: [],

                discharge_treatment_duration: '',
                discharge_further_care_arry: [],
                examination_history: [],

            },
            input_medical: {
                type: '',
                notes: '',
            },
            input_examination: {
                type: '',
                page: '',
                explanation: '',
            },

            form_hemodialys: {
                id: '',
                registration_destination_id: "",
                nurse_id: "",
                hemodialysis_date: "",
                hemodialysis_type_id: 0,
                frequency_type_id: 0,
                dialysate_type_id: 0,
                vascular_type_id: 0,
                long_hours: '',
                blood_velocity: "",
                initial_dose: "",
                intermittent: "",
                ufg: "",
                dialysate_velocity: "",
                maintenance: "",
                lmwh: "",
                dialysate_temperature: "",
                vascular_jugularis: "",
                heparin_type_id: "",
                without_heparin: "",
                profiling_type_id: 0,
                ultrafiltration: "",
                natrium: "",
                bicarbonate: "",
                therapy: "",
                medicine: "",
                injection: "",
            },
            options_heparin:[
                { value: 1, title: 'Heparin' },
                { value: 2, title: 'Tanpa Heparin' },
            ],

            //ranap neonatus property
            form_inpatientNeonatus: {
                id: '',
                register_destination_id: '',
                nurse_id: '',
                neonatus_riwayat_kesehatan_bayi_ibu: {
                    hasil_laboratorium_ibu: '',
                    golongan_darah_ibu: '',
                    riwayat_persalinan: '',
                    riwayat_kelahiran: '',
                    jenis_kelamin_bayi: '',
                    pemberian_obat_setelah_lahir: '',
                    diagnosa_partus: '',
                    riwayat_penyakit_ibu_setelah_hamil: '',
                    riwayat_kehamilan: '',
                },
                neonatus_kebutuhan_edukasi_orang_tua: {
                    bicara: '',
                    bahasa_sehari_hari: '',
                    perlu_penerjemah: '',
                    tingkat_pendidikan: '',
                    keluarga_menginginkan_informasi_tentang_proses_penyakit: '',
                },
                neonatus_spiritual: {
                    agama: '',
                    permintaan_khusus_terkait_dengan_adat: ''
                },
                neonatus_sosial_budaya: {
                    suku_bangsa: '',
                    warga_negara: '',
                    tempat_tinggal: '',
                    yang_merawat_bayi: '',
                    orang_tua: '',
                    berkunjung: '',
                    menyentuh: '',
                    menggendong: '',
                    kontak_mata: '',
                    berbicara: '',
                    lainnya: '',
                },
                neonatus_paru: {
                    pergerakan_paru: '',
                    frekuensi_pernapasan_paru: '',
                    retraksi: '',
                    nch: '',
                    grunting: '',
                    dyspnoe: '',
                    tachynoe: '',
                    gargling_napas_tambahan: '',
                    snooring_napas_tambahan: '',
                    stridor_napas_tambahan: '',
                    peruksi_paru_paru: '',
                    ronkhi_paru_kanan: '',
                    wheezing_paru_kanan: '',
                    lainnya_paru_kanan: '',
                    perkusi_paru_kiri: '',
                    ronkhi_paru_kiri: '',
                    wheezing_paru_kiri: '',
                    lainnya_paru_kiri: '',
                },
                neonatus_down_score: {
                    frekuensi_napas: '',
                    retraksi: '',
                    sianosis: '',
                    air_entry: '',
                    merintih: ''
                },
                neonatus_eliminasi: {
                    anus: '',
                    defaksi_anus: '',
                    defaksi_stoma: '',
                    frekuensi_defaksi: '',
                    konsistensi_defaksi: '',
                    karakteristik_fases: '',
                    urin: '',
                    diuresis: ''
                },
                neonatus_genitalia: {
                    jenis_kelamin: '',
                    kelainan: '',
                    testis_laki_laki: '',
                    penis_laki_laki: '',
                    labio_perempuan: '',
                    lainnya: ''
                },
                neonatus_apgar_score: {
                    menit_ke: '',
                    denyut_jantung: '',
                    usaha_napas: '',
                    tonus_otot: '',
                    refleks: ''
                },
                neonatus_kardiologi: {
                    kelainan_jantung: '',
                    denyut_jantung: '',
                    type_denyut_jantung: '',
                    murmur: '',
                    keterangan_murmur: '',
                    gallop: '',
                    keterangan_gallop: '',
                    sianosis: '',
                    pucat: '',
                    crt: '',
                    akral: ''
                },
                neonatus_skala_nyeri: {
                    nama: '',
                    skor: ''
                },
                neonatus_trauma_kulit: {
                    usia_gestasi: '',
                    status_mental: '',
                    mobilisasi: '',
                    aktivitas: '',
                    nutrisi: '',
                    kelembaban: ''
                },
                neonatus_alergi_reaksi: {
                    makanan: '',
                    makanan_reaksi: '',
                    obat: '',
                    obat_reaksi: '',
                    lainnya: '',
                    lainnya_reaksi: '',
                    klip_tanda_alergi: '',
                    beritahu_dokter: '',
                    beritahu_farmasi: '',
                    beritahu_dietisien: ''
                },
                neonatus_muskuloskeletal: {
                    tonus: '',
                    kelainan_tulang: '',
                    gerakan_bayi: ''
                },
                neonatus_penanggung_jawab: {
                    wali: '',
                    nama: '',
                    usia: '',
                    perkawinan_ke: '',
                    pekerjaan: '',
                    pendidikan_terakhir: '',
                    alamat: '',
                    telepon: '',
                    penghasilan_perbulan: ''
                },
                neonatus_gastrointesnital: {
                    stomatitis: '',
                    labio_palatochiziz: '',
                    mukosa_kering: '',
                    pendarahan_gusi: '',
                    muntah: '',
                    frekuensi_muntah: '',
                    residu: '',
                    asites: '',
                    bising_usus: '',
                    nyeri_tekan: '',
                    limpa: '',
                    hepar: '',
                    lokasi_nyeri_tekan: ''
                },
            },
            //ranap child property
            form_inpatientChild: {
                id: '',
                register_destination_id: '',
                nurse_id: '',
                //umum
                child_riwayat_kesehatan_dahulu: {
                    jenis_persalinan: '',
                    keterangan_lahir: '',
                    lingkar_kepala: '',
                    berat_badan_lahir: '',
                    tumbuh_kembang: '',
                    lila: '',
                    tinggi_badan_lahir: '',
                    tinggi_badan: '',
                    imt: '',
                    status_gizi: '',
                    berat_badan: '',
                    perawakan: '',
                },
                child_riwayat_tumbuh_kembang: {
                    bb_anak_saat_lahir: '',
                    pb_anak_saat_lahir:'',
                    asi_sampai_umur: '',
                    susu_formula: '',
                    makanan_padat: '',
                    makanan_tambahan: '',
                    tengkurap: '',
                    duduk: '',
                    merangkak: '',
                    berdiri: '',
                    berjalan:'',
                },
                // child_riwayat_imunisasi: {
                //     nama_vaksin: [],
                //     vaksin_select: '',
                //     qty: ''
                // },
                //pemeriksaan fisik
                child_pemeriksaan_fisik_ttv: {
                    tekanan_darah: '',
                    suhu: '',
                    nadi: '',
                    kesadaran: ''
                },
                child_pemeriksaan_fisik_berat_badan: {
                    berat_badan: '',
                    tinggi_badan: '',
                    lla: ''
                },
                child_pemeriksaan_fisik_neorologi:  {
                    penglihatan: '',
                    menggunakan_alat_bantu_penglihatan: '',
                    pendengaran: '',
                    bicara: '',
                    sensorik: '',
                    motorik: '',
                    kekuatan_otot: '',
                    menggunakan_alat_bantu_pendengaran: ''
                },
                child_pemeriksaan_fisik_integument: {
                    warna_kulit: '',
                    tugor: '',
                    kulit: '',
                    kriteria_resiko_dekubitus: ''
                },
                child_pemeriksaan_fisik_kardiovaskuler: {
                    warna_kulit: '',
                    denyut_nadi: '',
                    sirkulasi: '',
                    nyeri_dada: '',
                    pulasi: ''
                },
                child_pemeriksaan_fisik_respirasi: {
                    pola_nafas: '',
                    irama_nafas: '',
                    batuk: '',
                    volume_pernafasan: '',
                    kesulitan_bernafas: '',
                    jenis_pernafasan: ''
                },
                child_pemeriksaan_fisik_gastrointestinal: {
                    mulut: '',
                    gigi: '',
                    lidah: '',
                    tenggorokan: '',
                    abdomen: ''
                },
                //penilaian persistem
                child_penilaian_persistem_umum: {
                    apakah_ada_nyeri: '',
                    riwayat_minuman_keras: '',
                    riwayat_penggunaan_obat_penenang: '',
                    riwayat_merokok: '',
                    pola_istirahat: ''
                },
                child_penilaian_persistem_asesmen_fungsi: {
                    kemampuan_melakukan_aktivitas_sehari: '',
                    ekstremitas_atas: '',
                    alat_ambulasi: '',
                    aktivitas: '',
                    kemampuan_menggenggam: ''
                },
                child_penilaian_persistem_keadaan_mental_dan_psikologis: {
                    status_mental: '',
                    status_psikologi: ''
                },
                child_penilaian_persistem_eliminasi: {
                    bab: '',
                    bak: ''
                },
                child_penilaian_persistem_seksual: {
                    wanita_haid: '',
                    laki_laki: ''
                }
            },
            //option neonatus
            OptionsNeonatusKebutuhanEdukasiOrangTuaBicara: [
                { value: 1, title: 'Normal', },
                { value: 2, title: 'Gangguan', },
            ],
            OptionsNeonatusKebutuhanEdukasiOrangTuaBahasaSehariHari: [
                { value: 1, title: 'Indonesia', },
                { value: 2, title: 'Inggris', },
                { value: 3, title: 'Daerah', },
                { value: 4, title: 'Lainnya', },
            ],
            OptionsNeonatusKebutuhanEdukasiOrangTuaBahasaPerluPenerjemah: [
                { value: 1, title: 'Ya', },
                { value: 2, title: 'Tidak', },
            ],
            OptionsNeonatusKebutuhanEdukasiOrangTuaBahasaTingkatPendidikan: [
                { value: 1, title: 'SD', },
                { value: 2, title: 'SMP', },
                { value: 3, title: 'SMA', },
                { value: 4, title: 'Akademi', },
                { value: 5, title: 'Lainnya', },
            ],
            OptionsNeonatusSpiritualAgama: [
                { value: 1, title: 'Islam', },
                { value: 2, title: 'Kristen', },
                { value: 3, title: 'Katolik', },
                { value: 4, title: 'Hindu', },
                { value: 5, title: 'Budha', },
                { value: 6, title: 'Lainnya', },
            ],
            OptionsNeonatusHasilLabIbu: [
                { value: 1, title: 'HB', },
                { value: 2, title: 'HbsAg', },
                { value: 3, title: 'Leukosit' },
                { value: 4, title: 'Lainnya' },
            ],
            OptionsNeonatusHasilLabIbuSelect: 0,
            OptionsNeonatusGolonganDarahIbu: [
                { value: 1, title: 'A', },
                { value: 2, title: 'B', },
                { value: 3, title: 'O' },
                { value: 4, title: 'AB' },
                { value: 5, title: 'Rhesus' },
            ],
            OptionsNeonatusRiwayatPersalinan: [
                { value: 1, title: 'Spontan', },
                { value: 2, title: 'Vakum/Forcep', },
                { value: 3, title: 'SC' },
            ],
            OptionsNeonatusRiwayatKelahiran: [
                { value: 1, title: 'Tunggal', },
                { value: 2, title: 'Kembar', },
                { value: 3, title: 'LK' },
                { value: 4, title: 'LD' },
                { value: 5, title: 'Anus' },
            ],
            OptionsNeonatusJenisKelaminBayi: [
                { value: 1, title: 'Laki-laki', },
                { value: 2, title: 'Perempuan', },
            ],
            OptionsNeonatusPemberianObatSetelahLahir: [
                { value: 1, title: 'Vit K', },
                { value: 2, title: 'Salep Mata', },
                { value: 3, title: 'Lainnya' },
            ],
            OptionsNeonatusPemberianObatSetelahLahirSelect: 0,
            OptionRiwayatKehamilan: [
                { value: 1, title: 'Usia gestasi', },
                { value: 2, title: 'Usia koreksi', },
            ],
            OptionRiwayatKehamilanSelect: 0,
            OptionsNeonatusTempatTinggalBayi: [
                { value: 1, title: 'Penitipan Anak', },
                { value: 2, title: 'Pengasuh', },
                { value: 3, title: 'Rumah' },
            ],
            OptionsNeonatusYangMerawatBayi: [
                { value: 1, title: 'Ayah', },
                { value: 2, title: 'Ibu', },
                { value: 3, title: 'Nenek' },
                { value: 4, title: 'Pengasuh' },
            ],
            OptionsNeonatusSosialBudayaTwoOption: [
                { value: 1, title: 'Ya', },
                { value: 2, title: 'Tidak', },
            ],
            //option anak
            option_anak_pemeriksaan_fisik_neorologi_penglihatan: [
                { value: 1, title: 'Tidak ada kelainan' },
                { value: 2, title: 'Gangguan Penglihatan' },
            ],
            option_anak_pemeriksaan_fisik_neorologi_menggunakan_alat_bantu_penglihatan: [
                { value: 1, title: 'Tidak' },
                { value: 2, title: 'Ya' },
            ],
            option_anak_pemeriksaan_fisik_neorologi_menggunakan_alat_bantu_penglihatan_select: 0,
            option_anak_pemeriksaan_fisik_neorologi_menggunakan_alat_bantu_penglihatan_ya: [
                { value: 1, title: 'Mata Palsu' },
                { value: 2, title: 'Kaca Mata' },
                { value: 3, title: 'Lensa Kontak' },
            ],
            option_anak_pemeriksaan_fisik_neorologi_pendengaran: [
                { value: 1, title: 'Tidak ada kelainan' },
                { value: 2, title: 'Tuli' },
                { value: 3, title: 'Berdengung' },
                { value: 4, title: 'Nyeri' },
                { value: 5, title: 'Keluar Cairan' },
                { value: 6, title: 'Lain-lain...' },
            ],
            option_anak_pemeriksaan_fisik_neorologi_pendengaran_select: 0,
            option_anak_pemeriksaan_fisik_neorologi_menggunakan_alat_bantu_pendengaran: [
                { value: 1, title: 'Tidak' },
                { value: 2, title: 'Ya' },
            ],
            option_anak_pemeriksaan_fisik_neorologi_bicara_artikulasi: [
                { value: 1, title: 'Jelas' },
                { value: 2, title: 'Tidak,sebutkan' },
            ],
            option_anak_pemeriksaan_fisik_neorologi_bicara_artikulasi_select: 0,
            option_anak_pemeriksaan_fisik_neorologi_sensorik: [
                { value: 1, title: 'Tidak ada kelainan' },
                { value: 2, title: 'Nyeri' },
                { value: 3, title: 'Rasa kebas' },
                { value: 4, title: 'Lainnya...' },
            ],
            option_anak_pemeriksaan_fisik_neorologi_sensorik_select: 0,
            option_anak_pemeriksaan_fisik_neorologi_motorik: [
                { value: 1, title: 'Hemiparase' },
                { value: 2, title: 'Tetrapase' },
                { value: 3, title: 'Tremor' },
                { value: 4, title: 'Tidak ada kelainan' },
                { value: 5, title: 'Lainnya...' },
            ],
            option_anak_pemeriksaan_fisik_neorologi_motorik_select: 0,
            option_anak_pemeriksaan_fisik_neorologi_kekuatan_otot: [
                { value: 1, title: 'Kuat' },
                { value: 2, title: 'Lemah' },
            ],
            //pemeriksaan fisik:integument
            option_anak_pemeriksaan_fisik_integument_warna_kulit: [
                { value: 1, title: 'Pucat' },
                { value: 2, title: 'Kemerahan' },
                { value: 3, title: 'Normal' },
            ],
            option_anak_pemeriksaan_fisik_integument_tugor: [
                { value: 1, title: 'Baik' },
                { value: 2, title: 'Sedang' },
                { value: 3, title: 'Buruk' },
                { value: 4, title: 'Lesi' },
                { value: 5, title: 'Lain-lain...' },
            ],
            option_anak_pemeriksaan_fisik_integument_tugor_select: 0,
            option_anak_pemeriksaan_fisik_integument_kulit: [
                { value: 1, title: 'Normal' },
                { value: 2, title: 'Kemerahan' },
                { value: 3, title: 'Bula' },
                { value: 4, title: 'Memar' },
                { value: 5, title: 'Ptekie' },
                { value: 6, title: 'Lainnya-lainnya...' },
            ],
            option_anak_pemeriksaan_fisik_integument_kulit_select: 0,
            option_anak_pemeriksaan_fisik_integument_kriteria_resiko_dekubitus: [
                { value: 1, title: 'Dirawat di intensive care' },
                { value: 2, title: 'Inkontinensia uri/alvi' },
                { value: 3, title: 'Penyakit Kronis (DM,CHF,CKD)' },
                { value: 4, title: 'Pasien immobilisasi' },
                { value: 5, title: 'Obesitas/cachexia' },
                { value: 6, title: 'Paraplegi/vegetatif stage' },
            ],
            //pemeriksaan fisik:kardiovaskuler
            option_anak_pemeriksaan_fisik_kardiovaskuler_warna_kulit: [
                { value: 1, title: 'Nomral' },
                { value: 2, title: 'Kemerahan' },
                { value: 3, title: 'Sianosis' },
                { value: 4, title: 'Pucat' },
                { value: 5, title: 'Lain-lain' },
            ],
            option_anak_pemeriksaan_fisik_kardiovaskuler_warna_kulit_select: 0,
            option_anak_pemeriksaan_fisik_kardiovaskuler_nyeri_dada: [
                { value: 1, title: 'Tidak' },
                { value: 2, title: 'Ya,sebutkan' },
            ],
            option_anak_pemeriksaan_fisik_kardiovaskuler_nyeri_dada_select: 0,
            option_anak_pemeriksaan_fisik_kardiovaskuler_denyut_nadi: [
                { value: 1, title: 'Teratur' },
                { value: 2, title: 'Tidak teratur' },
            ],
            option_anak_pemeriksaan_fisik_kardiovaskuler_sirkulasi: [
                { value: 1, title: 'Akral hangat' },
                { value: 2, title: 'Akral dingin' },
                { value: 3, title: 'Tachipnea' },
                { value: 4, title: 'Palpitasi' },
                { value: 5, title: 'Edema,lokasi...' },
            ],
            option_anak_pemeriksaan_fisik_kardiovaskuler_sirkulasi_select: 0,
            option_anak_pemeriksaan_fisik_kardiovaskuler_pulasi: [
                { value: 1, title: 'Kuat' },
                { value: 2, title: 'Lemah' },
                { value: 3, title: 'Lain-lain...' },
            ],
            option_anak_pemeriksaan_fisik_kardiovaskuler_pulasi_select: 0,
            //pemeriksaan fisik:respirasi
            option_anak_pemeriksaan_fisik_respirasi_pola_nafas: [
                { value: 1, title: 'Normal' },
                { value: 2, title: 'Bradipnea' },
                { value: 3, title: 'Tachpnea' },
            ],
            option_anak_pemeriksaan_fisik_respirasi_volume_pernafasan: [
                { value: 1, title: 'Normal' },
                { value: 2, title: 'Hiperventilasi' },
                { value: 3, title: 'Hipoventilasi' },
            ],
            option_anak_pemeriksaan_fisik_respirasi_jenis_pernafasan: [
                { value: 1, title: 'Pernafasan dada' },
                { value: 2, title: 'Pernafasan perut' },
                { value: 3, title: 'Menggunakan alat bantu nafas,sebutkan...' },
            ],
            option_anak_pemeriksaan_fisik_respirasi_jenis_pernafasan_select: 0,
            option_anak_pemeriksaan_fisik_respirasi_irama_nafas: [
                { value: 1, title: 'Teratur' },
                { value: 2, title: 'Tidak teratur' },
            ],
            option_anak_pemeriksaan_fisik_respirasi_kesulitan_bernafas: [
                { value: 1, title: 'Tidak' },
                { value: 2, title: 'Ya' },
                { value: 3, title: 'Lain-lain...' },
            ],
            option_anak_pemeriksaan_fisik_respirasi_kesulitan_bernafas_ya: [
                { value: 1, title: 'Dyspenia' },
                { value: 2, title: 'Orthopnia' },
            ],
            option_anak_pemeriksaan_fisik_respirasi_kesulitan_bernafas_select: 0,
            option_anak_pemeriksaan_fisik_respirasi_batuk: [
                { value: 1, title: 'Tidak' },
                { value: 2, title: 'Ya' },
                { value: 3, title: 'Hemoptoe' },
            ],
            option_anak_pemeriksaan_fisik_respirasi_batuk_ya: [
                { value: 1, title: 'Berdahak' },
                { value: 2, title: 'Tidak berdahak' },
            ],
            option_anak_pemeriksaan_fisik_respirasi_batuk_select: 0,
            //pemeriksaan fisik:gastrointestinal
            option_anak_pemeriksaan_fisik_gastrointestinal_mulut: [
                { value: 1, title: 'Tidak ada kelainan' },
                { value: 2, title: 'Simetris' },
                { value: 3, title: 'Asimetris' },
                { value: 4, title: 'Bibir pucat' },
                { value: 5, title: 'Mukosa mulut kering' },
                { value: 6, title: 'Sariawan' },
                { value: 7, title: 'Lain-lain...' },
            ],
            option_anak_pemeriksaan_fisik_gastrointestinal_mulut_select: 0,
            option_anak_pemeriksaan_fisik_gastrointestinal_gigi: [
                { value: 1, title: 'Tidak ada kelainan' },
                { value: 2, title: 'Caries' },
                { value: 3, title: 'Goyang' },
                { value: 4, title: 'Berlubang' },
                { value: 5, title: 'Gigi palsu' },
                { value: 6, title: 'Tambal' },
                { value: 7, title: 'Lain-lain' },
            ],
            option_anak_pemeriksaan_fisik_gastrointestinal_gigi_select: 0,
            option_anak_pemeriksaan_fisik_gastrointestinal_lidah: [
                { value: 1, title: 'Tidak ada kelainan' },
                { value: 2, title: 'Kotor' },
                { value: 3, title: 'Gerakan asimetris' },
                { value: 4, title: 'Lain-lain' },
            ],
            option_anak_pemeriksaan_fisik_gastrointestinal_lidah_select: 0,
            option_anak_pemeriksaan_fisik_gastrointestinal_tenggorokan: [
                { value: 1, title: 'Gangguan menelan' },
                { value: 2, title: 'Sakit menelan' },
                { value: 3, title: 'Lain-lain...' },
            ],
            option_anak_pemeriksaan_fisik_gastrointestinal_tenggorokan_select: 0,
            option_anak_pemeriksaan_fisik_gastrointestinal_abdomen: [
                { value: 1, title: 'Supel' },
                { value: 2, title: 'Asites' },
                { value: 3, title: 'Tegang' },
                { value: 4, title: 'Lain-lain...' },
            ],
            option_anak_pemeriksaan_fisik_gastrointestinal_abdomen_select: 0,
            //end option pemeriksaan fisik
            //option anak
            //penilaian persistem:umum
            option_anak_penilaian_persistem_umum_kenyamanan: [
                { value: 1, title: 'Tidak' },
                { value: 2, title: 'Ya (Lakukan pengkajian nyeri)' },
            ],
            option_anak_penilaian_persistem_umum_riwayat_merokok: [
                { value: 1, title: 'Tidak' },
                { value: 2, title: 'Ya' },
            ],
            option_anak_penilaian_persistem_umum_riwayat_merokok_select: 0,
            option_anak_penilaian_persistem_umum_riwayat_minuman_keras: [
                { value: 1, title: 'Tidak' },
                { value: 2, title: 'Ya' },
            ],
            option_anak_penilaian_persistem_umum_riwayat_minuman_keras_select: 0,
            option_anak_penilaian_persistem_umum_riwayat_penggunaan_obat_penenang: [
                { value: 1, title: 'Tidak' },
                { value: 2, title: 'Ya' },

            ],
            option_anak_penilaian_persistem_umum_riwayat_penggunaan_obat_penenang_select: 0,
            option_anak_penilaian_persistem_umum_pola_istirahat_tidur: [
                { value: 1, title: 'Teratur' },
                { value: 2, title: 'Tidak' },

            ],
            //penilaian persistem:asesmen fungsi
            option_anak_penilaian_persistem_asesmen_fungsi_melakukan_aktivitas_sehari_hari: [
                { value: 1, title: 'Mandiri' },
                { value: 2, title: 'Bantuan sebagian' },
                { value: 3, title: 'Duduk' },
                { value: 4, title: 'Bantuan mininal' },
                { value: 5, title: 'Ketergantungan total' },
                { value: 6, title: 'Berjalan' },

            ],
            option_anak_penilaian_persistem_asesmen_fungsi_aktivitas: [
                { value: 1, title: 'Tirah baring' },
                { value: 2, title: 'Duduk' },
                { value: 3, title: 'Berjalan' },
            ],
            option_anak_penilaian_persistem_asesmen_fungsi_kemampuan_menggenggam: [
                { value: 1, title: 'Tidak ada kesulitan' },
                { value: 2, title: 'Terkilir' },
                { value: 3, title: 'Lain-lain...' },
                { value: 4, title: 'Ada masalah...' },
            ],
            option_anak_penilaian_persistem_asesmen_fungsi_kemampuan_menggenggam_select: 0,
            option_anak_penilaian_persistem_asesmen_fungsi_ekstremitas_atas: [
                { value: 1, title: 'Tidak ada kesulitan' },
                { value: 2, title: 'Paralisis' },
                { value: 3, title: 'Defomitas' },
                { value: 4, title: 'Penurunan kekuatan range of motion' },
                { value: 5, title: 'Hilang keseimbangan' },
                { value: 6, title: 'Riwayat patah tulang' },
                { value: 7, title: 'Sering jatuh' },
                { value: 8, title: 'Lemah' },
            ],
            option_anak_penilaian_persistem_asesmen_fungsi_ekstremitas_atas_select: 0,
            option_anak_penilaian_persistem_asesmen_fungsi_alat_ambulasi: [
                { value: 1, title: 'Walker' },
                { value: 2, title: 'Tongkat' },
                { value: 3, title: 'Kursi roda' },
            ],
            //penilaian persistem:keadaan mental dan psikologis
            option_anak_penilaian_persistem_keadaan_mental_psikologis_status_mental: [
                { value: 1, title: 'Normal' },
                { value: 2, title: 'Konstipasi' },
                { value: 3, title: 'Agltasi' },
                { value: 4, title: 'Koorperatif' },
                { value: 5, title: 'Letargi' },
                { value: 6, title: 'Marah' },
                { value: 7, title: 'Disorientasi' },
                { value: 8, title: 'Orang' },
                { value: 9, title: 'Tempat' },
                { value: 10, title: 'Waktu' },
                { value: 11, title: 'Kejang tipe' },
            ],
            option_anak_penilaian_persistem_keadaan_mental_psikologis_status_psikologis: [
                { value: 1, title: 'Tenang' },
                { value: 2, title: 'Cemas' },
                { value: 3, title: 'Depresi' },
                { value: 4, title: 'Menyerang' },
                { value: 5, title: 'Hiperaktif' },
                { value: 6, title: 'Gangguan Sekitar' },
                { value: 7, title: 'Lain-lain...' },
            ],
            option_anak_penilaian_persistem_keadaan_mental_psikologis_status_psikologis_select: 0,
            //penilaian persistem:eliminasi
            option_anak_penilaian_persistem_eliminasi_bab: [
                { value: 1, title: 'Normal' },
                { value: 2, title: 'Konstipasi' },
                { value: 3, title: 'Melena' },
                { value: 4, title: 'Inkontinensia' },
                { value: 5, title: 'Ileustomy' },
                { value: 6, title: 'Colostomy' },
                { value: 7, title: 'Diare,frekuensi' },
            ],
            option_anak_penilaian_persistem_eliminasi_bab_select: 0,
            option_anak_penilaian_persistem_eliminasi_bak: [
                { value: 1, title: 'Normal' },
                { value: 2, title: 'Henmaturi' },
                { value: 3, title: 'Nokturia' },
                { value: 4, title: 'Disuria' },
                { value: 5, title: 'Uretrostomy' },
                { value: 6, title: 'Urine menetes' },
                { value: 7, title: 'Inkontinensia Uri' },
            ],
            //penilaian persistem:seksual
            option_anak_penilaian_persistem_seksual_wanita_haid: [
                { value: 1, title: 'Belum' },
                { value: 2, title: 'Sudah' },
            ],
            option_anak_penilaian_persistem_seksual_laki_laki: [
                { value: 1, title: 'Sirkumsisi' },
                { value: 2, title: 'Masalah lain' },

            ],
            option_anak_penilaian_persistem_seksual_laki_laki_select: 0,
            option_anak_penilaian_persistem_seksual_laki_laki_sirkumsisi: [
                { value: 1, title: 'Tidak' },
                { value: 2, title: 'Ya' },
            ],
            option_anak_penilaian_persistem_seksual_laki_laki_sirkumsisi_select: 0,
            option_anak_penilaian_persistem_seksual_laki_laki_masalah_lain: [
                { value: 1, title: 'Tidak' },
                { value: 2, title: 'Ya' },
            ],
            option_anak_penilaian_persistem_seksual_laki_laki_masalah_lain_select: 0,
            //end option penilaian persistem
            option_riwayat_vaksin: [
                { value: 1, title: 'BCG' },
                { value: 2, title: 'Hepatitis B' },
                { value: 3, title: 'DPT' },
                { value: 4, title: 'Polio' },
                { value: 5, title: 'Campak' },
                { value: 6, title: 'Hib' },
                { value: 7, title: 'IPD' },
                { value: 8, title: 'Flu' },
                { value: 9, title: 'Cacar Air' },
                { value: 10, title: 'MMR' },
                { value: 11, title: 'Typhoid' },
                { value: 12, title: 'Hepatitis A' },
                { value: 13, title: 'HPV' },
                { value: 14, title: 'Rotarius' },
                { value: 15, title: 'Influenza' },
            ],
            options_anak_jenis_persalinan: [
                { value: 1, title: 'Normal' },
                { value: 2, title: 'Abnormal' },
            ],
            options_anak_status_gizi: [
                { value: 1, title: 'Kurang' },
                { value: 2, title: 'Cukup' },
            ],
            option_anak_kurva_hasil: [
                { value: 1, title: 'Normal' },
                { value: 2, title: 'Tidak Normal' },
            ],
            option_anak_kurva_status: [
                { value: 1, title: 'Normal' },
                { value: 2, title: 'Abnormal' },
            ],
            option_anak_vaksin_status: [
                { value: 1, title: 'Sudah Diberikan' },
                { value: 2, title: 'Belum Diberikan' },
            ],
        }
    },
    watch: {
        "form_transaction.costing_id": function (value){
            console.log('form_transaction.costing_id: ',value);
        },
        "form_igd.body_mass_index": function (value){
            if(value) {
                this.handleNutritionScore()
            }
        },
        "form_nurse.anamesa_type_id"(newValue) {
            if (newValue == "1") {
                this.form_nurse.family_relation_id = ''
                this.form_nurse.family_name = ''
            }
        },
        // 'items': function (value) {
        //     console.log('items:', value);
        // },

        //  =========================  PERNAH DIRAWAT SEBELUMNYA  =========================
        "form_igd.been_hospitalized_type_id"(newValue) {
            if (newValue != "1") {
                this.form_igd.been_hospitalized_answer = ""
            }
        },

        //  =========================  RIWAYAT PENYAKIT KELUARGA  =========================
        "form_igd.any_history_family_disease_type_id"(newValue) {
            if (newValue != "1") {
                this.form_igd.any_history_family_answer_arry = []
                this.form_igd.any_history_family_answer = ""
            }
        },

        "form_igd.any_history_family_answer_arry"(newValues) {
            if (newValues && !newValues.includes("g")) {
                this.form_igd.any_history_family_answer = ""
            }
        },

        //  =========================  RIWAYAT ALERGI  =========================
        "form_igd.history_allergy_type_id"(newValue) {
            if (newValue != "1") {
                this.form_igd.history_allergy = ""
            }
        },

        //  =========================  SKRINING GIZI  =========================
        "form_igd.six_month_lose_weight"(newValue) {
            if (newValue != "3") {
                this.form_igd.lose_weight = ""
            }
        },

        "form_igd.any_surgery_type_id"(newValue) {
            if (newValue != "1") {
                this.form_igd.any_surgery_date = ""
            }
        },
        
        //  =============== RANAP DEWASA ================
        "form_inpatientAdult.care_plan_diet_nutrition_type_id"(newValue) {
            if (newValue != "1") {
                this.form_inpatientAdult.care_plan_diet_nutrition = ""
            }
        },

        "form_inpatientAdult.care_plan_rehabilitation_type_id"(newValue) {
            if (newValue != "1") {
                this.form_inpatientAdult.care_plan_rehabilitation = ""
            }
        },

        "form_inpatientAdult.care_plan_wound_type_id"(newValue) {
            if (newValue != "1") {
                this.form_inpatientAdult.care_plan_wound = ""
            }
        },

        "form_inpatientAdult.care_plan_pain_management_type_id"(newValue) {
            if (newValue != "1") {
                this.form_inpatientAdult.care_plan_pain_management = ""
            }
        },

        "form_inpatientAdult.medical_devices_type_id"(newValue) {
            if (newValue != "1") {
                this.form_inpatientAdult.medical_devices = []
            }
        },
        
        "form_inpatientAdult.evaluation_smoke_type_id"(newValue) {
            if (newValue != "1") {
                this.form_inpatientAdult.evaluation_smoke_amount = ''
                this.form_inpatientAdult.evaluation_smoke_long = ''
            }
        },

        "form_inpatientAdult.evaluation_alcohol_type_id"(newValue) {
            if (newValue != "1") {
                this.form_inpatientAdult.evaluation_alcohol_type = ''
                this.form_inpatientAdult.evaluation_alcohol_amount = ''
            }
        },

        "form_inpatientAdult.evaluation_sedative_type_id"(newValue) {
            if (newValue != "1") {
                this.form_inpatientAdult.evaluation_sedative_type = ''
                this.form_inpatientAdult.evaluation_sedative_amount = ''
            }
        },

        "form_inpatientAdult.evaluation_sleep_type_id"(newValue) {
            if (newValue != "1") {
                this.form_inpatientAdult.evaluation_sleep = ''
            }
        },

        "form_inpatientAdult.evaluation_grip_type_id"(newValue) {
            if (newValue != "3") {
                this.form_inpatientAdult.evaluation_grip_other = ''
            }
        },

        "form_inpatientAdult.evaluation_coordination_type_id"(newValue) {
            if (newValue != "2") {
                this.form_inpatientAdult.evaluation_coordination_other = ''
            }
        },

        "form_inpatientAdult.evaluation_extremities_bottom_type_id"(newValue) {
            if (newValue != "7") {
                this.form_inpatientAdult.evaluation_extremities_bottom_bone = ''
            }
        },

        "form_inpatientAdult.psychological_status_arry"(newValues) {
            if (newValues && !newValues.includes("f")) {
                this.form_inpatientAdult.psychological_status = ""
            }
        },
        "form_inpatientAdult.mental_status_arry"(newValues) {
            if (newValues && !newValues.includes("b")) {
                this.form_inpatientAdult.mental_status_problems = ""
            }
            if (newValues && !newValues.includes("c")) {
                this.form_inpatientAdult.mental_status_violence = ""
            }
        },

        "form_inpatientAdult.elimination_color_type_id"(newValues) {
            if (newValues != "3") {
                this.form_inpatientAdult.elimination_color = ""
            }
        },

        // "form_inpatientAdult.sore_complaints_type_id"(newValues) {
        //     if (newValues != "1") {
        //         // complaints
        //         this.form_inpatientAdult.sore_complaints = ""
        //         // sore
        //         this.form_inpatientAdult.sore_goes_away_arry = []
        //         this.form_inpatientAdult.sore_goes_away = ""
        //         this.form_inpatientAdult.sore_method_type_id = 0
        //         this.form_inpatientAdult.sore_total_score = "0"
        //         // sore_method_vas
        //         this.form_inpatientAdult.face_score = 0
        //         this.form_inpatientAdult.legs_score = 0
        //         this.form_inpatientAdult.activity_score = 0
        //         this.form_inpatientAdult.cry_score = 0
        //         this.form_inpatientAdult.consolability_score = 0
        //         // sore_method_nrs
        //         this.form_inpatientAdult.tell_doctor_type_id = "0"
        //         this.form_inpatientAdult.tell_doctor_date = ""
        //     }
        // },
        // "form_inpatientAdult.sore_goes_away_arry"(newValues) {
        //     if (newValues && !newValues.includes("e")) {
        //         this.form_inpatientAdult.sore_goes_away = ""
        //     }
        // },
        // "form_inpatientAdult.sore_method_type_id"(newValue) {
        //     if (newValue != 3) {
        //         // console.log('clear VAS');
        //         this.form_inpatientAdult.face_score = 0
        //         this.form_inpatientAdult.legs_score = 0
        //         this.form_inpatientAdult.activity_score = 0
        //         this.form_inpatientAdult.cry_score = 0
        //         this.form_inpatientAdult.consolability_score = 0
        //     }
        //     if (newValue != 5) {
        //         // console.log('clear wong baker')
        //         this.form_inpatientAdult.wong_baker_score = 0
        //         this.form_inpatientAdult.sore_total_score = "0"
        //     }
        //     if (newValue != 6) {
        //         // console.log('clear NRS');
        //         this.form_inpatientAdult.nrs_score = ""
        //         this.form_inpatientAdult.next_nrs_score = []
        //         this.form_inpatientAdult.tell_doctor_type_id = "0"
        //         this.form_inpatientAdult.tell_doctor_date = ""
        //         this.form_inpatientAdult.sore_total_score = "0"
        //     }
        // },
        // "form_inpatientAdult.tell_doctor_type_id"(newValues) {
        //     if (newValues != "1") {
        //         this.form_inpatientAdult.tell_doctor_date = ""
        //     }
        // },
        // "form_inpatientAdult.sore_affects_arry"(newValues) {
        //     if (newValues && !newValues.includes("f")) {
        //         this.form_inpatientAdult.sore_affects = ""
        //     }
        // },

        //  =============== HEMODIALISA ================
        "form_hemodialys.dialysate_type_id"(newValue) {
            if (newValue != "3") {
                this.form_hemodialys.dialysate_temperature = ""
            }
        },

        "form_hemodialys.vascular_type_id"(newValue) {
            if (newValue != "4") {
                this.form_hemodialys.vascular_jugularis = ""
            }
        },

        "form_hemodialys.heparin_type_id"(newValue) {
            if (newValue != 1 && newValue != 2) {
                this.form_hemodialys.initial_dose = ""
                this.form_hemodialys.intermittent = ""
                this.form_hemodialys.without_heparin = ""

            } else if (newValue == 1) {
                this.form_hemodialys.without_heparin = ""

            } else if (newValue == 2) {
                this.form_hemodialys.initial_dose = ""
                this.form_hemodialys.intermittent = ""
            } 
        },

        "form_hemodialys.profiling_type_id"(newValue) {
            if (newValue != 1 && newValue != 2 && newValue != 3) {
                this.form_hemodialys.ultrafiltration = ""
                this.form_hemodialys.natrium = ""
                this.form_hemodialys.bicarbonate = ""
                
            } else if (newValue == 1) {
                this.form_hemodialys.natrium = ""
                this.form_hemodialys.bicarbonate = ""
                
            } else if (newValue == 2) {
                this.form_hemodialys.ultrafiltration = ""
                this.form_hemodialys.bicarbonate = ""

            } else if (newValue == 3) {
                this.form_hemodialys.ultrafiltration = ""
                this.form_hemodialys.natrium = ""
            }
        },
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat("en-US", { style: "currency", currency: "IDR" }).format(value)
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
    computed: {
        ...mapState({ 
            items: state => state.nurse.items, 
            fieldsTransaction: state => state.nurse.fieldstransaction, 
            itemsTransaction: state => state.nurse.itemstransaction, 
            OptionsCosting: state => state.costing.options.costing,
            itemsSoap: state => state.nurse.itemsSoap,

            // vital option
            OptionsOxygen: state => state.soap.vitals.OptionsOxygen,
            OptionsAware: state => state.soap.vitals.OptionsAware,
            OptionGcsEye: state => state.soap.vitals.OptionGcsEye,
            OptionGcsVerbal: state => state.soap.vitals.OptionGcsVerbal,
            OptionGcsMotoric: state => state.soap.vitals.OptionGcsMotoric,
        }),
        
    },
    methods: {
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },
        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
        },
        getFamilyRelation(code) {
            const marrieds = {
                '1': 'Ayah',
                '2': 'Ibu',
                '3': 'Kakak',
                '4': 'Adik',
            };
            return marrieds[code] || '-';
        },
        getTypeNurseName(type_nurse_id){
            switch (type_nurse_id) {
                case 1:
                    return 'Perawat: Kajian Awal Dewasa'           
                case 2:
                    return 'Perawat: Kajian Awal Anak'           
                case 3:
                    return 'Perawat: Kajian Awal Obgyn'           
                case 4:
                    return 'Perawat: Kajian Awal IGD'           
                case 5:
                    return 'Triase'           
                case 6:
                    return 'Perawat: Kajian Awal Rawat Inap Anak'           
                case 7:
                    return 'Perawat: Kajian Awal Rawat Inap Dewasa'           
                case 8:
                    return 'Perawat: Kajian Awal Rawat Inap Lansia'           
                case 9:
                    return 'Perawat: Kajian Awal Fisioterapi'           
                case 10:
                    return 'Perawat: Kajian SOAP'           
                case 11:
                    return 'SDKI SLKI SIKI'   
                case 12:
                    return 'Perawat: Kajian Awal Rawat Inap Neonatus'
                case 13:
                    return 'Perawat: Kajian Awal Hemodialisa'
                default:
                    return 'Kajian Perawat'
            }
        },
        // getVerbalName(value) {
        //     const result = this.OptionsResponse.find((item) => item.value == value);
        //     return result ? result.title : '-';
        // },
        getOxygenName(value) {
            const result = this.OptionsOxygen.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getAwareName(value) {
            const result = this.OptionsAware.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getGcsEyeName(value) {
            const result = this.OptionGcsEye.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getGcsVerbalName(value) {
            const result = this.OptionGcsVerbal.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        getGcsMotoricName(value) {
            const result = this.OptionGcsMotoric.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        // getGcsPupilName(value) {
        //     const result = this.OptionGcsPupil.find((item) => item.value == value);
        //     return result ? result.title : '-';
        // },
        saveChangedNurse($event){
            localStorage.setItem("nursestudies", JSON.stringify(this.form_nurse))
        },
        imtChanged($event){
            localStorage.setItem("nursestudies", JSON.stringify(this.form_nurse))
            // let value_doctor = $event
            var brt_badan = parseFloat(this.form_nurse.weight);
            var tng_badan = parseFloat(this.form_nurse.height);
            var imt = 0.0;    
            var opt = 0;
            // console.log(brt_badan);
            // console.log(tng_badan);
            imt = tng_badan * tng_badan;
            imt = (brt_badan/imt)*100*100;
            // console.log(imt);
            this.form_nurse.imt = imt.toFixed(1);

            if (imt < 17) {
                this.form_igd.body_mass_index = 3

            }else if (imt >= 17 && imt <= 18.4){
                this.form_igd.body_mass_index = 2

            }else if (imt >= 18.5 && imt <= 22.9) {
                this.form_igd.body_mass_index = 1

            } else if (imt >= 23 && imt <= 24.9) {
                this.form_igd.body_mass_index = 2

            } else if (imt >= 25) {
                this.form_igd.body_mass_index = 3

            }
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

        rowOnDelete (obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus tindakan ${obj.costing_name} tanggal ${obj.date_added} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                this.$store.dispatch('nurse/removeTransaction', obj.id)
                .then(response => {
                    if (response.status === 200) {
                        this.getNurseList(this.regData.norm)
                        this.notification('Success','CheckIcon','success','You have successfully')
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

        clearTransactionForm() {
            this.form_transaction.id = ''
            this.form_transaction.costing_id = ''
            this.form_transaction.nurse_id = ''
            this.form_transaction.notes = ''
            this.form_transaction.var_1 = 1
            this.form_transaction.var_2 = ''
            this.form_transaction.var_3 = ''
        },

        openModalTransaction (view, obj) {
            this.clearTransactionForm()
            this.open_modal.nurse = true
            
            if (view === 'add') {
                this.form_transaction.view = view
                this.form_transaction.nurse_id = obj.nurse_id

            } else {
                this.form_transaction.view = view
                this.form_transaction.id = obj.id
                this.form_transaction.costing_id = {
                    value: obj.costing_service_class_id,
                    title: obj.costing_name,
                    qty: obj.var_1,
                    costing: obj.var_2
                }
                this.form_transaction.notes = obj.notes
                this.form_transaction.var_1 = obj.var_1
            }
        },

        validationFormTransaction() {
            let formData = {}

            if (this.form_transaction.costing_id == '' || this.form_transaction.var_1 == '') {
                this.notification('Warning','CloseIcon','warning','Tindakan perawat dan Jumlah wajib terisi')
                
            } else{
                if (this.form_transaction.view == 'add' && this.form_transaction.id == '') {
                    formData = {
                        id: this.form_transaction.id,
                        nurse_id: this.form_transaction.nurse_id,
                        costing_id: this.form_transaction.costing_id.value,
                        var_1: this.form_transaction.var_1,
                        var_2: this.form_transaction.costing_id.costing,
                        var_3: this.form_transaction.costing_id.costing*this.form_transaction.var_1,
                        notes: this.form_transaction.notes,
                    }
                } else if (this.form_transaction.view == 'edit' && this.form_transaction.id != '') {
                    formData = {
                        id: this.form_transaction.id,
                        costing_id: this.form_transaction.costing_id.value,
                        var_1: this.form_transaction.var_1,
                        var_2: this.form_transaction.costing_id.costing,
                        var_3: this.form_transaction.costing_id.costing*this.form_transaction.var_1,
                        notes: this.form_transaction.notes,
                    }
                }

                try {
                    this.disabled.transaction = true
                    this.$store.dispatch('nurse/formPostPutTransaction', formData) .then( res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.getNurseList(this.regData.norm)
                        this.open_modal.nurse = false
                        this.disabled.transaction = false
                    })
                    
                } catch (error) {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    console.error('Error at: ', error)
                    this.disabled.transaction = false
                }
            }
        },

        onNurseModal(){
            this.open_modal.main_modal = true
            
            this.resetIgdForm()
            this.resetInpatientAdultForm()
            // this.resetInpatientNeonatusForm()
            // this.resetInpatientChildForm()
            this.resetHemodialysForm()
            this.fillByStorage()

            this.form_nurse.id = '' 
            this.form_nurse.employee_nurse_id = ''
            
            let temp_facility = this.regData.health_facility_id
            let temp_facility_item = this.regData.health_facility_item_id
            let temp_is_adult = this.count_age(this.regData.birth_date)
            // let temp_is_neonatus = this.count_age_neonatus(this.regData.birth_date)
            // let temp_is_child = this.count_age_child(this.regData.birth_date)
            // this.setViewCondition(temp_facility, temp_facility_item, temp_is_adult, temp_is_neonatus, temp_is_child)
            this.setViewCondition(temp_facility, temp_facility_item, temp_is_adult)
        },

        // setViewCondition(health_facility_id, health_facility_item_id, is_adult, is_neonatus, is_child) {
        setViewCondition(health_facility_id, health_facility_item_id, is_adult) {
            this.view_condition.health_facility_id = health_facility_id
            this.view_condition.health_facility_item_id = health_facility_item_id
            this.view_condition.isAdult = is_adult
            // this.view_condition.isNeonatus = is_neonatus
            // this.view_condition.isChild = is_child
            // console.log('this.view_condition: ', this.view_condition);
        },

        fillByStorage() {
            const storedNurseStudies = localStorage.getItem('nursestudies');
            if (storedNurseStudies) {
                const parsedNurse = JSON.parse(storedNurseStudies);

                // nextTick buat mastiin kondisi terpenuhi abis di render
                this.$nextTick(() => {
                    if (this.reg_des_id === parsedNurse.registration_destination_id) {
                        // console.log('sama');
                        this.form_nurse.anamesa_type_id = parsedNurse.anamesa_type_id 
                        this.form_nurse.main_complaint = parsedNurse.main_complaint
                        this.form_nurse.history_disease_now = parsedNurse.history_disease_now
                        this.form_nurse.history_disease_past = parsedNurse.history_disease_past
                        this.form_nurse.systolic_blood_pressure = parsedNurse.systolic_blood_pressure
                        this.form_nurse.diastolic_blood_pressure = parsedNurse.diastolic_blood_pressure
                        this.form_nurse.classification_blood_pressure_type_id = parsedNurse.classification_blood_pressure_type_id
                        this.form_nurse.pulse_frequency = parsedNurse.pulse_frequency
                        this.form_nurse.breath_frequency = parsedNurse.breath_frequency
                        this.form_nurse.oximetry_frequency = parsedNurse.oximetry_frequency
                        this.form_nurse.temperature = parsedNurse.temperature 
                        // this.form_nurse.blood_sugar = parsedNurse.blood_sugar
                        // this.form_nurse.sore_score = parsedNurse.sore_score
                        // this.form_nurse.ews = parsedNurse.ews
                        // this.form_nurse.verbal_response = parsedNurse.verbal_response
                        this.form_nurse.oxygen_type_id = parsedNurse.oxygen_type_id || ''
                        this.form_nurse.aware = parsedNurse.aware
                        this.form_nurse.eye_score = parsedNurse.eye_score || ''
                        this.form_nurse.verbal_score = parsedNurse.verbal_score || ''
                        this.form_nurse.motor_score = parsedNurse.motor_score || ''
                        // this.form_nurse.pupil_score = parsedNurse.pupil_score
                        this.form_nurse.weight = parsedNurse.weight
                        this.form_nurse.height = parsedNurse.height
                        this.form_nurse.head_circumference = parsedNurse.head_circumference
                        this.form_nurse.chest_circumference = parsedNurse.chest_circumference
                        this.form_nurse.arm_circumference = parsedNurse.arm_circumference
                        this.form_nurse.stomach_circumference = parsedNurse.stomach_circumference
                        this.form_nurse.knee_circumference = parsedNurse.knee_circumference
                        this.form_nurse.aids = parsedNurse.aids
                        this.form_nurse.prothesa = parsedNurse.prothesa
                        this.form_nurse.disabled = parsedNurse.disabled
                        this.form_nurse.adl_type_id = parsedNurse.adl_type_id
                    } else {
                        localStorage.removeItem("nursestudies");
                        // console.log('beda');
                    }
                });
            } else {
                console.log('localStorage nursestudies is empty');
            }
        },
        
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        validationForm() {
            const formData = this.form_nurse

            if (this.view_condition.health_facility_item_id == "cca59ec4-d77a-45fd-b51f-aae069da22ea") { // IGD
                this.form_nurse.type_nurse_id = 4
            // } else if (this.view_condition.health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f' && this.view_condition.isChild == true) {
            //     this.form_nurse.type_nurse_id = 6
            } else if (this.view_condition.health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f' && this.view_condition.isAdult == true) {
                this.form_nurse.type_nurse_id = 7
            // } else if (this.view_condition.health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f' && this.view_condition.isNeonatus == true) {
            //     this.form_nurse.type_nurse_id = 12
            } else if (this.view_condition.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e') {
                this.form_nurse.type_nurse_id = 13
            }

            this.form_nurse.employee_nurse_id = this.userData.employee_id
            this.form_nurse.registration_destination_id = this.reg_des_id

            const fields = {
                systolic_blood_pressure: 'Systolic',
                diastolic_blood_pressure: 'Diastolic',
                pulse_frequency: 'Frekuensi nadi',
                breath_frequency: 'Frekuensi nafas',
                oximetry_frequency: 'Saturasi oksigen',
                temperature: 'Suhu',
                oxygen_type_id: 'Oksigen',
                aware: 'Kesadaran',
                eye_score: 'GCS Mata ',
                verbal_score: 'GCS verbal',
                motor_score: 'GCS Motorik',
                // pupil_score: 'GCS Pupil',
                // verbal_response: 'Kesadaran',
                // sore_score: 'Skor nyeri',
                weight: 'Berat badan',
                height: 'Tinggi badan'
            };

            let isEmpty = false;
            for (const [key, message] of Object.entries(fields)) {
                if (this.form_nurse[key] === '' || this.form_nurse[key] === null) {
                    this.notification('Warning','CloseIcon','warning', `${message} wajib diisi`)
                    isEmpty = true;
                }
            }

            if (!isEmpty) {
                // console.log('fields 'required' terisi semua');
                try {
                    this.disabled.main = true
                    this.$store.dispatch('nurse/formPostPut', formData).then(response => {
                        this.disabled.main = false
                        localStorage.setItem("nursestudies", '')
                        this.notification('Success','CheckIcon','success','You have successfully')                        
                        let temp_nurse_id = formData.id ? formData.id : response.data.id
                        
                        if (this.view_condition.health_facility_item_id == "cca59ec4-d77a-45fd-b51f-aae069da22ea") { // IGD
                            this.validationNurseIGD(temp_nurse_id)
                        } else if (this.view_condition.health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f' && this.view_condition.isAdult == true) {
                            this.validationNurseInpatientAdult(response.data.id)
                        // } else if (this.view_condition.health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f' && this.view_condition.isChild == true) {
                        //     this.validationNurseInpatientChild(response.data.id)
                        // } else if (this.view_condition.health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f' && this.view_condition.isNeonatus == true) {
                        //     this.validationNurseInpatientNeonatus(response.data.id)
                        } else if (this.view_condition.health_facility_item_id == "ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e") { // HEMODIALISIS
                            this.validationNurseHemodialysis(response.data.id)
                        }

                        this.open_modal.main_modal = false
                        this.resetMainNurse()
                        this.getNurseList(this.regData.norm)
                    }, 
                    err => {
                        this.disabled.main = false
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    })
                    // console.log({nurse: formData})
                } catch (error) {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    this.disabled.main = false
                    console.error('Error at: ', error)
                }
            }
        },
        // goToTab(tab) {
        //     this.form_nurse.type_nurse_id = tab
        //     this.sendTriggerClear(`from-tab-${tab}`)
        // },
        // sendTriggerClear(trigger){
        //     this.$emit('sendTriggerClear', trigger);
        //     this.trigger_clear_subnurse = trigger
        //     console.log(this.trigger_clear_subnurse);
        // },
        // catchTriggerClear(message) {
        //     console.log('run');
        // },
        getNurseList(norm){
            this.$store.dispatch('nurse/getAllList', { norm: norm }).then((response) => {
                this.nurseData = response
                // console.log('this.nurseData: ', this.nurseData);
            })
        },
        getData (id) {
			this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]

                // this.$store.dispatch('nurse/getByNorm', this.regData.norm)
                this.getNurseList(this.regData.norm)

                this.$store.dispatch('nurse/getAllResolve', id).then(data => {
                    this.nurseResume = data[0]
                    if (data[0]) {
                        this.form_nurse.anamesa_type_id = data[0].anamesa_type_id
                        this.form_nurse.family_relation_id = data[0].family_relation_id
                        this.form_nurse.family_name = data[0].family_name
                        this.form_nurse.main_complaint = data[0].main_complaint
                        this.form_nurse.history_disease_now = data[0].history_disease_now
                        this.form_nurse.history_disease_past = data[0].history_disease_past
                        this.form_nurse.systolic_blood_pressure = data[0].systolic_blood_pressure 
                        this.form_nurse.diastolic_blood_pressure = data[0].diastolic_blood_pressure 
                        this.form_nurse.classification_blood_pressure_type_id = data[0].classification_blood_pressure_type_id 
                        this.form_nurse.pulse_frequency = data[0].pulse_frequency 
                        this.form_nurse.breath_frequency = data[0].breath_frequency 
                        this.form_nurse.oximetry_frequency = data[0].oximetry_frequency 
                        this.form_nurse.temperature = data[0].temperature 
                        // this.form_nurse.blood_sugar = data[0].blood_sugar
                        // this.form_nurse.sore_score = data[0].sore_score
                        // this.form_nurse.ews = data[0].ews
                        // this.form_nurse.verbal_response = data[0].verbal_response
                        this.form_nurse.oxygen_type_id = data[0].oxygen_type_id || ''
                        this.form_nurse.aware = data[0].aware
                        this.form_nurse.eye_score = data[0].eye_score || ''
                        this.form_nurse.verbal_score = data[0].verbal_score || ''
                        this.form_nurse.motor_score = data[0].motor_score || ''
                        // this.form_nurse.pupil_score = data[0].pupil_score
                        this.form_nurse.weight = data[0].weight
                        this.form_nurse.height = data[0].height
                        this.form_nurse.imt = data[0].imt
                        this.form_nurse.head_circumference = data[0].head_circumference
                        this.form_nurse.chest_circumference = data[0].chest_circumference
                        this.form_nurse.arm_circumference = data[0].arm_circumference
                        this.form_nurse.stomach_circumference = data[0].stomach_circumference
                        this.form_nurse.knee_circumference = data[0].knee_circumference
                    }
                })

                if (this.regData.health_facility_id === "40f06534-07ec-4221-ad9d-dea08196079f") {
                    this.$store.dispatch('costing/getParam', {type_costing_id:3,is_nurse:true,service_class_id:this.regData.service_class_id,health_facility_item_id:'d158a64e-6246-444a-97b7-8d0a2d90e53a'})
                    
                    // this.isAdult = this.count_age(this.regData.birth_date)

                } else {
                    this.$store.dispatch('costing/getParam', {type_costing_id:3,is_nurse:true,service_class_id:this.regData.service_class_id})
                }
            })
        },

        onEditNurse(obj) {
            // console.log('nurse_id: ', obj)
            let temp_facility = obj.health_facility_id
            let temp_facility_item = obj.health_facility_item_id
            let temp_is_adult = this.count_age(this.regData.birth_date)
            // let temp_is_neonatus = this.count_age_neonatus(this.regData.birth_date)
            // let temp_is_child = this.count_age_child(this.regData.birth_date)
            // this.setViewCondition(temp_facility, temp_facility_item, temp_is_adult, temp_is_neonatus, temp_is_child)
            this.setViewCondition(temp_facility, temp_facility_item, temp_is_adult)

            this.resetMainNurse()
            this.resetIgdForm()
            this.resetHemodialysForm()
            this.resetInpatientAdultForm()
            // this.resetInpatientNeonatusForm()
            // this.resetInpatientChildForm()

            this.open_modal.main_modal = true
            
            this.fillMainNurse(obj)
            if (obj.type_nurse_id == 4) {
                this.fillIGDForm(obj.id)
            } else if (obj.type_nurse_id == 7) {
                this.fillAdultInpatientForm(obj.id)
            // } else if (obj.type_nurse_id == 6) {
            //     this.fillChildInpatientForm(obj.id)
            // } else if (obj.type_nurse_id == 12) {
            //     this.fillNeonatusInpatientForm(obj.id)
            } else if (obj.type_nurse_id == 13) {
                this.fillHemodialysisForm(obj.id)
            } 
        },
        
        fillMainNurse (obj) {
            this.form_nurse.id = obj.id ? obj.id : '' 
            this.form_nurse.employee_nurse_id = obj.employee_nurse_id ? obj.employee_nurse_id : ''
            this.form_nurse.registration_destination_id = obj.registration_destination_id ? obj.registration_destination_id : ''
            this.form_nurse.type_nurse_id = obj.type_nurse_id ? obj.type_nurse_id : '1'

            // Anamnesa
            this.form_nurse.anamesa_type_id = obj.anamesa_type_id ? obj.anamesa_type_id : 1 
            this.form_nurse.family_relation_id = obj.family_relation_id ? obj.family_relation_id : ''
            this.form_nurse.family_name = obj.family_name ? obj.family_name : ''
            this.form_nurse.main_complaint = obj.main_complaint ? obj.main_complaint : ''
            this.form_nurse.history_disease_now = obj.history_disease_now ? obj.history_disease_now : ''
            this.form_nurse.history_disease_past = obj.history_disease_past ? obj.history_disease_past : ''

            // Tanda Vital
            this.form_nurse.systolic_blood_pressure = obj.systolic_blood_pressure ? obj.systolic_blood_pressure : '' 
            this.form_nurse.diastolic_blood_pressure = obj.diastolic_blood_pressure ? obj.diastolic_blood_pressure : '' 
            this.form_nurse.pulse_frequency = obj.pulse_frequency ? obj.pulse_frequency : '' 
            this.form_nurse.breath_frequency = obj.breath_frequency ? obj.breath_frequency : '' 
            this.form_nurse.oximetry_frequency = obj.oximetry_frequency ? obj.oximetry_frequency : '' 
            this.form_nurse.temperature = obj.temperature ? obj.temperature : ''
            // this.form_nurse.blood_sugar = obj.blood_sugar ? obj.blood_sugar : ''
            // this.form_nurse.sore_score = obj.sore_score ? obj.sore_score : ''
            // this.form_nurse.ews = obj.ews ? obj.ews : ''                
            // this.form_nurse.verbal_response = obj?.verbal_response || ''
            this.form_nurse.oxygen_type_id = obj?.oxygen_type_id || ''
            this.form_nurse.aware = obj?.aware
            this.form_nurse.eye_score = obj?.eye_score || ''
            this.form_nurse.verbal_score = obj?.verbal_score || ''
            this.form_nurse.motor_score = obj?.motor_score || ''
            // this.form_nurse.pupil_score = obj?.pupil_score

            // Antopometri
            this.form_nurse.weight = obj.weight ? obj.weight : ''
            this.form_nurse.height = obj.height ? obj.height : ''
            this.form_nurse.imt = obj.imt ? obj.imt : ''
            this.form_nurse.head_circumference = obj.head_circumference ? obj.head_circumference : ''
            this.form_nurse.chest_circumference = obj.chest_circumference ? obj.chest_circumference : ''
            this.form_nurse.arm_circumference = obj.arm_circumference ? obj.arm_circumference : ''
            this.form_nurse.stomach_circumference = obj.stomach_circumference ? obj.stomach_circumference : ''
            this.form_nurse.knee_circumference = obj.knee_circumference ? obj.knee_circumference : ''

            // Fungsional
            this.form_nurse.aids = obj.aids ? obj.aids : ''
            this.form_nurse.prothesa = obj.prothesa ? obj.prothesa : ''
            this.form_nurse.disabled = obj.disabled ? obj.disabled : ''
            this.form_nurse.adl_type_id = obj.adl_type_id ? obj.adl_type_id : ''
        },

        resetMainNurse() {
            this.form_nurse.id = '' 
            this.form_nurse.employee_nurse_id = ''
            this.form_nurse.registration_destination_id = ''
            this.form_nurse.type_nurse_id = '1'

            // Anamnesa
            this.form_nurse.anamesa_type_id = 1 
            this.form_nurse.family_relation_id = ''
            this.form_nurse.family_name = ''
            this.form_nurse.main_complaint = ''
            this.form_nurse.history_disease_now = ''
            this.form_nurse.history_disease_past = ''

            // Tanda Vital
            this.form_nurse.systolic_blood_pressure = '' 
            this.form_nurse.diastolic_blood_pressure = '' 
            this.form_nurse.pulse_frequency = '' 
            this.form_nurse.breath_frequency = '' 
            this.form_nurse.oximetry_frequency = '' 
            this.form_nurse.temperature = '' 
            // this.form_nurse.blood_sugar = ''
            // this.form_nurse.sore_score = ''
            // this.form_nurse.ews = ''                
            // this.form_nurse.verbal_response = ''
            this.form_nurse.oxygen_type_id = ''
            this.form_nurse.aware = ''
            this.form_nurse.eye_score = ''
            this.form_nurse.verbal_score = ''
            this.form_nurse.motor_score = ''
            // this.form_nurse.pupil_score = ''

            // Antopometri
            this.form_nurse.weight = ''
            this.form_nurse.height = ''
            this.form_nurse.imt = ''
            this.form_nurse.head_circumference = ''
            this.form_nurse.chest_circumference = ''
            this.form_nurse.arm_circumference = ''
            this.form_nurse.stomach_circumference = ''
            this.form_nurse.knee_circumference = ''

            // Fungsional
            this.form_nurse.aids = ''
            this.form_nurse.prothesa = ''
            this.form_nurse.disabled = ''
            this.form_nurse.adl_type_id = ''
        },

        // FORM IGD
        validationNurseIGD(nurse_id){
            this.form_igd.nurse_id = nurse_id
            try {
                this.disabled.main = true
                this.$store.dispatch('nurse/formPostPutNurseIGD', this.form_igd).then((response) => {
                    this.resetIgdForm()
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.disabled.main = false
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error at: ', error)
                this.disabled.main = false
            }
        },

        fillIGDForm(nurse_id) {
            if (nurse_id) {
                this.$store.dispatch('nurse/getEmergencyDetail', { nurse_id: nurse_id }).then((igd) => {
                    const res = igd[0]
                    // console.log('fill igd res: ', res)
                    this.form_igd.id = res.id ? res.id : ''
                    this.form_igd.nurse_id = res.nurse_id ? res.nurse_id : ''
                    this.form_igd.been_hospitalized_type_id = res.been_hospitalized_type_id ? res.been_hospitalized_type_id : 0
                    this.form_igd.been_hospitalized_answer = res.been_hospitalized_answer ? res.been_hospitalized_answer : ''
                    this.form_igd.any_history_family_disease_type_id = res.any_history_family_disease_type_id ? res.any_history_family_disease_type_id : 0
                    this.form_igd.any_history_family_answer_arry = res.any_history_family_answer_arry ? res.any_history_family_answer_arry : []
                    this.form_igd.any_history_family_answer = res.any_history_family_answer ? res.any_history_family_answer : ''
                    this.form_igd.any_surgery = res.any_surgery ? res.any_surgery : ''
                    this.form_igd.any_surgery_date = res.any_surgery_date ? res.any_surgery_date : ''
                    this.form_igd.complication = res.complication ? res.complication : ''
                    this.form_igd.history_allergy_type_id = res.history_allergy_type_id ? res.history_allergy_type_id : 0
                    this.form_igd.history_allergy = res.history_allergy ? res.history_allergy : ''
                    this.form_igd.history_psychological_arry = res.history_psychological_arry ? res.history_psychological_arry : []
                    this.form_igd.body_mass_index = res.body_mass_index ? res.body_mass_index : ''
                    this.form_igd.patient_food_intake = res.patient_food_intake ? res.patient_food_intake : ''
                    this.form_igd.patient_certain_diets = res.patient_certain_diets ? res.patient_certain_diets : ''
                    this.form_igd.six_month_lose_weight = res.six_month_lose_weight ? res.six_month_lose_weight : ''
                    this.form_igd.lose_weight = res.lose_weight ? res.lose_weight : ''
                    this.form_igd.patient_disease_condition = res.patient_disease_condition ? res.patient_disease_condition : ''
                    this.form_igd.nutrition_total_score = res.nutrition_total_score ? res.nutrition_total_score : '0'
                    this.form_igd.fall_risk_method_type_id = res.fall_risk_method_type_id ? res.fall_risk_method_type_id : ''
                    this.form_igd.fall_risk_type_id = res.fall_risk_type_id ? res.fall_risk_type_id : ''
                    this.form_igd.nursing_diagnoses_arry = res.nursing_diagnoses_arry ? res.nursing_diagnoses_arry : []
                    this.form_igd.nursing_diagnoses = res.nursing_diagnoses ? res.nursing_diagnoses : ''
                    this.form_igd.implementation_arry = res.implementation_arry ? res.implementation_arry : []
                    this.form_igd.implementation = res.implementation ? res.implementation : ''
                    this.form_igd.any_surgery_type_id = res.any_surgery_type_id ? res.any_surgery_type_id : 0
                    this.form_igd.fall_risk_component_a = res.fall_risk_component_a ? res.fall_risk_component_a : 0 
                    this.form_igd.fall_risk_component_b = res.fall_risk_component_b ? res.fall_risk_component_b : 0 
                })
            }
        },

        resetIgdForm() {
            this.form_igd.id =  ''
            this.form_igd.nurse_id =  ''
            this.form_igd.been_hospitalized_type_id =  0
            this.form_igd.been_hospitalized_answer =  ''
            this.form_igd.any_history_family_disease_type_id =  0
            this.form_igd.any_history_family_answer_arry =  []
            this.form_igd.any_history_family_answer =  ''
            this.form_igd.any_surgery =  ''
            this.form_igd.any_surgery_date =  ''
            this.form_igd.complication =  ''
            this.form_igd.history_allergy_type_id =  0
            this.form_igd.history_allergy =  ''
            this.form_igd.history_psychological_arry =  []
            this.form_igd.body_mass_index =  ''
            this.form_igd.patient_food_intake =  ''
            this.form_igd.patient_certain_diets =  ''
            this.form_igd.six_month_lose_weight =  ''
            this.form_igd.lose_weight =  ''
            this.form_igd.patient_disease_condition =  ''
            this.form_igd.nutrition_total_score =  '0'
            this.form_igd.fall_risk_method_type_id =  ''
            this.form_igd.fall_risk_type_id =  ''
            this.form_igd.nursing_diagnoses_arry =  []
            this.form_igd.nursing_diagnoses =  ''
            this.form_igd.implementation_arry =  []
            this.form_igd.implementation =  ''

            this.form_igd.any_surgery_type_id =  0
            this.form_igd.fall_risk_component_a = 0 
            this.form_igd.fall_risk_component_b = 0 
        },

        // FORM Adult Inpatient
        validationNurseInpatientAdult(nurse_id){
            this.form_inpatientAdult.nurse_id = nurse_id

            try {
                this.disabled.main = true
                this.$store.dispatch('nurse/formPostPutNurseInpatientAdult', this.form_inpatientAdult).then((response) => {
                    this.resetInpatientAdultForm()
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.disabled.main = false
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error at: ', error)
                this.disabled.main = false
            }
        },
        fillAdultInpatientForm(nurse_id) {
            if (nurse_id) {
                this.$store.dispatch('nurse/getAdultInpatientDetail', { nurse_id: nurse_id }).then((response) => {
                    let res = response[0]
                    // console.log('fill ranap dewasa: ', res)

                    this.form_inpatientAdult.id = res.id ? res.id : ''
                    this.form_inpatientAdult.nurse_id = res.nurse_id ? res.nurse_id : ""
                    this.form_inpatientAdult.psychological_status_arry = res.psychological_status_arry ? res.psychological_status_arry : []
                    this.form_inpatientAdult.psychological_status = res.psychological_status ? res.psychological_status : ""
                    this.form_inpatientAdult.mental_status_arry = res.mental_status_arry ? res.mental_status_arry : []
                    this.form_inpatientAdult.mental_status_problems = res.mental_status_problems ? res.mental_status_problems : ""
                    this.form_inpatientAdult.mental_status_violence = res.mental_status_violence ? res.mental_status_violence : ""
                    this.form_inpatientAdult.elimination_defecate = res.elimination_defecate ? res.elimination_defecate : ""
                    this.form_inpatientAdult.elimination_urination = res.elimination_urination ? res.elimination_urination : ""
                    this.form_inpatientAdult.elimination_consistency_type_id = res.elimination_consistency_type_id ? res.elimination_consistency_type_id : 0
                    this.form_inpatientAdult.elimination_color_type_id = res.elimination_color_type_id ? res.elimination_color_type_id : 0
                    this.form_inpatientAdult.elimination_color = res.elimination_color ? res.elimination_color : ""
                    // this.form_inpatientAdult.sore_complaints_type_id = res.sore_complaints_type_id ? res.sore_complaints_type_id : 0
                    // this.form_inpatientAdult.sore_complaints = res.sore_complaints ? res.sore_complaints : ""
                    // this.form_inpatientAdult.sore_goes_away_arry = res.sore_goes_away_arry ? res.sore_goes_away_arry : []
                    // this.form_inpatientAdult.sore_goes_away = res.sore_goes_away ? res.sore_goes_away : ""
                    // this.form_inpatientAdult.sore_method_type_id = res.sore_method_type_id ? res.sore_method_type_id : 0
                    // this.form_inpatientAdult.face_score = res.face_score ? res.face_score : 0
                    // this.form_inpatientAdult.legs_score = res.legs_score ? res.legs_score : 0
                    // this.form_inpatientAdult.activity_score = res.activity_score ? res.activity_score : 0
                    // this.form_inpatientAdult.cry_score = res.cry_score ? res.cry_score : 0
                    // this.form_inpatientAdult.consolability_score = res.consolability_score ? res.consolability_score : 0
                    // this.form_inpatientAdult.tell_doctor_type_id = res.tell_doctor_type_id ? res.tell_doctor_type_id : 0
                    // this.form_inpatientAdult.tell_doctor_date = res.tell_doctor_date ? res.tell_doctor_date : ""
                    // this.form_inpatientAdult.sore_total_score = res.sore_total_score ? res.sore_total_score : ""
                    // this.form_inpatientAdult.duration = res.duration ? res.duration : ""
                    // this.form_inpatientAdult.location = res.location ? res.location : ""
                    // this.form_inpatientAdult.sore_quality_arry = res.sore_quality_arry ? res.sore_quality_arry : []
                    // this.form_inpatientAdult.frequency = res.frequency ? res.frequency : ""
                    // this.form_inpatientAdult.wong_baker_score = res.wong_baker_score ? res.wong_baker_score : ""
                    // this.form_inpatientAdult.nrs_score = res.nrs_score ? res.nrs_score : ""
                    // this.form_inpatientAdult.next_nrs_score = res.next_nrs_score ? res.next_nrs_score : []
                    // this.form_inpatientAdult.sore_affects_arry = res.sore_affects_arry ? res.sore_affects_arry : []
                    // this.form_inpatientAdult.sore_affects = res.sore_affects ? res.sore_affects : ""
                    this.form_inpatientAdult.care_plan_diet_nutrition_type_id = res.care_plan_diet_nutrition_type_id ? res.care_plan_diet_nutrition_type_id : 0
                    this.form_inpatientAdult.care_plan_diet_nutrition = res.care_plan_diet_nutrition ? res.care_plan_diet_nutrition : ""
                    this.form_inpatientAdult.care_plan_rehabilitation_type_id = res.care_plan_rehabilitation_type_id ? res.care_plan_rehabilitation_type_id : 0
                    this.form_inpatientAdult.care_plan_rehabilitation = res.care_plan_rehabilitation ? res.care_plan_rehabilitation : "" 
                    this.form_inpatientAdult.care_plan_wound_type_id = res.care_plan_wound_type_id ? res.care_plan_wound_type_id : ""
                    this.form_inpatientAdult.care_plan_wound = res.care_plan_wound ? res.care_plan_wound : ""
                    this.form_inpatientAdult.care_plan_pain_management_type_id = res.care_plan_pain_management_type_id ? res.care_plan_pain_management_type_id : ""
                    this.form_inpatientAdult.care_plan_pain_management = res.care_plan_pain_management ? res.care_plan_pain_management : ""
                    this.form_inpatientAdult.care_plan_other = res.care_plan_other ? res.care_plan_other : ""
                    this.form_inpatientAdult.evaluation_smoke_type_id = res.evaluation_smoke_type_id ? res.evaluation_smoke_type_id : 0
                    this.form_inpatientAdult.evaluation_smoke_amount = res.evaluation_smoke_amount ? res.evaluation_smoke_amount : ""
                    this.form_inpatientAdult.evaluation_smoke_long = res.evaluation_smoke_long ? res.evaluation_smoke_long : ""
                    this.form_inpatientAdult.evaluation_alcohol_type_id = res.evaluation_alcohol_type_id ? res.evaluation_alcohol_type_id : 0
                    this.form_inpatientAdult.evaluation_alcohol_type = res.evaluation_alcohol_type ? res.evaluation_alcohol_type : ""
                    this.form_inpatientAdult.evaluation_alcohol_amount = res.evaluation_alcohol_amount ? res.evaluation_alcohol_amount : ""
                    this.form_inpatientAdult.evaluation_sedative_type_id = res.evaluation_sedative_type_id ? res.evaluation_sedative_type_id : 0
                    this.form_inpatientAdult.evaluation_sedative_type = res.evaluation_sedative_type ? res.evaluation_sedative_type : ""
                    this.form_inpatientAdult.evaluation_sedative_amount = res.evaluation_sedative_amount ? res.evaluation_sedative_amount : ""
                    this.form_inpatientAdult.evaluation_sleep_type_id = res.evaluation_sleep_type_id ? res.evaluation_sleep_type_id : 0
                    this.form_inpatientAdult.evaluation_sleep = res.evaluation_sleep ? res.evaluation_sleep : ""
                    this.form_inpatientAdult.evaluation_daily_type_id = res.evaluation_daily_type_id ? res.evaluation_daily_type_id : 0
                    this.form_inpatientAdult.evaluation_activity_type_id = res.evaluation_activity_type_id ? res.evaluation_activity_type_id : 0
                    this.form_inpatientAdult.evaluation_extremities_top_type_id = res.evaluation_extremities_top_type_id ? res.evaluation_extremities_top_type_id : 0
                    this.form_inpatientAdult.evaluation_grip_type_id = res.evaluation_grip_type_id ? res.evaluation_grip_type_id : 0
                    this.form_inpatientAdult.evaluation_grip_other = res.evaluation_grip_other ? res.evaluation_grip_other : ""
                    this.form_inpatientAdult.evaluation_coordination_type_id = res.evaluation_coordination_type_id ? res.evaluation_coordination_type_id : 0
                    this.form_inpatientAdult.evaluation_coordination_other = res.evaluation_coordination_other ? res.evaluation_coordination_other : ""
                    this.form_inpatientAdult.evaluation_extremities_bottom_type_id = res.evaluation_extremities_bottom_type_id ? res.evaluation_extremities_bottom_type_id : 0
                    this.form_inpatientAdult.evaluation_extremities_bottom_bone = res.evaluation_extremities_bottom_bone ? res.evaluation_extremities_bottom_bone : ""
                    this.form_inpatientAdult.evaluation_ambulation_type_id = res.evaluation_ambulation_type_id ? res.evaluation_ambulation_type_id : 0
                    this.form_inpatientAdult.medical_devices_type_id = res.medical_devices_type_id ? res.medical_devices_type_id : 0
                    this.form_inpatientAdult.medical_devices = res.medical_devices ? res.medical_devices : []
                    this.form_inpatientAdult.discharge_treatment_duration = res.discharge_treatment_duration ? res.discharge_treatment_duration : ''
                    this.form_inpatientAdult.discharge_further_care_arry = res.discharge_further_care_arry ? res.discharge_further_care_arry : []
                    this.form_inpatientAdult.examination_history = res.examination_history ? res.examination_history : []
                })
            }
        },
        resetInpatientAdultForm (){
            this.form_inpatientAdult.id = ''
            this.form_inpatientAdult.nurse_id = ""
            this.form_inpatientAdult.psychological_status_arry = []
            this.form_inpatientAdult.psychological_status = ""
            this.form_inpatientAdult.mental_status_arry = []
            this.form_inpatientAdult.mental_status_problems = ""
            this.form_inpatientAdult.mental_status_violence = ""
            this.form_inpatientAdult.elimination_defecate = ""
            this.form_inpatientAdult.elimination_urination = ""
            this.form_inpatientAdult.elimination_consistency_type_id = 0
            this.form_inpatientAdult.elimination_color_type_id = 0
            this.form_inpatientAdult.elimination_color = ""
            // this.form_inpatientAdult.sore_complaints_type_id = 0
            // this.form_inpatientAdult.sore_complaints = ""
            // this.form_inpatientAdult.sore_goes_away_arry = []
            // this.form_inpatientAdult.sore_goes_away = ""
            // this.form_inpatientAdult.sore_method_type_id = 0
            // this.form_inpatientAdult.face_score = 0
            // this.form_inpatientAdult.legs_score = 0
            // this.form_inpatientAdult.activity_score = 0
            // this.form_inpatientAdult.cry_score = 0
            // this.form_inpatientAdult.consolability_score = 0
            // this.form_inpatientAdult.tell_doctor_type_id = 0
            // this.form_inpatientAdult.tell_doctor_date = ""
            // this.form_inpatientAdult.sore_total_score = ""
            // this.form_inpatientAdult.duration = ""
            // this.form_inpatientAdult.location = ""
            // this.form_inpatientAdult.sore_quality_arry = []
            // this.form_inpatientAdult.frequency = ""
            // this.form_inpatientAdult.wong_baker_score = ""
            // this.form_inpatientAdult.nrs_score = ""
            // this.form_inpatientAdult.next_nrs_score = []
            // this.form_inpatientAdult.sore_affects_arry = []
            // this.form_inpatientAdult.sore_affects = ""
            this.form_inpatientAdult.care_plan_diet_nutrition_type_id = 0
            this.form_inpatientAdult.care_plan_diet_nutrition = ""
            this.form_inpatientAdult.care_plan_rehabilitation_type_id = 0
            this.form_inpatientAdult.care_plan_rehabilitation = "" 
            this.form_inpatientAdult.care_plan_wound_type_id = ""
            this.form_inpatientAdult.care_plan_wound = ""
            this.form_inpatientAdult.care_plan_pain_management_type_id = ""
            this.form_inpatientAdult.care_plan_pain_management = ""
            this.form_inpatientAdult.care_plan_other = ""
            this.form_inpatientAdult.evaluation_smoke_type_id = 0
            this.form_inpatientAdult.evaluation_smoke_amount = ""
            this.form_inpatientAdult.evaluation_smoke_long = ""
            this.form_inpatientAdult.evaluation_alcohol_type_id = 0
            this.form_inpatientAdult.evaluation_alcohol_type = ""
            this.form_inpatientAdult.evaluation_alcohol_amount = ""
            this.form_inpatientAdult.evaluation_sedative_type_id = 0
            this.form_inpatientAdult.evaluation_sedative_type = ""
            this.form_inpatientAdult.evaluation_sedative_amount = ""
            this.form_inpatientAdult.evaluation_sleep_type_id = 0
            this.form_inpatientAdult.evaluation_sleep = ""
            this.form_inpatientAdult.evaluation_daily_type_id = 0
            this.form_inpatientAdult.evaluation_activity_type_id = 0
            this.form_inpatientAdult.evaluation_extremities_top_type_id = 0
            this.form_inpatientAdult.evaluation_grip_type_id = 0
            this.form_inpatientAdult.evaluation_grip_other = ""
            this.form_inpatientAdult.evaluation_coordination_type_id = 0
            this.form_inpatientAdult.evaluation_coordination_other = ""
            this.form_inpatientAdult.evaluation_extremities_bottom_type_id = 0
            this.form_inpatientAdult.evaluation_extremities_bottom_bone = ""
            this.form_inpatientAdult.evaluation_ambulation_type_id = 0
            this.form_inpatientAdult.medical_devices_type_id = 0
            this.form_inpatientAdult.medical_devices = []
            this.form_inpatientAdult.discharge_treatment_duration = ''
            this.form_inpatientAdult.discharge_further_care_arry = []
            this.form_inpatientAdult.examination_history = []
        },

        // FORM Neonatus Inpatient
        validationNurseInpatientNeonatus(nurse_id) {
            this.form_inpatientNeonatus.register_destination_id = this.$route.params.id
            this.form_inpatientNeonatus.nurse_id = nurse_id
            //riwayat kesehatan dahulu
            this.form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.hasil_laboratorium_ibu = this.OptionsNeonatusHasilLabIbuSelect !== 4 ? this.OptionsNeonatusHasilLabIbuSelect : this.form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.hasil_laboratorium_ibu
            this.form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.pemberian_obat_setelah_lahir = this.OptionsNeonatusPemberianObatSetelahLahirSelect !== 3 ? this.OptionsNeonatusPemberianObatSetelahLahirSelect : this.form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.pemberian_obat_setelah_lahir
            // console.log({neonatus: this.form_inpatientNeonatus})
            try {
                this.disabled.main = true
                this.$store.dispatch('nurse/formPostPutNurseInpatientNeonatus', this.form_inpatientNeonatus).then((response) => {
                    this.resetInpatientNeonatusForm()
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.disabled.main = false
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error at: ', error)
                this.disabled.main = false
            }
        },

        fillNeonatusInpatientForm(nurse_id) {
            if(nurse_id){
                this.$store.dispatch('nurse/getXamNeonatusInpatientDetail', { nurse_id: nurse_id }).then((res) => {
                    // console.log({detailNeonatus: res});
                    this.form_inpatientNeonatus.id = res.data.id ? res.data.id : ''
                    this.form_inpatientNeonatus.register_destination_id = res.data.register_destination_id ? res.data.register_destination_id : ''
                    this.form_inpatientNeonatus.nurse_id = res.data.nurse_id ? res.data.nurse_id : ''
                    //riwayat kesehatan bayi dan ibu
                    res.data.neonatus_riwayat_kesehatan_bayi_ibu.hasil_laboratorium_ibu !== 4 ? this.OptionsNeonatusHasilLabIbuSelect = Number(res.data.neonatus_riwayat_kesehatan_bayi_ibu.hasil_laboratorium_ibu) : this.form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.hasil_laboratorium_ibu = Number(res.data.neonatus_riwayat_kesehatan_bayi_ibu.hasil_laboratorium_ibu)
                    this.form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.golongan_darah_ibu = Number(res.data.neonatus_riwayat_kesehatan_bayi_ibu.golongan_darah_ibu) ? Number(res.data.neonatus_riwayat_kesehatan_bayi_ibu.golongan_darah_ibu) : ''
                    this.form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.riwayat_persalinan = Number(res.data.neonatus_riwayat_kesehatan_bayi_ibu.riwayat_persalinan) ? Number(res.data.neonatus_riwayat_kesehatan_bayi_ibu.riwayat_persalinan) : ''
                    this.form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.riwayat_kelahiran = Number(res.data.neonatus_riwayat_kesehatan_bayi_ibu.riwayat_kelahiran) ? Number(res.data.neonatus_riwayat_kesehatan_bayi_ibu.riwayat_kelahiran) : ''
                    this.form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.jenis_kelamin_bayi = Number(res.data.neonatus_riwayat_kesehatan_bayi_ibu.jenis_kelamin_bayi) ? Number(res.data.neonatus_riwayat_kesehatan_bayi_ibu.jenis_kelamin_bayi) : ''
                    res.data.neonatus_riwayat_kesehatan_bayi_ibu.pemberian_obat_setelah_lahir !== 3 ? this.OptionsNeonatusPemberianObatSetelahLahirSelect = Number(res.data.neonatus_riwayat_kesehatan_bayi_ibu.pemberian_obat_setelah_lahir) : this.form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.pemberian_obat_setelah_lahir =  Number(res.data.neonatus_riwayat_kesehatan_bayi_ibu.pemberian_obat_setelah_lahir)
                    this.form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.diagnosa_partus = res.data.neonatus_riwayat_kesehatan_bayi_ibu.diagnosa_partus ? res.data.neonatus_riwayat_kesehatan_bayi_ibu.diagnosa_partus : ''
                    this.form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.riwayat_penyakit_ibu_setelah_hamil = res.data.neonatus_riwayat_kesehatan_bayi_ibu.riwayat_penyakit_ibu_setelah_hamil ? res.data.neonatus_riwayat_kesehatan_bayi_ibu.riwayat_penyakit_ibu_setelah_hamil : ''
                    this.form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.riwayat_kehamilan = Number(res.data.neonatus_riwayat_kesehatan_bayi_ibu.riwayat_kehamilan) ? Number(res.data.neonatus_riwayat_kesehatan_bayi_ibu.riwayat_kehamilan) : ''
                    //neonatus kebutuhan edukasi orang tua
                    this.form_inpatientNeonatus.neonatus_kebutuhan_edukasi_orang_tua.bicara = Number(res.data.neonatus_kebutuhan_edukasi_orang_tua.bicara) ? Number(res.data.neonatus_kebutuhan_edukasi_orang_tua.bicara) : ''
                    this.form_inpatientNeonatus.neonatus_kebutuhan_edukasi_orang_tua.bahasa_sehari_hari = Number(res.data.neonatus_kebutuhan_edukasi_orang_tua.bahasa_sehari_hari) ? Number(res.data.neonatus_kebutuhan_edukasi_orang_tua.bahasa_sehari_hari) : ''
                    this.form_inpatientNeonatus.neonatus_kebutuhan_edukasi_orang_tua.perlu_penerjemah = Number(res.data.neonatus_kebutuhan_edukasi_orang_tua.perlu_penerjemah) ? Number(res.data.neonatus_kebutuhan_edukasi_orang_tua.perlu_penerjemah) : ''
                    this.form_inpatientNeonatus.neonatus_kebutuhan_edukasi_orang_tua.tingkat_pendidikan = Number(res.data.neonatus_kebutuhan_edukasi_orang_tua.tingkat_pendidikan) ? Number(res.data.neonatus_kebutuhan_edukasi_orang_tua.tingkat_pendidikan) : ''
                    this.form_inpatientNeonatus.neonatus_kebutuhan_edukasi_orang_tua.keluarga_menginginkan_informasi_tentang_proses_penyakit = res.data.neonatus_kebutuhan_edukasi_orang_tua.keluarga_menginginkan_informasi_tentang_proses_penyakit ? res.data.neonatus_kebutuhan_edukasi_orang_tua.keluarga_menginginkan_informasi_tentang_proses_penyakit : ''
                    //neonatus spiritual
                    this.form_inpatientNeonatus.neonatus_spiritual.agama = Number(res.data.neonatus_spiritual.agama) ? Number(res.data.neonatus_spiritual.agama) : ''
                    this.form_inpatientNeonatus.neonatus_spiritual.permintaan_khusus_terkait_dengan_adat = res.data.neonatus_spiritual.permintaan_khusus_terkait_dengan_adat ? res.data.neonatus_spiritual.permintaan_khusus_terkait_dengan_adat : ''
                    //neonatus sosial budaya
                    this.form_inpatientNeonatus.neonatus_sosial_budaya.suku_bangsa = res.data.neonatus_sosial_budaya.suku_bangsa ? res.data.neonatus_sosial_budaya.suku_bangsa : ''
                    this.form_inpatientNeonatus.neonatus_sosial_budaya.warga_negara = res.data.neonatus_sosial_budaya.warga_negara ? res.data.neonatus_sosial_budaya.warga_negara : ''
                    this.form_inpatientNeonatus.neonatus_sosial_budaya.tempat_tinggal = Number(res.data.neonatus_sosial_budaya.tempat_tinggal)  ? Number(res.data.neonatus_sosial_budaya.tempat_tinggal) : ''
                    this.form_inpatientNeonatus.neonatus_sosial_budaya.yang_merawat_bayi = Number(res.data.neonatus_sosial_budaya.yang_merawat_bayi) ? Number(res.data.neonatus_sosial_budaya.yang_merawat_bayi) : ''
                    this.form_inpatientNeonatus.neonatus_sosial_budaya.orang_tua = res.data.neonatus_sosial_budaya.orang_tua ? res.data.neonatus_sosial_budaya.orang_tua : ''
                    this.form_inpatientNeonatus.neonatus_sosial_budaya.berkunjung = Number(res.data.neonatus_sosial_budaya.berkunjung) ? Number(res.data.neonatus_sosial_budaya.berkunjung) : ''
                    this.form_inpatientNeonatus.neonatus_sosial_budaya.menyentuh = Number(res.data.neonatus_sosial_budaya.menyentuh) ? Number(res.data.neonatus_sosial_budaya.menyentuh) : ''
                    this.form_inpatientNeonatus.neonatus_sosial_budaya.menggendong = Number(res.data.neonatus_sosial_budaya.menggendong) ? Number(res.data.neonatus_sosial_budaya.menggendong) : ''
                    this.form_inpatientNeonatus.neonatus_sosial_budaya.kontak_mata = Number(res.data.neonatus_sosial_budaya.kontak_mata) ? Number(res.data.neonatus_sosial_budaya.kontak_mata) : ''
                    this.form_inpatientNeonatus.neonatus_sosial_budaya.berbicara = Number(res.data.neonatus_sosial_budaya.berbicara) ? Number(res.data.neonatus_sosial_budaya.berbicara) : ''
                    this.form_inpatientNeonatus.neonatus_sosial_budaya.lainnya = res.data.neonatus_sosial_budaya.lainnya ? res.data.neonatus_sosial_budaya.lainnya : ''
                    //neonatus paru
                    this.form_inpatientNeonatus.neonatus_paru.pergerakan_paru = res.data.neonatus_paru.pergerakan_paru ? res.data.neonatus_paru.pergerakan_paru : ''
                    this.form_inpatientNeonatus.neonatus_paru.frekuensi_pernapasan_paru = res.data.neonatus_paru.frekuensi_pernapasan_paru ? res.data.neonatus_paru.frekuensi_pernapasan_paru : ''
                    this.form_inpatientNeonatus.neonatus_paru.retraksi = res.data.neonatus_paru.retraksi ? res.data.neonatus_paru.retraksi : ''
                    this.form_inpatientNeonatus.neonatus_paru.nch = res.data.neonatus_paru.nch ? res.data.neonatus_paru.nch : ''
                    this.form_inpatientNeonatus.neonatus_paru.grunting = res.data.neonatus_paru.grunting ? res.data.neonatus_paru.grunting : ''
                    this.form_inpatientNeonatus.neonatus_paru.dyspnoe = res.data.neonatus_paru.dyspnoe ? res.data.neonatus_paru.dyspnoe : ''
                    this.form_inpatientNeonatus.neonatus_paru.tachynoe = res.data.neonatus_paru.tachynoe ? res.data.neonatus_paru.tachynoe : ''
                    this.form_inpatientNeonatus.neonatus_paru.gargling_napas_tambahan = res.data.neonatus_paru.gargling_napas_tambahan ? res.data.neonatus_paru.gargling_napas_tambahan : ''
                    this.form_inpatientNeonatus.neonatus_paru.snooring_napas_tambahan = res.data.neonatus_paru.snooring_napas_tambahan ? res.data.neonatus_paru.snooring_napas_tambahan : ''
                    this.form_inpatientNeonatus.neonatus_paru.stridor_napas_tambahan = res.data.neonatus_paru.stridor_napas_tambahan ? res.data.neonatus_paru.stridor_napas_tambahan : ''
                    this.form_inpatientNeonatus.neonatus_paru.peruksi_paru_paru = res.data.neonatus_paru.peruksi_paru_paru ? res.data.neonatus_paru.peruksi_paru_paru : ''
                    this.form_inpatientNeonatus.neonatus_paru.ronkhi_paru_kanan = res.data.neonatus_paru.ronkhi_paru_kanan ? res.data.neonatus_paru.ronkhi_paru_kanan : ''
                    this.form_inpatientNeonatus.neonatus_paru.wheezing_paru_kanan = res.data.neonatus_paru.wheezing_paru_kanan ? res.data.neonatus_paru.wheezing_paru_kanan : ''
                    this.form_inpatientNeonatus.neonatus_paru.lainnya_paru_kanan = res.data.neonatus_paru.lainnya_paru_kanan ? res.data.neonatus_paru.lainnya_paru_kanan : ''
                    this.form_inpatientNeonatus.neonatus_paru.perkusi_paru_kiri = res.data.neonatus_paru.perkusi_paru_kiri ? res.data.neonatus_paru.perkusi_paru_kiri : ''
                    this.form_inpatientNeonatus.neonatus_paru.ronkhi_paru_kiri = res.data.neonatus_paru.ronkhi_paru_kiri ? res.data.neonatus_paru.ronkhi_paru_kiri : ''
                    this.form_inpatientNeonatus.neonatus_paru.wheezing_paru_kiri = res.data.neonatus_paru.wheezing_paru_kiri ? res.data.neonatus_paru.wheezing_paru_kiri : ''
                    this.form_inpatientNeonatus.neonatus_paru.lainnya_paru_kiri = res.data.neonatus_paru.lainnya_paru_kiri ? res.data.neonatus_paru.lainnya_paru_kiri : ''
                    //neonatus down score
                    this.form_inpatientNeonatus.neonatus_down_score.frekuensi_napas = res.data.neonatus_down_score.frekuensi_napas ? res.data.neonatus_down_score.frekuensi_napas : ''
                    this.form_inpatientNeonatus.neonatus_down_score.retraksi = res.data.neonatus_down_score.retraksi ? res.data.neonatus_down_score.retraksi : ''
                    this.form_inpatientNeonatus.neonatus_down_score.sianosis = res.data.neonatus_down_score.sianosis ? res.data.neonatus_down_score.sianosis : ''
                    this.form_inpatientNeonatus.neonatus_down_score.air_entry = res.data.neonatus_down_score.air_entry ? res.data.neonatus_down_score.air_entry : ''
                    this.form_inpatientNeonatus.neonatus_down_score.merintih = res.data.neonatus_down_score.merintih ? res.data.neonatus_down_score.merintih : ''
                    //neonatus_eliminasi
                    this.form_inpatientNeonatus.neonatus_eliminasi.anus = res.data.neonatus_eliminasi.anus ? res.data.neonatus_eliminasi.anus : ''
                    this.form_inpatientNeonatus.neonatus_eliminasi.defaksi_anus = res.data.neonatus_eliminasi.defaksi_anus ? res.data.neonatus_eliminasi.defaksi_anus : ''
                    this.form_inpatientNeonatus.neonatus_eliminasi.defaksi_stoma = res.data.neonatus_eliminasi.defaksi_stoma ? res.data.neonatus_eliminasi.defaksi_stoma : ''
                    this.form_inpatientNeonatus.neonatus_eliminasi.frekuensi_defaksi = res.data.neonatus_eliminasi.frekuensi_defaksi ? res.data.neonatus_eliminasi.frekuensi_defaksi : ''
                    this.form_inpatientNeonatus.neonatus_eliminasi.konsistensi_defaksi = res.data.neonatus_eliminasi.konsistensi_defaksi ? res.data.neonatus_eliminasi.konsistensi_defaksi : ''
                    this.form_inpatientNeonatus.neonatus_eliminasi.karakteristik_fases = res.data.neonatus_eliminasi.karakteristik_fases ? res.data.neonatus_eliminasi.karakteristik_fases : ''
                    this.form_inpatientNeonatus.neonatus_eliminasi.urin = res.data.neonatus_eliminasi.urin ? res.data.neonatus_eliminasi.urin : ''
                    this.form_inpatientNeonatus.neonatus_eliminasi.diuresis = res.data.neonatus_eliminasi.diuresis ? res.data.neonatus_eliminasi.diuresis : ''
                    //neonatus genitalia
                    this.form_inpatientNeonatus.neonatus_genitalia.jenis_kelamin = res.data.neonatus_genitalia.jenis_kelamin ? res.data.neonatus_genitalia.jenis_kelamin : ''
                    this.form_inpatientNeonatus.neonatus_genitalia.kelainan = res.data.neonatus_genitalia.kelainan ? res.data.neonatus_genitalia.kelainan : ''
                    this.form_inpatientNeonatus.neonatus_genitalia.testis_laki_laki = res.data.neonatus_genitalia.testis_laki_laki ? res.data.neonatus_genitalia.testis_laki_laki : ''
                    this.form_inpatientNeonatus.neonatus_genitalia.penis_laki_laki = res.data.neonatus_genitalia.penis_laki_laki ? res.data.neonatus_genitalia.penis_laki_laki : ''
                    this.form_inpatientNeonatus.neonatus_genitalia.labio_perempuan = res.data.neonatus_genitalia.labio_perempuan ? res.data.neonatus_genitalia.labio_perempuan : ''
                    this.form_inpatientNeonatus.neonatus_genitalia.lainnya = res.data.neonatus_genitalia.lainnya ? res.data.neonatus_genitalia.lainnya : ''
                    //neonatus apgar score
                    this.form_inpatientNeonatus.neonatus_apgar_score.menit_ke = res.data.neonatus_apgar_score.menit_ke ? res.data.neonatus_apgar_score.menit_ke : ''
                    this.form_inpatientNeonatus.neonatus_apgar_score.denyut_jantung = res.data.neonatus_apgar_score.denyut_jantung ? res.data.neonatus_apgar_score.denyut_jantung : ''
                    this.form_inpatientNeonatus.neonatus_apgar_score.usaha_napas = res.data.neonatus_apgar_score.usaha_napas ? res.data.neonatus_apgar_score.usaha_napas : ''
                    this.form_inpatientNeonatus.neonatus_apgar_score.tonus_otot = res.data.neonatus_apgar_score.tonus_otot ? res.data.neonatus_apgar_score.tonus_otot : ''
                    this.form_inpatientNeonatus.neonatus_apgar_score.refleks = res.data.neonatus_apgar_score.refleks ? res.data.neonatus_apgar_score.refleks : ''
                    //neonatus kardiologi
                    this.form_inpatientNeonatus.neonatus_kardiologi.kelainan_jantung = res.data.neonatus_kardiologi.kelainan_jantung ? res.data.neonatus_kardiologi.kelainan_jantung : ''
                    this.form_inpatientNeonatus.neonatus_kardiologi.denyut_jantung = res.data.neonatus_kardiologi.denyut_jantung ? res.data.neonatus_kardiologi.denyut_jantung : ''
                    this.form_inpatientNeonatus.neonatus_kardiologi.type_denyut_jantung = res.data.neonatus_kardiologi.type_denyut_jantung ? res.data.neonatus_kardiologi.type_denyut_jantung : ''
                    this.form_inpatientNeonatus.neonatus_kardiologi.murmur = res.data.neonatus_kardiologi.murmur ? res.data.neonatus_kardiologi.murmur : ''
                    this.form_inpatientNeonatus.neonatus_kardiologi.keterangan_murmur = res.data.neonatus_kardiologi.keterangan_murmur ? res.data.neonatus_kardiologi.keterangan_murmur : ''
                    this.form_inpatientNeonatus.neonatus_kardiologi.gallop = res.data.neonatus_kardiologi.gallop ? res.data.neonatus_kardiologi.gallop : ''
                    this.form_inpatientNeonatus.neonatus_kardiologi.keterangan_gallop = res.data.neonatus_kardiologi.keterangan_gallop ? res.data.neonatus_kardiologi.keterangan_gallop : ''
                    this.form_inpatientNeonatus.neonatus_kardiologi.sianosis = res.data.neonatus_kardiologi.sianosis ? res.data.neonatus_kardiologi.sianosis : ''
                    this.form_inpatientNeonatus.neonatus_kardiologi.pucat = res.data.neonatus_kardiologi.pucat ? res.data.neonatus_kardiologi.pucat : ''
                    this.form_inpatientNeonatus.neonatus_kardiologi.crt = res.data.neonatus_kardiologi.crt ? res.data.neonatus_kardiologi.crt : ''
                    this.form_inpatientNeonatus.neonatus_kardiologi.akral = res.data.neonatus_kardiologi.akral ? res.data.neonatus_kardiologi.akral : ''
                    //neonatus skala nyeri
                    this.form_inpatientNeonatus.neonatus_skala_nyeri.nama = res.data.neonatus_skala_nyeri.nama ? res.data.neonatus_skala_nyeri.nama : ''
                    this.form_inpatientNeonatus.neonatus_skala_nyeri.skor = res.data.neonatus_skala_nyeri.skor ? res.data.neonatus_skala_nyeri.skor : ''
                    //neonatus trauma kulit
                    this.form_inpatientNeonatus.neonatus_trauma_kulit.usia_gestasi = res.data.neonatus_trauma_kulit.usia_gestasi ? res.data.neonatus_trauma_kulit.usia_gestasi : ''
                    this.form_inpatientNeonatus.neonatus_trauma_kulit.status_mental = res.data.neonatus_trauma_kulit.status_mental ? res.data.neonatus_trauma_kulit.status_mental : ''
                    this.form_inpatientNeonatus.neonatus_trauma_kulit.mobilisasi = res.data.neonatus_trauma_kulit.mobilisasi ? res.data.neonatus_trauma_kulit.mobilisasi : ''
                    this.form_inpatientNeonatus.neonatus_trauma_kulit.aktivitas = res.data.neonatus_trauma_kulit.aktivitas ? res.data.neonatus_trauma_kulit.aktivitas : ''
                    this.form_inpatientNeonatus.neonatus_trauma_kulit.nutrisi = res.data.neonatus_trauma_kulit.nutrisi ? res.data.neonatus_trauma_kulit.nutrisi : ''
                    this.form_inpatientNeonatus.neonatus_trauma_kulit.kelembaban = res.data.neonatus_trauma_kulit.kelembaban ? res.data.neonatus_trauma_kulit.kelembaban : ''
                    //neonatus alergi reaksi
                    this.form_inpatientNeonatus.neonatus_alergi_reaksi.makanan = res.data.neonatus_alergi_reaksi.makanan ? res.data.neonatus_alergi_reaksi.makanan : ''
                    this.form_inpatientNeonatus.neonatus_alergi_reaksi.makanan_reaksi = res.data.neonatus_alergi_reaksi.makanan_reaksi ? res.data.neonatus_alergi_reaksi.makanan_reaksi : ''
                    this.form_inpatientNeonatus.neonatus_alergi_reaksi.obat = res.data.neonatus_alergi_reaksi.obat ? res.data.neonatus_alergi_reaksi.obat : ''
                    this.form_inpatientNeonatus.neonatus_alergi_reaksi.obat_reaksi = res.data.neonatus_alergi_reaksi.obat_reaksi ? res.data.neonatus_alergi_reaksi.obat_reaksi : ''
                    this.form_inpatientNeonatus.neonatus_alergi_reaksi.lainnya = res.data.neonatus_alergi_reaksi.lainnya ? res.data.neonatus_alergi_reaksi.lainnya : ''
                    this.form_inpatientNeonatus.neonatus_alergi_reaksi.lainnya_reaksi = res.data.neonatus_alergi_reaksi.lainnya_reaksi ? res.data.neonatus_alergi_reaksi.lainnya_reaksi : ''
                    this.form_inpatientNeonatus.neonatus_alergi_reaksi.klip_tanda_alergi = res.data.neonatus_alergi_reaksi.klip_tanda_alergi ? res.data.neonatus_alergi_reaksi.klip_tanda_alergi : ''
                    this.form_inpatientNeonatus.neonatus_alergi_reaksi.beritahu_dokter = res.data.neonatus_alergi_reaksi.beritahu_dokter ? res.data.neonatus_alergi_reaksi.beritahu_dokter : ''
                    this.form_inpatientNeonatus.neonatus_alergi_reaksi.beritahu_farmasi = res.data.neonatus_alergi_reaksi.beritahu_farmasi ? res.data.neonatus_alergi_reaksi.beritahu_farmasi : ''
                    this.form_inpatientNeonatus.neonatus_alergi_reaksi.beritahu_dietisien = res.data.neonatus_alergi_reaksi.beritahu_dietisien ? res.data.neonatus_alergi_reaksi.beritahu_dietisien : ''
                    //neonatus muskoloskeletal
                    this.form_inpatientNeonatus.neonatus_muskuloskeletal.tonus = res.data.neonatus_muskuloskeletal.tonus ? res.data.neonatus_muskuloskeletal.tonus : ''
                    this.form_inpatientNeonatus.neonatus_muskuloskeletal.kelainan_tulang = res.data.neonatus_muskuloskeletal.kelainan_tulang ? res.data.neonatus_muskuloskeletal.kelainan_tulang : ''
                    this.form_inpatientNeonatus.neonatus_muskuloskeletal.gerakan_bayi = res.data.neonatus_muskuloskeletal.gerakan_bayi ? res.data.neonatus_muskuloskeletal.gerakan_bayi : ''
                    //neonatus penanggung jawab
                    this.form_inpatientNeonatus.neonatus_penanggung_jawab.wali = res.data.neonatus_penanggung_jawab.wali ? res.data.neonatus_penanggung_jawab.wali : ''
                    this.form_inpatientNeonatus.neonatus_penanggung_jawab.nama = res.data.neonatus_penanggung_jawab.nama ? res.data.neonatus_penanggung_jawab.nama : ''
                    this.form_inpatientNeonatus.neonatus_penanggung_jawab.usia = res.data.neonatus_penanggung_jawab.usia ? res.data.neonatus_penanggung_jawab.usia : ''
                    this.form_inpatientNeonatus.neonatus_penanggung_jawab.perkawinan_ke = res.data.neonatus_penanggung_jawab.perkawinan_ke ? res.data.neonatus_penanggung_jawab.perkawinan_ke : ''
                    this.form_inpatientNeonatus.neonatus_penanggung_jawab.pekerjaan = res.data.neonatus_penanggung_jawab.pekerjaan ? res.data.neonatus_penanggung_jawab.pekerjaan : ''
                    this.form_inpatientNeonatus.neonatus_penanggung_jawab.pendidikan_terakhir = res.data.neonatus_penanggung_jawab.pendidikan_terakhir ? res.data.neonatus_penanggung_jawab.pendidikan_terakhir : ''
                    this.form_inpatientNeonatus.neonatus_penanggung_jawab.alamat = res.data.neonatus_penanggung_jawab.alamat ? res.data.neonatus_penanggung_jawab.alamat : ''
                    this.form_inpatientNeonatus.neonatus_penanggung_jawab.telepon = res.data.neonatus_penanggung_jawab.telepon ? res.data.neonatus_penanggung_jawab.telepon : ''
                    this.form_inpatientNeonatus.neonatus_penanggung_jawab.penghasilan_perbulan = res.data.neonatus_penanggung_jawab.penghasilan_perbulan ? res.data.neonatus_penanggung_jawab.penghasilan_perbulan : ''
                    //neonatus gastrointestinal
                    this.form_inpatientNeonatus.neonatus_gastrointesnital.stomatitis = res.data.neonatus_gastrointesnital.stomatitis ? res.data.neonatus_gastrointesnital.stomatitis : ''
                    this.form_inpatientNeonatus.neonatus_gastrointesnital.labio_palatochiziz = res.data.neonatus_gastrointesnital.labio_palatochiziz ? res.data.neonatus_gastrointesnital.labio_palatochiziz : ''
                    this.form_inpatientNeonatus.neonatus_gastrointesnital.mukosa_kering = res.data.neonatus_gastrointesnital.mukosa_kering ? res.data.neonatus_gastrointesnital.mukosa_kering : ''
                    this.form_inpatientNeonatus.neonatus_gastrointesnital.pendarahan_gusi = res.data.neonatus_gastrointesnital.pendarahan_gusi ? res.data.neonatus_gastrointesnital.pendarahan_gusi : ''
                    this.form_inpatientNeonatus.neonatus_gastrointesnital.muntah = res.data.neonatus_gastrointesnital.muntah ? res.data.neonatus_gastrointesnital.muntah : ''
                    this.form_inpatientNeonatus.neonatus_gastrointesnital.frekuensi_muntah = res.data.neonatus_gastrointesnital.frekuensi_muntah ? res.data.neonatus_gastrointesnital.frekuensi_muntah : ''
                    this.form_inpatientNeonatus.neonatus_gastrointesnital.residu = res.data.neonatus_gastrointesnital.residu ? res.data.neonatus_gastrointesnital.residu : ''
                    this.form_inpatientNeonatus.neonatus_gastrointesnital.asites = res.data.neonatus_gastrointesnital.asites ? res.data.neonatus_gastrointesnital.asites : ''
                    this.form_inpatientNeonatus.neonatus_gastrointesnital.bising_usus = res.data.neonatus_gastrointesnital.bising_usus ? res.data.neonatus_gastrointesnital.bising_usus : ''
                    this.form_inpatientNeonatus.neonatus_gastrointesnital.nyeri_tekan = res.data.neonatus_gastrointesnital.nyeri_tekan ? res.data.neonatus_gastrointesnital.nyeri_tekan : ''
                    this.form_inpatientNeonatus.neonatus_gastrointesnital.limpa = res.data.neonatus_gastrointesnital.limpa ? res.data.neonatus_gastrointesnital.limpa : ''
                    this.form_inpatientNeonatus.neonatus_gastrointesnital.hepar = res.data.neonatus_gastrointesnital.hepar ? res.data.neonatus_gastrointesnital.hepar : ''
                    this.form_inpatientNeonatus.neonatus_gastrointesnital.lokasi_nyeri_tekan = res.data.neonatus_gastrointesnital.lokasi_nyeri_tekan ? res.data.neonatus_gastrointesnital.lokasi_nyeri_tekan : ''
                })
            }
        },

        resetInpatientNeonatusForm() {
            this.form_inpatientNeonatus.id = ''
            this.form_inpatientNeonatus.register_destination_id = ''
            this.form_inpatientNeonatus.nurse_id = ''
            //riwayat kesehatan bayi dan ibu
            this.form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.hasil_laboratorium_ibu = ''
            this.form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.golongan_darah_ibu = ''
            this.form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.riwayat_persalinan = ''
            this.form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.riwayat_kelahiran = ''
            this.form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.jenis_kelamin_bayi = ''
            this.form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.pemberian_obat_setelah_lahir = ''
            this.form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.diagnosa_partus = ''
            this.form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.riwayat_penyakit_ibu_setelah_hamil = ''
            this.form_inpatientNeonatus.neonatus_riwayat_kesehatan_bayi_ibu.riwayat_kehamilan = ''
            //neonatus kebutuhan edukasi orang tua
            this.form_inpatientNeonatus.neonatus_kebutuhan_edukasi_orang_tua.bicara = ''
            this.form_inpatientNeonatus.neonatus_kebutuhan_edukasi_orang_tua.bahasa_sehari_hari = ''
            this.form_inpatientNeonatus.neonatus_kebutuhan_edukasi_orang_tua.perlu_penerjemah = ''
            this.form_inpatientNeonatus.neonatus_kebutuhan_edukasi_orang_tua.tingkat_pendidikan = ''
            this.form_inpatientNeonatus.neonatus_kebutuhan_edukasi_orang_tua.keluarga_menginginkan_informasi_tentang_proses_penyakit = ''
            //neonatus spiritual
            this.form_inpatientNeonatus.neonatus_spiritual.agama = ''
            this.form_inpatientNeonatus.neonatus_spiritual.permintaan_khusus_terkait_dengan_adat = ''
            //neonatus sosial budaya
            this.form_inpatientNeonatus.neonatus_sosial_budaya.suku_bangsa = ''
            this.form_inpatientNeonatus.neonatus_sosial_budaya.warga_negara = ''
            this.form_inpatientNeonatus.neonatus_sosial_budaya.tempat_tinggal = ''
            this.form_inpatientNeonatus.neonatus_sosial_budaya.yang_merawat_bayi = ''
            this.form_inpatientNeonatus.neonatus_sosial_budaya.orang_tua = ''
            this.form_inpatientNeonatus.neonatus_sosial_budaya.berkunjung = ''
            this.form_inpatientNeonatus.neonatus_sosial_budaya.menyentuh = ''
            this.form_inpatientNeonatus.neonatus_sosial_budaya.menggendong = ''
            this.form_inpatientNeonatus.neonatus_sosial_budaya.kontak_mata = ''
            this.form_inpatientNeonatus.neonatus_sosial_budaya.berbicara = ''
            this.form_inpatientNeonatus.neonatus_sosial_budaya.lainnya = ''
            //neonatus paru
            this.form_inpatientNeonatus.neonatus_paru.pergerakan_paru = ''
            this.form_inpatientNeonatus.neonatus_paru.frekuensi_pernapasan_paru = ''
            this.form_inpatientNeonatus.neonatus_paru.retraksi = ''
            this.form_inpatientNeonatus.neonatus_paru.nch = ''
            this.form_inpatientNeonatus.neonatus_paru.grunting = ''
            this.form_inpatientNeonatus.neonatus_paru.dyspnoe = ''
            this.form_inpatientNeonatus.neonatus_paru.tachynoe = ''
            this.form_inpatientNeonatus.neonatus_paru.gargling_napas_tambahan = ''
            this.form_inpatientNeonatus.neonatus_paru.snooring_napas_tambahan = ''
            this.form_inpatientNeonatus.neonatus_paru.stridor_napas_tambahan = ''
            this.form_inpatientNeonatus.neonatus_paru.peruksi_paru_paru = ''
            this.form_inpatientNeonatus.neonatus_paru.ronkhi_paru_kanan = ''
            this.form_inpatientNeonatus.neonatus_paru.wheezing_paru_kanan = ''
            this.form_inpatientNeonatus.neonatus_paru.lainnya_paru_kanan = ''
            this.form_inpatientNeonatus.neonatus_paru.perkusi_paru_kiri = ''
            this.form_inpatientNeonatus.neonatus_paru.ronkhi_paru_kiri = ''
            this.form_inpatientNeonatus.neonatus_paru.wheezing_paru_kiri = ''
            this.form_inpatientNeonatus.neonatus_paru.lainnya_paru_kiri = ''
            //neonatus down score
            this.form_inpatientNeonatus.neonatus_down_score.frekuensi_napas = ''
            this.form_inpatientNeonatus.neonatus_down_score.retraksi = ''
            this.form_inpatientNeonatus.neonatus_down_score.sianosis = ''
            this.form_inpatientNeonatus.neonatus_down_score.air_entry = ''
            this.form_inpatientNeonatus.neonatus_down_score.merintih = ''
            //neonatus_eliminasi
            this.form_inpatientNeonatus.neonatus_eliminasi.anus = ''
            this.form_inpatientNeonatus.neonatus_eliminasi.defaksi_anus = ''
            this.form_inpatientNeonatus.neonatus_eliminasi.defaksi_stoma = ''
            this.form_inpatientNeonatus.neonatus_eliminasi.frekuensi_defaksi = ''
            this.form_inpatientNeonatus.neonatus_eliminasi.konsistensi_defaksi = ''
            this.form_inpatientNeonatus.neonatus_eliminasi.karakteristik_fases = ''
            this.form_inpatientNeonatus.neonatus_eliminasi.urin = ''
            this.form_inpatientNeonatus.neonatus_eliminasi.diuresis = ''
            //neonatus genitalia
            this.form_inpatientNeonatus.neonatus_genitalia.jenis_kelamin = ''
            this.form_inpatientNeonatus.neonatus_genitalia.kelainan = ''
            this.form_inpatientNeonatus.neonatus_genitalia.testis_laki_laki = ''
            this.form_inpatientNeonatus.neonatus_genitalia.penis_laki_laki = ''
            this.form_inpatientNeonatus.neonatus_genitalia.labio_perempuan = ''
            this.form_inpatientNeonatus.neonatus_genitalia.lainnya = ''
            //neonatus apgar score
            this.form_inpatientNeonatus.neonatus_apgar_score.menit_ke = ''
            this.form_inpatientNeonatus.neonatus_apgar_score.denyut_jantung = ''
            this.form_inpatientNeonatus.neonatus_apgar_score.usaha_napas = ''
            this.form_inpatientNeonatus.neonatus_apgar_score.tonus_otot = ''
            this.form_inpatientNeonatus.neonatus_apgar_score.refleks = ''
            //neonatus kardiologi
            this.form_inpatientNeonatus.neonatus_kardiologi.kelainan_jantung = ''
            this.form_inpatientNeonatus.neonatus_kardiologi.denyut_jantung = ''
            this.form_inpatientNeonatus.neonatus_kardiologi.type_denyut_jantung = ''
            this.form_inpatientNeonatus.neonatus_kardiologi.murmur = ''
            this.form_inpatientNeonatus.neonatus_kardiologi.keterangan_murmur = ''
            this.form_inpatientNeonatus.neonatus_kardiologi.gallop = ''
            this.form_inpatientNeonatus.neonatus_kardiologi.keterangan_gallop = ''
            this.form_inpatientNeonatus.neonatus_kardiologi.sianosis = ''
            this.form_inpatientNeonatus.neonatus_kardiologi.pucat = ''
            this.form_inpatientNeonatus.neonatus_kardiologi.crt = ''
            this.form_inpatientNeonatus.neonatus_kardiologi.akral = ''
            //neonatus skala nyeri
            this.form_inpatientNeonatus.neonatus_skala_nyeri.nama = ''
            this.form_inpatientNeonatus.neonatus_skala_nyeri.skor = ''
            //neonatus trauma kulit
            this.form_inpatientNeonatus.neonatus_trauma_kulit.usia_gestasi = ''
            this.form_inpatientNeonatus.neonatus_trauma_kulit.status_mental = ''
            this.form_inpatientNeonatus.neonatus_trauma_kulit.mobilisasi = ''
            this.form_inpatientNeonatus.neonatus_trauma_kulit.aktivitas = ''
            this.form_inpatientNeonatus.neonatus_trauma_kulit.nutrisi = ''
            this.form_inpatientNeonatus.neonatus_trauma_kulit.kelembaban = ''
            //neonatus alergi reaksi
            this.form_inpatientNeonatus.neonatus_alergi_reaksi.makanan = ''
            this.form_inpatientNeonatus.neonatus_alergi_reaksi.makanan_reaksi = ''
            this.form_inpatientNeonatus.neonatus_alergi_reaksi.obat = ''
            this.form_inpatientNeonatus.neonatus_alergi_reaksi.obat_reaksi = ''
            this.form_inpatientNeonatus.neonatus_alergi_reaksi.lainnya = ''
            this.form_inpatientNeonatus.neonatus_alergi_reaksi.lainnya_reaksi = ''
            this.form_inpatientNeonatus.neonatus_alergi_reaksi.klip_tanda_alergi = ''
            this.form_inpatientNeonatus.neonatus_alergi_reaksi.beritahu_dokter = ''
            this.form_inpatientNeonatus.neonatus_alergi_reaksi.beritahu_farmasi = ''
            this.form_inpatientNeonatus.neonatus_alergi_reaksi.beritahu_dietisien = ''
            //neonatus muskoloskeletal
            this.form_inpatientNeonatus.neonatus_muskuloskeletal.tonus = ''
            this.form_inpatientNeonatus.neonatus_muskuloskeletal.kelainan_tulang = ''
            this.form_inpatientNeonatus.neonatus_muskuloskeletal.gerakan_bayi = ''
            //neonatus penanggung jawab
            this.form_inpatientNeonatus.neonatus_penanggung_jawab.wali = ''
            this.form_inpatientNeonatus.neonatus_penanggung_jawab.nama = ''
            this.form_inpatientNeonatus.neonatus_penanggung_jawab.usia = ''
            this.form_inpatientNeonatus.neonatus_penanggung_jawab.perkawinan_ke = ''
            this.form_inpatientNeonatus.neonatus_penanggung_jawab.pekerjaan = ''
            this.form_inpatientNeonatus.neonatus_penanggung_jawab.pendidikan_terakhir = ''
            this.form_inpatientNeonatus.neonatus_penanggung_jawab.alamat = ''
            this.form_inpatientNeonatus.neonatus_penanggung_jawab.telepon = ''
            this.form_inpatientNeonatus.neonatus_penanggung_jawab.penghasilan_perbulan = ''
            //neonatus gastrointestinal
            this.form_inpatientNeonatus.neonatus_gastrointesnital.stomatitis = ''
            this.form_inpatientNeonatus.neonatus_gastrointesnital.labio_palatochiziz = ''
            this.form_inpatientNeonatus.neonatus_gastrointesnital.mukosa_kering = ''
            this.form_inpatientNeonatus.neonatus_gastrointesnital.pendarahan_gusi = ''
            this.form_inpatientNeonatus.neonatus_gastrointesnital.muntah = ''
            this.form_inpatientNeonatus.neonatus_gastrointesnital.frekuensi_muntah = ''
            this.form_inpatientNeonatus.neonatus_gastrointesnital.residu = ''
            this.form_inpatientNeonatus.neonatus_gastrointesnital.asites = ''
            this.form_inpatientNeonatus.neonatus_gastrointesnital.bising_usus = ''
            this.form_inpatientNeonatus.neonatus_gastrointesnital.nyeri_tekan = ''
            this.form_inpatientNeonatus.neonatus_gastrointesnital.limpa = ''
            this.form_inpatientNeonatus.neonatus_gastrointesnital.hepar = ''
            this.form_inpatientNeonatus.neonatus_gastrointesnital.lokasi_nyeri_tekan = ''
        },

        // FORM Child Inpatient
        validationNurseInpatientChild(nurse_id) {
            this.form_inpatientChild.register_destination_id = this.$route.params.id
            this.form_inpatientChild.nurse_id = nurse_id
            //pemeriksaan fisik gastrointestinal
            this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.mulut = this.option_anak_pemeriksaan_fisik_gastrointestinal_mulut_select !== 7 ? this.option_anak_pemeriksaan_fisik_gastrointestinal_mulut_select : this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.mulut
            this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.tenggorokan = this.option_anak_pemeriksaan_fisik_gastrointestinal_tenggorokan_select !== 3 ? this.option_anak_pemeriksaan_fisik_gastrointestinal_mulut_select : this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.tenggorokan
            this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.gigi = this.option_anak_pemeriksaan_fisik_gastrointestinal_gigi_select !== 7 ? this.option_anak_pemeriksaan_fisik_gastrointestinal_gigi_select : this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.gigi
            this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.abdomen = this.option_anak_pemeriksaan_fisik_gastrointestinal_abdomen_select !== 4 && this.option_anak_pemeriksaan_fisik_gastrointestinal_abdomen_select !== 2 && this.option_anak_pemeriksaan_fisik_gastrointestinal_abdomen_select !== 3 ? this.option_anak_pemeriksaan_fisik_gastrointestinal_abdomen_select : this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.abdomen
            this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.lidah = this.option_anak_pemeriksaan_fisik_gastrointestinal_lidah_select !== 4 ? this.option_anak_pemeriksaan_fisik_gastrointestinal_lidah_select : this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.lidah
            //pemeriksaan fisik neorologi
            this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.bicara = this.option_anak_pemeriksaan_fisik_neorologi_bicara_artikulasi_select !== 2 ? this.option_anak_pemeriksaan_fisik_neorologi_bicara_artikulasi_select : this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.bicara
            this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.menggunakan_alat_bantu_penglihatan = this.option_anak_pemeriksaan_fisik_neorologi_menggunakan_alat_bantu_penglihatan_select !== 2 ? this.option_anak_pemeriksaan_fisik_neorologi_menggunakan_alat_bantu_penglihatan_select : this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.menggunakan_alat_bantu_penglihatan
            this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.motorik = this.option_anak_pemeriksaan_fisik_neorologi_motorik_select !== 5 ? this.option_anak_pemeriksaan_fisik_neorologi_motorik_select : this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.motorik
            this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.pendengaran = this.option_anak_pemeriksaan_fisik_neorologi_pendengaran_select !== 6 ? this.option_anak_pemeriksaan_fisik_neorologi_pendengaran_select : this.form.child_pemeriksaan_fisik_neorologi.pendengaran
            this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.sensorik = this.option_anak_pemeriksaan_fisik_neorologi_sensorik_select !== 4 ? this.option_anak_pemeriksaan_fisik_neorologi_sensorik_select : this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.sensorik
            //pemeriksaan fisik integument
            this.form_inpatientChild.child_pemeriksaan_fisik_integument.kulit = this.option_anak_pemeriksaan_fisik_integument_kulit_select !== 6 ? this.option_anak_pemeriksaan_fisik_integument_kulit_select : this.form_inpatientChild.child_pemeriksaan_fisik_integument.kulit
            this.form_inpatientChild.child_pemeriksaan_fisik_integument.tugor = this.option_anak_pemeriksaan_fisik_integument_tugor_select !== 5 ? this.option_anak_pemeriksaan_fisik_integument_tugor_select : this.form_inpatientChild.child_pemeriksaan_fisik_integument.tugor
            //pemeriksaan fisik kardiovaskuler
            this.form_inpatientChild.child_pemeriksaan_fisik_kardiovaskuler.warna_kulit = this.option_anak_pemeriksaan_fisik_kardiovaskuler_warna_kulit_select !== 5 ? this.option_anak_pemeriksaan_fisik_kardiovaskuler_warna_kulit_select : this.form_inpatientChild.child_pemeriksaan_fisik_kardiovaskuler.warna_kulit
            this.form_inpatientChild.child_pemeriksaan_fisik_kardiovaskuler.nyeri_dada = this.option_anak_pemeriksaan_fisik_kardiovaskuler_nyeri_dada_select !== 2 ? this.option_anak_pemeriksaan_fisik_kardiovaskuler_nyeri_dada_select : this.form_inpatientChild.child_pemeriksaan_fisik_kardiovaskuler.nyeri_dada
            this.form_inpatientChild.child_pemeriksaan_fisik_kardiovaskuler.pulasi = this.option_anak_pemeriksaan_fisik_kardiovaskuler_pulasi_select !== 3 ? this.option_anak_pemeriksaan_fisik_kardiovaskuler_pulasi_select : this.form_inpatientChild.child_pemeriksaan_fisik_kardiovaskuler.pulasi
            this.form_inpatientChild.child_pemeriksaan_fisik_kardiovaskuler.sirkulasi = this.option_anak_pemeriksaan_fisik_kardiovaskuler_sirkulasi_select !== 5 ? this.option_anak_pemeriksaan_fisik_kardiovaskuler_sirkulasi_select : this.form_inpatientChild.child_pemeriksaan_fisik_kardiovaskuler.sirkulasi
            //pemeriksaan fisik respirasi
            this.form_inpatientChild.child_pemeriksaan_fisik_respirasi.kesulitan_bernafas = this.option_anak_pemeriksaan_fisik_respirasi_kesulitan_bernafas_select !== 2 && this.option_anak_pemeriksaan_fisik_respirasi_kesulitan_bernafas_select !== 3 ? this.option_anak_pemeriksaan_fisik_respirasi_kesulitan_bernafas_select : this.form_inpatientChild.child_pemeriksaan_fisik_respirasi.kesulitan_bernafas
            this.form_inpatientChild.child_pemeriksaan_fisik_respirasi.batuk = this.option_anak_pemeriksaan_fisik_respirasi_batuk_select !== 2 ? this.option_anak_pemeriksaan_fisik_respirasi_batuk_select : this.form_inpatientChild.child_pemeriksaan_fisik_respirasi.batuk
            this.form_inpatientChild.child_pemeriksaan_fisik_respirasi.jenis_pernafasan = this.option_anak_pemeriksaan_fisik_respirasi_jenis_pernafasan_select !== 3 ? this.option_anak_pemeriksaan_fisik_respirasi_jenis_pernafasan_select : this.form_inpatientChild.child_pemeriksaan_fisik_respirasi.jenis_pernafasan
            //pemeriksaan fisik gastrointestinal
            this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.mulut = this.option_anak_pemeriksaan_fisik_gastrointestinal_mulut_select !== 7 ? this.option_anak_pemeriksaan_fisik_gastrointestinal_mulut_select : this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.mulut
            this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.tenggorokan = this.option_anak_pemeriksaan_fisik_gastrointestinal_tenggorokan_select !== 3 ? this.option_anak_pemeriksaan_fisik_gastrointestinal_tenggorokan_select : this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.tenggorokan
            this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.gigi = this.option_anak_pemeriksaan_fisik_gastrointestinal_gigi_select !== 7 ? this.option_anak_pemeriksaan_fisik_gastrointestinal_gigi_select : this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.gigi
            this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.abdomen = this.option_anak_pemeriksaan_fisik_gastrointestinal_abdomen_select !== 4 ? this.option_anak_pemeriksaan_fisik_gastrointestinal_abdomen_select : this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.abdomen
            this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.lidah = this.option_anak_pemeriksaan_fisik_gastrointestinal_lidah_select !== 4 ? this.option_anak_pemeriksaan_fisik_gastrointestinal_lidah_select : this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.lidah
            //penilaian persistem umum
            this.form_inpatientChild.child_penilaian_persistem_umum.riwayat_merokok = this.option_anak_penilaian_persistem_umum_riwayat_merokok_select !== 2 ? this.option_anak_penilaian_persistem_umum_riwayat_merokok_select : this.form_inpatientChild.child_penilaian_persistem_umum.riwayat_merokok
            this.form_inpatientChild.child_penilaian_persistem_umum.riwayat_minuman_keras = this.option_anak_penilaian_persistem_umum_riwayat_minuman_keras_select !== 2 ? this.option_anak_penilaian_persistem_umum_riwayat_minuman_keras_select : this.form_inpatientChild.child_penilaian_persistem_umum.riwayat_minuman_keras
            this.form_inpatientChild.child_penilaian_persistem_umum.riwayat_penggunaan_obat_penenang = this.option_anak_penilaian_persistem_umum_riwayat_penggunaan_obat_penenang_select !== 2 ? this.option_anak_penilaian_persistem_umum_riwayat_penggunaan_obat_penenang_select : this.form_inpatientChild.child_penilaian_persistem_umum.riwayat_penggunaan_obat_penenang
            //penilaian persistem asesmen fungsi
            this.form_inpatientChild.child_penilaian_persistem_asesmen_fungsi.ekstremitas_atas = this.option_anak_penilaian_persistem_asesmen_fungsi_ekstremitas_atas_select !== 6 ? this.option_anak_penilaian_persistem_asesmen_fungsi_ekstremitas_atas_select : this.form_inpatientChild.child_penilaian_persistem_asesmen_fungsi.ekstremitas_atas
            this.form_inpatientChild.child_penilaian_persistem_asesmen_fungsi.kemampuan_menggenggam = this.option_anak_penilaian_persistem_asesmen_fungsi_kemampuan_menggenggam_select !== 3 && this.option_anak_penilaian_persistem_asesmen_fungsi_kemampuan_menggenggam_select !== 4 ? this.option_anak_penilaian_persistem_asesmen_fungsi_kemampuan_menggenggam_select : this.form_inpatientChild.child_penilaian_persistem_asesmen_fungsi.kemampuan_menggenggam
            //penilaian persistem keadaan mental dan psikologis
            this.form_inpatientChild.child_penilaian_persistem_keadaan_mental_dan_psikologis.status_psikologi = this.option_anak_penilaian_persistem_keadaan_mental_psikologis_status_psikologis_select !== 7 ? this.option_anak_penilaian_persistem_keadaan_mental_psikologis_status_psikologis_select : this.form_inpatientChild.child_penilaian_persistem_keadaan_mental_dan_psikologis.status_psikologi
            //penilaian persistem eliminasi
            this.form_inpatientChild.child_penilaian_persistem_eliminasi.bab = this.option_anak_penilaian_persistem_eliminasi_bab_select !== 7 ? this.option_anak_penilaian_persistem_eliminasi_bab_select : this.form_inpatientChild.child_penilaian_persistem_eliminasi.bab
            // console.log({child: this.form_inpatientChild})
            try {
                this.disabled.main = true
                this.$store.dispatch('nurse/formPostPutNurseInpatientChild', this.form_inpatientChild).then((response) => {
                    this.resetInpatientChildForm()
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.disabled.main = false
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error at: ', error)
                this.disabled.main = false
            }
        },

        fillChildInpatientForm(nurse_id) {
            if(nurse_id){
                this.$store.dispatch('nurse/getXamChildInpatientDetail', { nurse_id: nurse_id }).then((res) => {
                    console.log({child: res})
                    this.form_inpatientChild.id = res.data.id ? res.data.id : ''
                    this.form_inpatientChild.register_destination_id = res.data.register_destination_id ? res.data.register_destination_id : ''
                    this.form_inpatientChild.nurse_id = res.data.nurse_id ? res.data.nurse_id : ''
                    //umum: riwayat kesehatan dahulu
                    this.form_inpatientChild.child_riwayat_kesehatan_dahulu.jenis_persalinan = Number(res.data.child_anak_riwayat_kesehatan_dahulu.jenis_persalinan) ? Number(res.data.child_anak_riwayat_kesehatan_dahulu.jenis_persalinan): ''
                    this.form_inpatientChild.child_riwayat_kesehatan_dahulu.keterangan_lahir = res.data.child_anak_riwayat_kesehatan_dahulu.keterangan_lahir ? res.data.child_anak_riwayat_kesehatan_dahulu.keterangan_lahir: ''
                    this.form_inpatientChild.child_riwayat_kesehatan_dahulu.lingkar_kepala = res.data.child_anak_riwayat_kesehatan_dahulu.lingkar_kepala ? res.data.child_anak_riwayat_kesehatan_dahulu.lingkar_kepala : ''
                    this.form_inpatientChild.child_riwayat_kesehatan_dahulu.berat_badan_lahir = res.data.child_anak_riwayat_kesehatan_dahulu.berat_badan_lahir ? res.data.child_anak_riwayat_kesehatan_dahulu.berat_badan_lahir : ''
                    this.form_inpatientChild.child_riwayat_kesehatan_dahulu.tumbuh_kembang = res.data.child_anak_riwayat_kesehatan_dahulu.tumbuh_kembang ? res.data.child_anak_riwayat_kesehatan_dahulu.tumbuh_kembang : ''
                    this.form_inpatientChild.child_riwayat_kesehatan_dahulu.lila = res.data.child_anak_riwayat_kesehatan_dahulu.lila ? res.data.child_anak_riwayat_kesehatan_dahulu.lila : ''
                    this.form_inpatientChild.child_riwayat_kesehatan_dahulu.tinggi_badan_lahir = res.data.child_anak_riwayat_kesehatan_dahulu.tinggi_badan_lahir ? res.data.child_anak_riwayat_kesehatan_dahulu.tinggi_badan_lahir : ''
                    this.form_inpatientChild.child_riwayat_kesehatan_dahulu.tinggi_badan = res.data.child_anak_riwayat_kesehatan_dahulu.tinggi_badan ? res.data.child_anak_riwayat_kesehatan_dahulu.tinggi_badan : ''
                    this.form_inpatientChild.child_riwayat_kesehatan_dahulu.imt = res.data.child_anak_riwayat_kesehatan_dahulu.imt ? res.data.child_anak_riwayat_kesehatan_dahulu.imt : ''
                    this.form_inpatientChild.child_riwayat_kesehatan_dahulu.status_gizi = Number(res.data.child_anak_riwayat_kesehatan_dahulu.status_gizi) ? Number(res.data.child_anak_riwayat_kesehatan_dahulu.status_gizi) : ''
                    this.form_inpatientChild.child_riwayat_kesehatan_dahulu.berat_badan = res.data.child_anak_riwayat_kesehatan_dahulu.berat_badan ? res.data.child_anak_riwayat_kesehatan_dahulu.berat_badan : ''
                    this.form_inpatientChild.child_riwayat_kesehatan_dahulu.perawakan = res.data.child_anak_riwayat_kesehatan_dahulu.perawakan ? res.data.child_anak_riwayat_kesehatan_dahulu.perawakan : ''
                    //umum: tumbuh kembang
                    this.form_inpatientChild.child_riwayat_tumbuh_kembang.bb_anak_saat_lahir = res.data.child_riwayat_tumbuh_kembang.bb_anak_saat_lahir ? res.data.child_riwayat_tumbuh_kembang.bb_anak_saat_lahir : ''
                    this.form_inpatientChild.child_riwayat_tumbuh_kembang.pb_anak_saat_lahir = res.data.child_riwayat_tumbuh_kembang.pb_anak_saat_lahir ?  res.data.child_riwayat_tumbuh_kembang.pb_anak_saat_lahir : ''
                    this.form_inpatientChild.child_riwayat_tumbuh_kembang.asi_sampai_umur = res.data.child_riwayat_tumbuh_kembang.asi_sampai_umur ? res.data.child_riwayat_tumbuh_kembang.asi_sampai_umur : ''
                    this.form_inpatientChild.child_riwayat_tumbuh_kembang.susu_formula = res.data.child_riwayat_tumbuh_kembang.susu_formula ? res.data.child_riwayat_tumbuh_kembang.susu_formula : ''
                    this.form_inpatientChild.child_riwayat_tumbuh_kembang.makanan_padat = res.data.child_riwayat_tumbuh_kembang.makanan_padat ? res.data.child_riwayat_tumbuh_kembang.makanan_padat : ''
                    this.form_inpatientChild.child_riwayat_tumbuh_kembang.makanan_tambahan = res.data.child_riwayat_tumbuh_kembang.makanan_tambahan ? res.data.child_riwayat_tumbuh_kembang.makanan_tambahan : ''
                    this.form_inpatientChild.child_riwayat_tumbuh_kembang.tengkurap = res.data.child_riwayat_tumbuh_kembang.tengkurap ? res.data.child_riwayat_tumbuh_kembang.tengkurap : ''
                    this.form_inpatientChild.child_riwayat_tumbuh_kembang.duduk = res.data.child_riwayat_tumbuh_kembang.duduk ? res.data.child_riwayat_tumbuh_kembang.duduk : ''
                    this.form_inpatientChild.child_riwayat_tumbuh_kembang.merangkak = res.data.child_riwayat_tumbuh_kembang.merangkak ? res.data.child_riwayat_tumbuh_kembang.merangkak : ''
                    this.form_inpatientChild.child_riwayat_tumbuh_kembang.berdiri = res.data.child_riwayat_tumbuh_kembang.berdiri ? res.data.child_riwayat_tumbuh_kembang.berdiri : ''
                    this.form_inpatientChild.child_riwayat_tumbuh_kembang.berjalan = res.data.child_riwayat_tumbuh_kembang.berjalan ? res.data.child_riwayat_tumbuh_kembang.berjalan : ''
                    //pemeriksaan fisik: ttv
                    this.form_inpatientChild.child_pemeriksaan_fisik_ttv.tekanan_darah = res.data.child_pemeriksaan_fisik_ttv.tekanan_darah ? res.data.child_pemeriksaan_fisik_ttv.tekanan_darah : ''
                    this.form_inpatientChild.child_pemeriksaan_fisik_ttv.suhu = res.data.child_pemeriksaan_fisik_ttv.suhu ? res.data.child_pemeriksaan_fisik_ttv.suhu : ''
                    this.form_inpatientChild.child_pemeriksaan_fisik_ttv.nadi = res.data.child_pemeriksaan_fisik_ttv.nadi ? res.data.child_pemeriksaan_fisik_ttv.nadi : ''
                    this.form_inpatientChild.child_pemeriksaan_fisik_ttv.kesadaran = res.data.child_pemeriksaan_fisik_ttv.kesadaran ? res.data.child_pemeriksaan_fisik_ttv.kesadaran : ''
                    //pemeriksaan fisik: berat badan
                    this.form_inpatientChild.child_pemeriksaan_fisik_berat_badan.berat_badan = res.data.child_pemeriksaan_fisik_berat_badan.berat_badan ? res.data.child_pemeriksaan_fisik_berat_badan.berat_badan : ''
                    this.form_inpatientChild.child_pemeriksaan_fisik_berat_badan.tinggi_badan = res.data.child_pemeriksaan_fisik_berat_badan.tinggi_badan ? res.data.child_pemeriksaan_fisik_berat_badan.tinggi_badan : ''
                    this.form_inpatientChild.child_pemeriksaan_fisik_berat_badan.lla = res.data.child_pemeriksaan_fisik_berat_badan.lla ? res.data.child_pemeriksaan_fisik_berat_badan.lla : ''
                    //pemeriksaan fisik: neorologi
                    this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.penglihatan = Number(res.data.child_pemeriksaan_fisik_neorologi.penglihatan) ? Number(res.data.child_pemeriksaan_fisik_neorologi.penglihatan) : ''
                    this.option_anak_pemeriksaan_fisik_neorologi_menggunakan_alat_bantu_penglihatan_select !== 2 ? this.option_anak_pemeriksaan_fisik_neorologi_menggunakan_alat_bantu_penglihatan_select = Number(res.data.child_pemeriksaan_fisik_neorologi.menggunakan_alat_bantu_penglihatan) : this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.menggunakan_alat_bantu_penglihatan = Number(res.data.child_pemeriksaan_fisik_neorologi.menggunakan_alat_bantu_penglihatan)
                    this.option_anak_pemeriksaan_fisik_neorologi_pendengaran_select !== 6 ? this.option_anak_pemeriksaan_fisik_neorologi_pendengaran_select = Number(res.data.child_pemeriksaan_fisik_neorologi.pendengaran) : this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.pendengaran = Number(res.data.child_pemeriksaan_fisik_neorologi.pendengaran)
                    this.option_anak_pemeriksaan_fisik_neorologi_bicara_artikulasi_select !== 2 ? this.option_anak_pemeriksaan_fisik_neorologi_bicara_artikulasi_select = Number(res.data.child_pemeriksaan_fisik_neorologi.bicara) : this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.bicara = Number(res.data.child_pemeriksaan_fisik_neorologi.bicara)
                    this.option_anak_pemeriksaan_fisik_neorologi_sensorik_select !== 4 ? this.option_anak_pemeriksaan_fisik_neorologi_sensorik_select = Number(res.data.child_pemeriksaan_fisik_neorologi.sensorik) : this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.sensorik = Number(res.data.child_pemeriksaan_fisik_neorologi.sensorik)
                    this.option_anak_pemeriksaan_fisik_neorologi_motorik_select !== 5 ? this.option_anak_pemeriksaan_fisik_neorologi_motorik_select = Number(res.data.child_pemeriksaan_fisik_neorologi.motorik) : this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.motorik = Number(res.data.child_pemeriksaan_fisik_neorologi.motorik)
                    this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.kekuatan_otot = Number(res.data.child_pemeriksaan_fisik_neorologi.kekuatan_otot) ? Number(res.data.child_pemeriksaan_fisik_neorologi.kekuatan_otot) : ''
                    this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.menggunakan_alat_bantu_pendengaran = Number(res.data.child_pemeriksaan_fisik_neorologi.menggunakan_alat_bantu_pendengaran) ? Number(res.data.child_pemeriksaan_fisik_neorologi.menggunakan_alat_bantu_pendengaran) : ''
                    //pemeriksaan fisik: integument
                    this.form_inpatientChild.child_pemeriksaan_fisik_integument.warna_kulit = Number(res.data.child_pemeriksaan_fisik_integument.warna_kulit) ? Number(res.data.child_pemeriksaan_fisik_integument.warna_kulit) : ''
                    this.option_anak_pemeriksaan_fisik_integument_tugor_select !== 5 ? this.option_anak_pemeriksaan_fisik_integument_tugor_select = Number(res.data.child_pemeriksaan_fisik_integument.tugor) : this.form_inpatientChild.child_pemeriksaan_fisik_integument.tugor = Number(res.data.child_pemeriksaan_fisik_integument.tugor)
                    this.option_anak_pemeriksaan_fisik_integument_kulit_select !== 6 ? this.option_anak_pemeriksaan_fisik_integument_kulit_select = Number(res.data.child_pemeriksaan_fisik_integument.kulit) : this.form_inpatientChild.child_pemeriksaan_fisik_integument.kulit = Number(res.data.child_pemeriksaan_fisik_integument.kulit)
                    this.form_inpatientChild.child_pemeriksaan_fisik_integument.kriteria_resiko_dekubitus = Number(res.data.child_pemeriksaan_fisik_integument.kriteria_resiko_dekubitus) ? Number(res.data.child_pemeriksaan_fisik_integument.kriteria_resiko_dekubitus) : ''
                    //pemeriksaan fisik: kardiovaskuler
                    this.option_anak_pemeriksaan_fisik_kardiovaskuler_warna_kulit_select !== 5 ? this.option_anak_pemeriksaan_fisik_kardiovaskuler_warna_kulit_select = Number(res.data.child_pemeriksaan_fisik_kardiovaskuler.warna_kulit) : this.form_inpatientChild.child_pemeriksaan_fisik_kardiovaskuler.warna_kulit = Number(res.data.child_pemeriksaan_fisik_kardiovaskuler.warna_kulit)
                    this.form_inpatientChild.child_pemeriksaan_fisik_kardiovaskuler.denyut_nadi = Number(res.data.child_pemeriksaan_fisik_kardiovaskuler.denyut_nadi) ? Number(res.data.child_pemeriksaan_fisik_kardiovaskuler.denyut_nadi) : ''
                    this.option_anak_pemeriksaan_fisik_kardiovaskuler_sirkulasi_select !== 5 ? this.option_anak_pemeriksaan_fisik_kardiovaskuler_sirkulasi_select = Number(res.data.child_pemeriksaan_fisik_kardiovaskuler.sirkulasi) : this.form_inpatientChild.child_pemeriksaan_fisik_kardiovaskuler.sirkulasi = Number(res.data.child_pemeriksaan_fisik_kardiovaskuler.sirkulasi)
                    this.option_anak_pemeriksaan_fisik_kardiovaskuler_nyeri_dada_select !== 2 ? this.option_anak_pemeriksaan_fisik_kardiovaskuler_nyeri_dada_select = Number(res.data.child_pemeriksaan_fisik_kardiovaskuler.nyeri_dada) : this.form_inpatientChild.child_pemeriksaan_fisik_kardiovaskuler.nyeri_dada = Number(res.data.child_pemeriksaan_fisik_kardiovaskuler.nyeri_dada)
                    this.option_anak_pemeriksaan_fisik_kardiovaskuler_pulasi_select !== 3 ? this.option_anak_pemeriksaan_fisik_kardiovaskuler_pulasi_select = Number(res.data.child_pemeriksaan_fisik_kardiovaskuler.pulasi) : this.form_inpatientChild.child_pemeriksaan_fisik_kardiovaskuler.pulasi = Number(res.data.child_pemeriksaan_fisik_kardiovaskuler.pulasi)
                    //pemeriksaan fisik: respirasi
                    this.form_inpatientChild.child_pemeriksaan_fisik_respirasi.pola_nafas = Number(res.data.child_pemeriksaan_fisik_respirasi.pola_nafas) ? Number(res.data.child_pemeriksaan_fisik_respirasi.pola_nafas) : ''
                    this.form_inpatientChild.child_pemeriksaan_fisik_respirasi.irama_nafas = Number(res.data.child_pemeriksaan_fisik_respirasi.irama_nafas) ? Number(res.data.child_pemeriksaan_fisik_respirasi.irama_nafas) : ''
                    this.option_anak_pemeriksaan_fisik_respirasi_batuk_select !== 2 ? this.option_anak_pemeriksaan_fisik_respirasi_batuk_select = Number(res.data.child_pemeriksaan_fisik_respirasi.batuk) : this.form_inpatientChild.child_pemeriksaan_fisik_respirasi.batuk = Number(res.data.child_pemeriksaan_fisik_respirasi.batuk)
                    this.form_inpatientChild.child_pemeriksaan_fisik_respirasi.volume_pernafasan = Number(res.data.child_pemeriksaan_fisik_respirasi.volume_pernafasan) ? Number(res.data.child_pemeriksaan_fisik_respirasi.volume_pernafasan) : ''
                    this.option_anak_pemeriksaan_fisik_respirasi_kesulitan_bernafas_select !== 2 ? this.option_anak_pemeriksaan_fisik_respirasi_kesulitan_bernafas_select = Number(res.data.child_pemeriksaan_fisik_respirasi.kesulitan_bernafas) : this.form_inpatientChild.child_pemeriksaan_fisik_respirasi.kesulitan_bernafas = Number(res.data.child_pemeriksaan_fisik_respirasi.kesulitan_bernafas)
                    this.option_anak_pemeriksaan_fisik_respirasi_jenis_pernafasan_select !== 3 ? this.option_anak_pemeriksaan_fisik_respirasi_jenis_pernafasan_select = Number(res.data.child_pemeriksaan_fisik_respirasi.jenis_pernafasan) : this.form_inpatientChild.child_pemeriksaan_fisik_respirasi.jenis_pernafasan = Number(res.data.child_pemeriksaan_fisik_respirasi.jenis_pernafasan)
                    //pemeriksaan fisik: gastrointestinal
                    this.option_anak_pemeriksaan_fisik_gastrointestinal_mulut_select !== 7 ? this.option_anak_pemeriksaan_fisik_gastrointestinal_mulut_select = Number(res.data.child_pemeriksaan_fisik_gastrointestinal.mulut) : this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.mulut = Number(res.data.child_pemeriksaan_fisik_gastrointestinal.mulut)
                    this.option_anak_pemeriksaan_fisik_gastrointestinal_gigi_select !== 7 ? this.option_anak_pemeriksaan_fisik_gastrointestinal_gigi_select = Number(res.data.child_pemeriksaan_fisik_gastrointestinal.gigi) : this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.gigi = Number(res.data.child_pemeriksaan_fisik_gastrointestinal.gigi)
                    this.option_anak_pemeriksaan_fisik_gastrointestinal_lidah_select !== 4 ? this.option_anak_pemeriksaan_fisik_gastrointestinal_lidah_select = Number(res.data.child_pemeriksaan_fisik_gastrointestinal.lidah) : this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.lidah = Number(res.data.child_pemeriksaan_fisik_gastrointestinal.lidah)
                    this.option_anak_pemeriksaan_fisik_gastrointestinal_tenggorokan_select !== 3 ? this.option_anak_pemeriksaan_fisik_gastrointestinal_tenggorokan_select = Number(res.data.child_pemeriksaan_fisik_gastrointestinal.tenggorokan) : this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.tenggorokan = Number(res.data.child_pemeriksaan_fisik_gastrointestinal.tenggorokan)
                    this.option_anak_pemeriksaan_fisik_gastrointestinal_abdomen_select !== 4 ? this.option_anak_pemeriksaan_fisik_gastrointestinal_abdomen_select = Number(res.data.child_pemeriksaan_fisik_gastrointestinal.abdomen) : this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.abdomen = Number(res.data.child_pemeriksaan_fisik_gastrointestinal.abdomen)
                    //penilaian persistem: umum
                    this.form_inpatientChild.child_penilaian_persistem_umum.apakah_ada_nyeri = Number(res.data.child_penilaian_persistem_umum.apakah_ada_nyeri) ? Number(res.data.child_penilaian_persistem_umum.apakah_ada_nyeri) : ''
                    this.option_anak_penilaian_persistem_umum_riwayat_minuman_keras_select !== 2 ? this.option_anak_penilaian_persistem_umum_riwayat_minuman_keras_select = Number(res.data.child_penilaian_persistem_umum.riwayat_minuman_keras) : this.this.form_inpatientChild.child_penilaian_persistem_umum.riwayat_minuman_keras = Number(res.data.child_penilaian_persistem_umum.riwayat_minuman_keras)
                    this.option_anak_penilaian_persistem_umum_riwayat_penggunaan_obat_penenang_select !== 2 ? this.option_anak_penilaian_persistem_umum_riwayat_penggunaan_obat_penenang_select = Number(res.data.child_penilaian_persistem_umum.riwayat_penggunaan_obat_penenang) : this.form_inpatientChild.child_penilaian_persistem_umum.riwayat_penggunaan_obat_penenang = Number(res.data.child_penilaian_persistem_umum.riwayat_penggunaan_obat_penenang)
                    this.option_anak_penilaian_persistem_umum_riwayat_merokok_select !== 2 ? this.option_anak_penilaian_persistem_umum_riwayat_merokok_select = Number(res.data.child_penilaian_persistem_umum.riwayat_merokok) : this.form_inpatientChild.child_penilaian_persistem_umum.riwayat_merokok = Number(res.data.child_penilaian_persistem_umum.riwayat_merokok)
                    this.form_inpatientChild.child_penilaian_persistem_umum.pola_istirahat = Number(res.data.child_penilaian_persistem_umum.pola_istirahat) ? Number(res.data.child_penilaian_persistem_umum.pola_istirahat) : ''
                    //penilaian persistem: asesmen fungsi
                    this.form_inpatientChild.child_penilaian_persistem_asesmen_fungsi.kemampuan_melakukan_aktivitas_sehari = Number(res.data.child_penilaian_persistem_asesmen_fungsi.kemampuan_melakukan_aktivitas_sehari) ? Number(res.data.child_penilaian_persistem_asesmen_fungsi.kemampuan_melakukan_aktivitas_sehari) : ''
                    this.option_anak_penilaian_persistem_asesmen_fungsi_ekstremitas_atas_select !== 6 ? this.option_anak_penilaian_persistem_asesmen_fungsi_ekstremitas_atas_select = Number(res.data.child_penilaian_persistem_asesmen_fungsi.ekstremitas_atas) : this.form_inpatientChild.child_penilaian_persistem_asesmen_fungsi.ekstremitas_atas = Number(res.data.child_penilaian_persistem_asesmen_fungsi.ekstremitas_atas)
                    this.form_inpatientChild.child_penilaian_persistem_asesmen_fungsi.alat_ambulasi = Number(res.data.child_penilaian_persistem_asesmen_fungsi.alat_ambulasi) ? Number(res.data.child_penilaian_persistem_asesmen_fungsi.alat_ambulasi) : ''
                    this.form_inpatientChild.child_penilaian_persistem_asesmen_fungsi.aktivitas = Number(res.data.child_penilaian_persistem_asesmen_fungsi.aktivitas) ? Number(res.data.child_penilaian_persistem_asesmen_fungsi.aktivitas) : ''
                    this.option_anak_penilaian_persistem_asesmen_fungsi_kemampuan_menggenggam_select !== 3 && this.option_anak_penilaian_persistem_asesmen_fungsi_kemampuan_menggenggam_select !== 4 ? this.option_anak_penilaian_persistem_asesmen_fungsi_kemampuan_menggenggam_select = Number(res.data.child_penilaian_persistem_asesmen_fungsi.kemampuan_menggenggam) : this.form_inpatientChild.child_penilaian_persistem_asesmen_fungsi.kemampuan_menggenggam = Number(res.data.child_penilaian_persistem_asesmen_fungsi.kemampuan_menggenggam)
                    //penilaian persistem: keadaan mental dan psikologi
                    this.form_inpatientChild.child_penilaian_persistem_keadaan_mental_dan_psikologis.status_mental = Number(res.data.child_penilaian_persistem_asesmenKeadaan_mental_psikologis.status_mental) ? Number(res.data.child_penilaian_persistem_asesmenKeadaan_mental_psikologis.status_mental) : ''
                    this.option_anak_penilaian_persistem_keadaan_mental_psikologis_status_psikologis_select !== 7 ? this.option_anak_penilaian_persistem_keadaan_mental_psikologis_status_psikologis_select = Number(res.data.child_penilaian_persistem_asesmenKeadaan_mental_psikologis.status_psikologi) : this.form_inpatientChild.child_penilaian_persistem_keadaan_mental_dan_psikologis.status_psikologi = Number(res.data.child_penilaian_persistem_asesmenKeadaan_mental_psikologis.status_psikologi)
                    //penilaian persistem: eliminasi
                    this.option_anak_penilaian_persistem_eliminasi_bab_select !== 7 ? this.option_anak_penilaian_persistem_eliminasi_bab_select = Number(res.data.child_penilaian_persistem_eliminasi.bab) : this.form_inpatientChild.child_penilaian_persistem_eliminasi.bab = Number(res.data.child_penilaian_persistem_eliminasi.bab)
                    this.form_inpatientChild.child_penilaian_persistem_eliminasi.bak = Number(res.data.child_penilaian_persistem_eliminasi.bak) ? Number(res.data.child_penilaian_persistem_eliminasi.bak) : ''
                    //penilaian persistem: seksual
                    this.form_inpatientChild.child_penilaian_persistem_seksual.wanita_haid = Number(res.data.child_penilaian_persistem_seksual.wanita_haid) ? Number(res.data.child_penilaian_persistem_seksual.wanita_haid) : ''
                    this.form_inpatientChild.child_penilaian_persistem_seksual.laki_laki = res.data.child_penilaian_persistem_seksual.laki_laki ? res.data.child_penilaian_persistem_seksual.laki_laki : ''
                })
            }
        },

        resetInpatientChildForm() {
            this.form_inpatientChild.id = ''
            this.form_inpatientChild.register_destination_id = ''
            this.form_inpatientChild.nurse_id = ''
            //umum: riwayat kesehatan dahulu
            this.form_inpatientChild.child_riwayat_kesehatan_dahulu.jenis_persalinan = ''
            this.form_inpatientChild.child_riwayat_kesehatan_dahulu.keterangan_lahir = ''
            this.form_inpatientChild.child_riwayat_kesehatan_dahulu.lingkar_kepala = ''
            this.form_inpatientChild.child_riwayat_kesehatan_dahulu.berat_badan_lahir = ''
            this.form_inpatientChild.child_riwayat_kesehatan_dahulu.tumbuh_kembang = ''
            this.form_inpatientChild.child_riwayat_kesehatan_dahulu.lila = ''
            this.form_inpatientChild.child_riwayat_kesehatan_dahulu.tinggi_badan_lahir = ''
            this.form_inpatientChild.child_riwayat_kesehatan_dahulu.tinggi_badan = ''
            this.form_inpatientChild.child_riwayat_kesehatan_dahulu.imt = ''
            this.form_inpatientChild.child_riwayat_kesehatan_dahulu.status_gizi = ''
            this.form_inpatientChild.child_riwayat_kesehatan_dahulu.berat_badan = ''
            this.form_inpatientChild.child_riwayat_kesehatan_dahulu.perawakan = ''
            //umum: tumbuh kembang
            this.form_inpatientChild.child_riwayat_tumbuh_kembang.bb_anak_saat_lahir = ''
            this.form_inpatientChild.child_riwayat_tumbuh_kembang.pb_anak_saat_lahir = ''
            this.form_inpatientChild.child_riwayat_tumbuh_kembang.asi_sampai_umur = ''
            this.form_inpatientChild.child_riwayat_tumbuh_kembang.susu_formula = ''
            this.form_inpatientChild.child_riwayat_tumbuh_kembang.makanan_padat = ''
            this.form_inpatientChild.child_riwayat_tumbuh_kembang.makanan_tambahan = ''
            this.form_inpatientChild.child_riwayat_tumbuh_kembang.tengkurap = ''
            this.form_inpatientChild.child_riwayat_tumbuh_kembang.duduk = ''
            this.form_inpatientChild.child_riwayat_tumbuh_kembang.merangkak = ''
            this.form_inpatientChild.child_riwayat_tumbuh_kembang.berdiri = ''
            this.form_inpatientChild.child_riwayat_tumbuh_kembang.berjalan = ''
            //pemeriksaan fisik: ttv
            this.form_inpatientChild.child_pemeriksaan_fisik_ttv.tekanan_darah = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_ttv.suhu = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_ttv.nadi = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_ttv.kesadaran = ''
            //pemeriksaan fisik: berat badan
            this.form_inpatientChild.child_pemeriksaan_fisik_berat_badan.berat_badan = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_berat_badan.tinggi_badan = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_berat_badan.lla = ''
            //pemeriksaan fisik: neorologi
            this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.penglihatan = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.menggunakan_alat_bantu_penglihatan = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.pendengaran = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.bicara = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.sensorik = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.motorik = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.kekuatan_otot = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_neorologi.menggunakan_alat_bantu_pendengaran = ''
            //pemeriksaan fisik: integument
            this.form_inpatientChild.child_pemeriksaan_fisik_integument.warna_kulit = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_integument.tugor = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_integument.kulit = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_integument.kriteria_resiko_dekubitus = ''
            //pemeriksaan fisik: kardiovaskuler
            this.form_inpatientChild.child_pemeriksaan_fisik_kardiovaskuler.warna_kulit = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_kardiovaskuler.denyut_nadi = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_kardiovaskuler.sirkulasi = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_kardiovaskuler.nyeri_dada = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_kardiovaskuler.pulasi = ''
            //pemeriksaan fisik: respirasi
            this.form_inpatientChild.child_pemeriksaan_fisik_respirasi.pola_nafas = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_respirasi.irama_nafas = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_respirasi.batuk = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_respirasi.volume_pernafasan = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_respirasi.kesulitan_bernafas = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_respirasi.jenis_pernafasan = ''
            //pemeriksaan fisik: gastrointestinal
            this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.mulut = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.gigi = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.lidah = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.tenggorokan = ''
            this.form_inpatientChild.child_pemeriksaan_fisik_gastrointestinal.abdomen = ''
            //penilaian persistem: umum
            this.form_inpatientChild.child_penilaian_persistem_umum.apakah_ada_nyeri = ''
            this.form_inpatientChild.child_penilaian_persistem_umum.riwayat_minuman_keras = ''
            this.form_inpatientChild.child_penilaian_persistem_umum.riwayat_penggunaan_obat_penenang = ''
            this.form_inpatientChild.child_penilaian_persistem_umum.riwayat_merokok = ''
            this.form_inpatientChild.child_penilaian_persistem_umum.pola_istirahat = ''
            //penilaian persistem: asesmen fungsi
            this.form_inpatientChild.child_penilaian_persistem_asesmen_fungsi.kemampuan_melakukan_aktivitas_sehari = ''
            this.form_inpatientChild.child_penilaian_persistem_asesmen_fungsi.ekstremitas_atas = ''
            this.form_inpatientChild.child_penilaian_persistem_asesmen_fungsi.alat_ambulasi = ''
            this.form_inpatientChild.child_penilaian_persistem_asesmen_fungsi.aktivitas = ''
            this.form_inpatientChild.child_penilaian_persistem_asesmen_fungsi.kemampuan_menggenggam = ''
            //penilaian persistem: keadaan mental dan psikologi
            this.form_inpatientChild.child_penilaian_persistem_keadaan_mental_dan_psikologis.status_mental = ''
            this.form_inpatientChild.child_penilaian_persistem_keadaan_mental_dan_psikologis.status_psikologi = ''
            //penilaian persistem: eliminasi
            this.form_inpatientChild.child_penilaian_persistem_eliminasi.bab = ''
            this.form_inpatientChild.child_penilaian_persistem_eliminasi.bak = ''
            //penilaian persistem: seksual
            this.form_inpatientChild.child_penilaian_persistem_seksual.wanita_haid = ''
            this.form_inpatientChild.child_penilaian_persistem_seksual.laki_laki = ''
        },

        handleNutritionScore() {
            const score_body_mass_index = this.getBodyMassIndexScore(this.form_igd.body_mass_index)
            const score_six_month_lose_weight = this.getSixMonthLoseWeightScore(this.form_igd.six_month_lose_weight)
            const score_lose_weight = this.getLoseWeightScore(this.form_igd.lose_weight)
            const score_patient_food_intake = this.getPatientFoodIntakeScore(this.form_igd.patient_food_intake)
            const score_patient_disease_condition = this.getPatientDiseaseCondition(this.form_igd.patient_disease_condition)
            const score_patient_certain_diets = this.getPatientCertainDiets(this.form_igd.patient_certain_diets)
            
            let result = 0
            result = score_body_mass_index + score_six_month_lose_weight + score_lose_weight + score_patient_food_intake + score_patient_disease_condition + score_patient_certain_diets
            this.form_igd.nutrition_total_score = result.toString()
        },
        getBodyMassIndexScore(value){
            let score = 0
            if (value !== '') {
                if (value == '1') {
                    return score = 0
                } else if (value == '2') {
                    return score = 1
                } else if (value == '3') {
                    return score = 2
                }
            }
            return score = 0
        },
        getSixMonthLoseWeightScore(value){
            let score = 0
            if (value !== '') {
                if (value == '1') {
                    return score = 0
                } else if (value == '2') {
                    return score = 2
                } else if (value == '3') {
                    return score = 0
                }
            }
            return score = 0
        },
        getLoseWeightScore(value){
            let score = 0
            if (value !== '') {
                if (value == '1') {
                    return score = 1
                } else if (value == '2') {
                    return score = 2
                } else if (value == '3') {
                    return score = 3
                } else if (value == '4') {
                    return score = 4
                } else if (value == '5') {
                    return score = 2
                }
            }
            return score = 0
        },
        getPatientFoodIntakeScore(value){
            let score = 0
            if (value !== '') {
                if (value == '1') {
                    return score = 0
                } else if (value == '2') {
                    return score = 1
                } else if (value == '3') {
                    return score = 2
                }
            }
            return score = 0
        },
        getPatientDiseaseCondition(value){
            let score = 0
            if (value !== '') {
                if (value == '1') {
                    return score = 0
                } else if (value == '2') {
                    return score = 2
                }
            }
            return score = 0
        },
        getPatientCertainDiets(value){
            let score = 0
            if (value !== '') {
                if (value == '1') {
                    return score = 0
                } else if (value == '2') {
                    return score = 2
                }
            }
            return score = 0
        },
        
        // INPATIENT NEONATUS
        count_age_neonatus(input_date) {
            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();
            
            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');

            if (years == 0) { //neonatus if is 0 tahun
                return true;
            }else {
                return false;
            }
        },
        
        // INPATIENT CHILD
        count_age_child(input_date) {
            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();
            
            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');

            if (years >= 1 && years <= 17) { //neonatus if under 1 year
                return true;
            }else {
                return false;
            }
        },


        // INPATIENT ADULT
        count_age(input_date) {
            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();
            
            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');

            if (years >= 18 && years <= 60 ) {
                return true;
            } else {
                return false;
            }
        },

        addMedical() {
            let obj = {
                type: this.input_medical.type, 
                explanation: this.input_medical.notes, 
            }
            this.form_inpatientAdult.medical_devices.push(obj)
            this.input_medical.type = ''
            this.input_medical.notes = ''
        },

        removeMedical(index) {
            this.form_inpatientAdult.medical_devices.splice(index, 1)
        },

        addExamination() {
            let obj = {
                type: this.input_examination.type, 
                page: this.input_examination.page, 
                explanation: this.input_examination.explanation, 
            }
            this.form_inpatientAdult.examination_history.push(obj)
            this.input_examination.type = ''
            this.input_examination.page = ''
            this.input_examination.explanation = ''
        },

        removeExamination(index) {
            this.form_inpatientAdult.examination_history.splice(index, 1)
        },

        handleSoreMethodScore () {
            if (this.form_inpatientAdult.wong_baker_score !== '') {
                this.form_inpatientAdult.sore_total_score = this.form_inpatientAdult.wong_baker_score
            }
            
            if (this.form_inpatientAdult.nrs_score !== '') {
                this.form_inpatientAdult.sore_total_score = this.form_inpatientAdult.nrs_score
            }
        },

        // Child riwayat vaksin
        OnRiwayatVaksin(event) {
            localStorage.setItem("vaksin", JSON.stringify(this.form_inpatientChild.child_riwayat_imunisasi));
            this.form_inpatientChild.child_riwayat_imunisasi.nama_vaksin.push({ value: event.value, title: event.title, var_1: event.title, qty: 1, action: 'action' });
        },

        OnDeleteRiwayatVaksin(index) {
            this.form_inpatientChild.child_riwayat_imunisasi.nama_vaksin.splice(index, 1)
        },

        // HEMODIALISIS
        validationNurseHemodialysis(nurse_id){
            this.form_hemodialys.nurse_id = nurse_id
            this.form_hemodialys.registration_destination_id = this.reg_des_id
            
            try {
                this.$store.dispatch('nurse/PostPutNurseHemodialysis', this.form_hemodialys).then((response) => {
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.resetHemodialysForm()
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error at: ', error)
            }
        },
        resetHemodialysForm() {
            this.form_hemodialys.id = ''
            this.form_hemodialys.nurse_id = ""
            this.form_hemodialys.hemodialysis_date = ""
            this.form_hemodialys.hemodialysis_type_id = 0
            this.form_hemodialys.frequency_type_id = 0
            this.form_hemodialys.dialysate_type_id = 0
            this.form_hemodialys.vascular_type_id = 0
            this.form_hemodialys.long_hours = 0
            this.form_hemodialys.blood_velocity = ""
            this.form_hemodialys.initial_dose = ""
            this.form_hemodialys.intermittent = ""
            this.form_hemodialys.ufg = ""
            this.form_hemodialys.dialysate_velocity = ""
            this.form_hemodialys.maintenance = ""
            this.form_hemodialys.lmwh = ""
            this.form_hemodialys.dialysate_temperature = ""
            this.form_hemodialys.vascular_jugularis = ""
            this.form_hemodialys.heparin_type_id = ""
            this.form_hemodialys.without_heparin = ""
            this.form_hemodialys.profiling_type_id = 0
            this.form_hemodialys.ultrafiltration = ""
            this.form_hemodialys.natrium = ""
            this.form_hemodialys.bicarbonate = ""
            this.form_hemodialys.therapy = ""
            this.form_hemodialys.medicine = ""
            this.form_hemodialys.injection = ""
        },
        
        fillHemodialysisForm(nurse_id) {
            if (nurse_id) {
                this.$store.dispatch('nurse/getHemodialysisDetail', { nurse_id: nurse_id }).then((response) => {
                    const res = response[0]

                    this.form_hemodialys.id = res.id ? res.id : ''
                    this.form_hemodialys.registration_destination_id = res.registration_destination_id ? res.registration_destination_id : ""
                    this.form_hemodialys.nurse_id = res.nurse_id ? res.nurse_id : ""
                    this.form_hemodialys.hemodialysis_date = res.hemodialysis_date ? res.hemodialysis_date : ""
                    this.form_hemodialys.hemodialysis_type_id = res.hemodialysis_type_id ? res.hemodialysis_type_id : 0
                    this.form_hemodialys.frequency_type_id = res.frequency_type_id ? res.frequency_type_id : 0
                    this.form_hemodialys.dialysate_type_id = res.dialysate_type_id ? res.dialysate_type_id : 0
                    this.form_hemodialys.vascular_type_id = res.vascular_type_id ? res.vascular_type_id : 0
                    this.form_hemodialys.long_hours = res.long_hours ? res.long_hours : ''
                    this.form_hemodialys.blood_velocity = res.blood_velocity ? res.blood_velocity : ""
                    this.form_hemodialys.initial_dose = res.initial_dose ? res.initial_dose : ""
                    this.form_hemodialys.intermittent = res.intermittent ? res.intermittent : ""
                    this.form_hemodialys.ufg = res.ufg ? res.ufg : ""
                    this.form_hemodialys.dialysate_velocity = res.dialysate_velocity ? res.dialysate_velocity : ""
                    this.form_hemodialys.maintenance = res.maintenance ? res.maintenance : ""
                    this.form_hemodialys.lmwh = res.lmwh ? res.lmwh : ""
                    this.form_hemodialys.dialysate_temperature = res.dialysate_temperature ? res.dialysate_temperature : ""
                    this.form_hemodialys.vascular_jugularis = res.vascular_jugularis ? res.vascular_jugularis : ""
                    this.form_hemodialys.heparin_type_id = res.heparin_type_id ? res.heparin_type_id : ""
                    this.form_hemodialys.without_heparin = res.without_heparin ? res.without_heparin : ""
                    this.form_hemodialys.profiling_type_id = res.profiling_type_id ? res.profiling_type_id : 0
                    this.form_hemodialys.ultrafiltration = res.ultrafiltration ? res.ultrafiltration : ""
                    this.form_hemodialys.natrium = res.natrium ? res.natrium : ""
                    this.form_hemodialys.bicarbonate = res.bicarbonate ? res.bicarbonate : ""
                    this.form_hemodialys.therapy = res.therapy ? res.therapy : ""
                    this.form_hemodialys.medicine = res.medicine ? res.medicine : ""
                    this.form_hemodialys.injection = res.injection ? res.injection : ""
                })
            }
        },
    },
    created () {
        if (this.$route.params.id) {
            this.reg_des_id = this.$route.params.id
            this.getData(this.$route.params.id)
            // this.$store.dispatch('nurse/getByIdTransaction', this.$route.params.id)
        }
    },
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>
<style scoped>

.label-head::before {
    content: '';
    border-left: 6px solid #648DE1;
}

.c-img {
    aspect-ratio: 1/1;
    object-fit: cover;
    max-width: 150px;
    max-height: 150px;
    border: 10px solid white;
}

</style>