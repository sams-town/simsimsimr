<template>
    <div>
        <b-modal v-model="active.modal.main" size="lg" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI FORM TRANSFER INTERNAL
                    <span class="text-primary">{{ regData.alias_name }}. {{ regData.name }} | {{ regData.norm }} | {{localDate(regData.birth_date)}} ({{ parsedAge(regData.birth_date) }})</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>

            <section>
                <ModernLabel label="PROSES TRANSFER" />
                <div class="row mt-8">
                    <div class="col-lg-3">
                        <label class="fs-13 mb-0">Waktu berangkat</label>
                        <b-form-input v-model="form_inter.time_out" type="datetime-local" size="sm" />
                    </div>
                    <div class="col-lg-3 mt-8 mt-lg-0">
                        <label class="fs-13 mb-0">Mobilisasi</label>                        
                        <v-select v-model="form_inter.mobilization" :options="OptionMobilization" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                    </div>  
                    <div class="col-lg-3 mt-8 mt-lg-0">
                        <label class="fs-13 mb-0">Dokter penanggung jawab transfer</label>
                        <v-select v-model="form_inter.employee_doctor_id" :options="all_doctor" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                    </div>
                    <div class="col-lg-3 mt-8 mt-lg-0">
                        <label class="fs-13 mb-0">Perawat penanggung jawab transfer</label>
                        <v-select v-model="form_inter.employee_nurse_id" :options="all_nurse" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                    </div>
                </div>
            </section>

            <section class="mt-20">
                <ModernLabel label="DOKTER PENANGGUNG JAWAB PASIEN" />
                <div class="mt-8">
                    <b-table-simple small fixed class="text-center">
                        <b-tr class="fs-13 ">
                            <b-th class="align-middle" style="width: 80px;">
                                <b-button @click="addDoctor" variant="primary" size="sm">
                                    <feather-icon icon="PlusIcon" />
                                </b-button>
                            </b-th>
                            <b-th class="align-middle">Dokter</b-th>
                            <b-th class="align-middle">Peran</b-th>
                        </b-tr>
                        <b-tr v-for="(item, index) in temp_change.doctors" class="fs-12">
                            <b-td>
                                <b-button @click="removeDoctor(index)" variant="outline-danger-500" size="sm">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </b-td>
                            <b-td>
                                <v-select v-model="item.doctor_in_charge_id" @input="checkDuplicateDoctor(item.doctor_in_charge_id, index)" :options="all_doctor" :reduce="doctor => doctor.value" class="select-size-sm" label="title" placeholder="pilih dokter" />
                            </b-td>
                            <b-td>Dokter {{ index+1 }} <span v-if="index == 0">(DPJP)</span>
                            </b-td>
                        </b-tr>
                    </b-table-simple>
                </div>
            </section>

            <!-- <section v-if="regData?.health_facility_id != hfi_inpatient" class="mt-20"> -->
            <section class="mt-20">
                <ModernLabel label="PENJAMIN" />
                <div class="mt-8">
                    <b-table-simple fixed small bordered class="text-center">
                        <b-thead head-variant="light" class="text-bold-14">
                            <b-th></b-th>
                            <b-th>Jenis Penjamin</b-th>
                            <b-th>Nama Penjamin</b-th>
                            <b-th>No Anggota / NIP</b-th>
                            <b-th>Kelas Layanan</b-th>
                            <b-th>Aksi</b-th>
                        </b-thead>
                        <b-tbody class="text-regular-12">
                            <b-tr v-for="(item,index) in form_inter.insurances" :key="index">
                                <b-td>
                                    <template v-if="item.is_primary === 0">
                                        <b-button variant="warning-600" size="sm" @click="keyDpjpInsurance(index)">
                                            <feather-icon icon="KeyIcon" />
                                        </b-button>
                                    </template>
                                    <template v-if="item.is_primary === 1">
                                        <b-button variant="info-600" size="sm">UTAMA</b-button>
                                    </template>
                                </b-td>
                                <b-td>{{ item.insurance_name }}</b-td>
                                <b-td>{{ item.company_name }}</b-td>
                                <b-td>{{ item.number_insurance }}</b-td>
                                <b-td>{{ item.service_class_name }}</b-td>
                                <b-td>
                                    <b-button variant="danger-600" size="sm" @click="removeInsurance(index)">
                                        <feather-icon icon="TrashIcon" />
                                    </b-button>  
                                </b-td>
                            </b-tr>
                        </b-tbody>
                    </b-table-simple>
        
                    <div class="">
                        <div class="row mt-2 mt-lg-0">
                            <div class="col-lg-3">
                                <label class="fs-13 mb-0">Jenis Penjamin</label>
                                <v-select v-model="temp_change.insurance_id" :options="insurance_list" :reduce="item => item" label="title" placeholder="pilih" class="select-size-sm"/>
                            </div>
                            <template v-if="temp_change.insurance_id.value == 2 || temp_change.insurance_id.value == 3">
                                <div class="col-lg-3 mt-8 mt-lg-0">
                                    <label class="fs-13 mb-0">Nama Penjamin </label>
                                    <v-select v-model="temp_change.company_id" :options="company_list" :reduce="item => item" label="title" placeholder="pilih" class="select-size-sm"/>
                                </div>
                                <div class="col-lg-3 mt-8 mt-lg-0">
                                    <label class="fs-13 mb-0">{{ temp_change.insurance_id.value == 2 ? 'Nomor Anggota' : 'NIP' }}</label>
                                    <template v-if="temp_change.insurance_id.value == 2">
                                        <b-input-group size="sm">
                                            <b-form-input v-model="temp_change.number_insurance" @keyup.enter="getByNoka()" />
                                            <b-input-group-append>
                                                <b-button variant="primary-500" @click="getByNoka()"><feather-icon icon="SearchIcon" /></b-button>
                                            </b-input-group-append>
                                        </b-input-group>
                                    </template>
                                    <template v-if="temp_change.insurance_id.value == 3">
                                        <b-form-input v-model="temp_change.number_insurance" size="sm"/>
                                    </template>
                                </div>
                            </template>
                            <div class="col-lg-3 mt-8 mt-lg-0">
                                <label class="fs-13 mb-0">Hak Kelas </label>
                                <v-select v-model="temp_change.service_class_id" :options="service_class_list" :reduce="item => item" label="title" placeholder="-- Hak Kelas --" class="select-size-sm" />
                            </div>
                        </div>
                        
                        <b-button @click.prevent="addInsurance" variant="primary" size="sm" class="mt-12">TAMBAH PENJAMIN</b-button>
                    </div>
                </div>
            </section>

            <section class="mt-20">
                <ModernLabel label="LOKASI BANGSAL TUJUAN TRANSFER PASIEN" />
                <div class="px-16 py-8 bg-warning-100 fs-13 mt-8"><i>{{ pdCopywriting }}</i></div>
                <div class="row">
                    <div class="col-lg-3">
                        <label class="fs-13 mb-0">Bangsal tujuan transfer</label>
                        <v-select @input="findBed" v-model="form_inter.type_room_id" :options="TypeRoomOptions" :reduce="item => item.value" label="title" placeholder="-- Bangsal --" class="select-size-sm"></v-select>
                    </div>
                </div>

                <template v-if="bed_obtained">
                    <div class="px-16 py-8 fs-13 mt-20" :class="bed_obtained.status == 200 ? 'bg-success-200' : 'bg-danger-100'">
                        <div class="d-flex align-items-center">
                            <div>
                                <feather-icon :icon="bed_obtained.status == 200 ? 'CheckCircleIcon' : 'XCircleIcon'" size="20" />
                            </div>
                            <div class="pl-16 w-100">
                                <div class="font-weight-bold">
                                    <i>{{ bed_obtained?.message }}</i>
                                </div>
                                <div v-if="bed_obtained.status == 200">
                                    <i>{{ bed_obtained?.data?.room_name }}, bed no.{{ bed_obtained?.data?.bed_name }}, {{ bed_obtained?.data?.room_service_class }}</i>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </section>

            <!-- <section class="mt-20">
                <ModernLabel label="LOKASI BANGSAL TUJUAN TRANSFER PASIEN" />
                <div class="px-16 py-8 bg-warning-100 fs-13 mt-8"><i>{{ pdCopywriting }}</i></div>
                <div class="row">
                    <div class="col-lg-3">
                        <label class="fs-13 mb-0">Bangsal tujuan transfer</label>
                        <v-select v-model="form_inter.type_room_id" :options="TypeRoomOptions" :reduce="item => item.value" label="title" placeholder="-- Bangsal --" class="select-size-sm"></v-select>
                    </div>
                    <div class="col-lg-3 mt-8 mt-lg-0">
                        <label class="fs-13 mb-0">Ruangan</label>
                        <v-select v-model="form_inter.room_id" :options="OptionsRoom" :reduce="item => item.value"
                        label="title" placeholder="-- Ruangan --" class="select-size-sm" />
                    </div>

                    <div class="col-lg-3 mt-8 mt-lg-0">
                        <label class="fs-13 mb-0">Bed</label>
                        <v-select v-model="form_inter.bed_id" :options="OptionsBed" :reduce="item => item.value"
                        label="title" placeholder="-- Bed --" class="select-size-sm" />
                    </div>
                </div>
            </section> -->

            <section v-if="regData?.health_facility_id != hfi_inpatient" class="mt-20">
                <ModernLabel label="CHECKOUT" />
                <div class="row mt-8">
                    <div class="col-lg-3">
                        <label class="fs-13 mb-0">Kriteria Pasien</label>
                        <v-select v-model="form_inter.type_emergency_id" aria-readonly="true" :options="OptionsTypeEmergency" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="pilih" />
                    </div>
                    <div class="col-lg-3 mt-8 mt-lg-0">
                        <label class="fs-13 mb-0">Jenis Kunjungan</label>
                        <v-select v-model="form_inter.type_visit_id" aria-readonly="true" :options="OptionsTypeVisit" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="pilih" />
                    </div>
                </div>
            </section>

            <section v-if="regData?.health_facility_id != hfi_inpatient" class="mt-20">
                <ModernLabel label="SPRI" />
                <div class="row mt-8">
                    <div class="col-lg-3">
                        <label class="fs-13 mb-0">Indikasi Rawat Inap</label>
                        <v-select v-model="form_inter.indication" :options="OptionsIndication" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="pilih indikasi rawat inap" />
                    </div>
                    <div class="col-lg-3 mt-8 mt-lg-0">
                        <label class="fs-13 mb-0">Keadaan Umum Pasien</label>
                        <v-select v-model="form_inter.condition" :options="OptionsCondition" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="pilih keadaan umum pasien" />
                    </div>
                    <div class="col-12 mt-8">
                        <label class="fs-13 mb-0">Rencana Tindakan</label>
                        <textarea v-model.lazy="form_inter.plan" rows="4" size="sm" autocomplete="off" class="form-control form-control-sm"></textarea>
                    </div>
                </div>
            </section>

            <section class="mt-20">
                <ModernLabel label="KEADAAN TERAKHIR PASIEN SEBELUM KELUAR DARI UNIT PERTAMA" />
                <template v-if="updated_ttv">
                    <div class="px-16 py-8 bg-warning-100 fs-13 mt-8"><i>Monitoring TTV terakhir pada {{ updated_ttv }}</i></div>
                </template>
                <div class="row mt-8">
                    <div class="col-lg-3">
                        <label class="fs-13 mb-0">Systolic</label>
                        <b-input-group size="sm" append="mmHg">
                            <b-form-input v-model="form_inter.systolic" @input="handleEwsScore" type="number" autocomplete="off" />
                        </b-input-group>
                    </div>
                    <div class="col-lg-3 mt-8 mt-lg-0">
                        <label class="fs-13 mb-0">Diastolic</label>
                        <b-input-group size="sm" append="mmHg">
                            <b-form-input v-model="form_inter.diastolic" type="number" autocomplete="off" />
                        </b-input-group>
                    </div>
                    <div class="col-lg-3 mt-8 mt-lg-0">
                        <label class="fs-13 mb-0">Frekuensi Nadi</label>
                        <b-input-group size="sm" append="x/mnt">
                            <b-form-input v-model="form_inter.pulse_frequency" @input="handleEwsScore" type="number" autocomplete="off" />
                        </b-input-group>
                    </div>
                    <div class="col-lg-3 mt-8 mt-lg-0">
                        <label class="fs-13 mb-0">Frekuensi Nafas</label>
                        <b-input-group size="sm" append="x/mnt">
                            <b-form-input v-model="form_inter.breath_frequency" @input="handleEwsScore" type="number" autocomplete="off" />
                        </b-input-group>
                    </div>
                    <div class="col-lg-3 mt-8">
                        <label class="fs-13 mb-0">Saturasi Oksigen</label>
                        <b-input-group size="sm" append="%">
                            <b-form-input v-model="form_inter.oximetry_frequency" @input="handleEwsScore" type="number" autocomplete="off" />
                        </b-input-group>
                    </div>
                    <div class="col-lg-3 mt-8">
                        <label class="fs-13 mb-0">Suhu</label>
                        <b-input-group size="sm" append="ᵒC">
                            <b-form-input v-model="form_inter.temperature" @input="handleEwsScore" type="number" autocomplete="off" />
                        </b-input-group>
                    </div>
                    <div class="col-lg-3 mt-8">
                        <label class="fs-13 mb-0">Oksigenasi</label>
                        <v-select v-model="form_inter.oxygen_type_id" @input="handleEwsScore" :options="OptionsOxygen" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                    </div>
                    <div class="col-lg-3 mt-8">
                        <label class="fs-13 mb-0">Kesadaran</label>
                        <v-select v-model="form_inter.aware" @input="handleEwsScore" :options="OptionsAware" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                    </div>
                    <div class="col-lg-3 mt-8">
                        <label class="fs-13 mb-0">GCS Mata</label>
                        <v-select v-model="form_inter.eye_score" :options="OptionGcsEye" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                    </div>
                    <div class="col-lg-3 mt-8">
                        <label class="fs-13 mb-0">GCS verbal</label>
                        <v-select v-model="form_inter.verbal_score" :options="OptionGcsVerbal" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                    </div>
                    <div class="col-lg-3 mt-8">
                        <label class="fs-13 mb-0">GCS Motorik</label>
                        <v-select v-model="form_inter.motor_score" :options="OptionGcsMotoric" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                    </div>
                    <template v-if="isPews">
                        <div class="col-lg-3 mt-8">
                            <label class="fs-13 mb-0">Warna Kulit</label>
                            <v-select v-model="form_inter.skin_color" @input="handleEwsScore" :options="optSkinColor" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                        </div>
                        <div class="col-lg-3 mt-8">
                            <label class="fs-13 mb-0">Perilaku</label>
                            <v-select v-model="form_inter.behavior" @input="handleEwsScore" :options="optBehavior" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                        </div>
                    </template> 
                    <div class="col-lg-3 mt-8">
                        <label class="fs-13 mb-0">Berat Badan</label>
                        <b-input-group size="sm" append="kg">
                            <b-form-input v-model="form_inter.weight" type="number" autocomplete="off" />
                        </b-input-group>
                    </div>
                    <div class="col-lg-3 mt-8">
                        <label class="fs-13 mb-0">Tinggi Badan</label>
                        <b-input-group size="sm" append="cm">
                            <b-form-input v-model="form_inter.height" type="number" autocomplete="off" />
                        </b-input-group>
                    </div>
                    <div class="col-lg-3 mt-8">
                        <label class="fs-13 mb-0">Skor EWS</label>
                        <b-form-input v-model="form_inter.ews_downe_score" disabled type="number" autocomplete="off" size="sm"/>
                    </div>
                </div>
            </section>
            <!-- <b-button @click="calculatePews()">cobain pews</b-button> -->
            <section class="mt-20">
                <ModernLabel :label="`Skala Nyeri & Skala Jatuh - ${getFallName()}`" class="mt-16"/>
                <template v-if="updated_ns">
                    <div class="px-16 py-8 bg-warning-100 fs-13 mt-8"><i>Skala Nyeri & Skala Jatuh terakhir pada {{ updated_ns }}</i></div>
                </template>

                <div class="mt-8">
                    <label class="font-weight-bold fs-13">SKALA NYERI: </label>
                    <b-form-radio-group v-model="form_inter.pain_score" size="sm">
                        <div class="row">
                            <div class="col text-center">
                                <b-form-radio id="val0" value="0" class="mr-0"></b-form-radio>
                                <label for="val0" class="d-block" style="line-height: 100%">0</label>
                                <div class="mt-1">
                                    <img src="@/@core/assets/images/nurse/wong_baker/0.png" width="50px">
                                    <div class="fs-13 mt-10">Tidak nyeri</div>
                                </div>
                            </div>
                            <div class="col text-center">
                                <b-form-radio value="1" class="mr-0"></b-form-radio>
                                <label for="val1" class="d-block" style="line-height: 100%">1</label>
                            </div>
                            <div class="col text-center">
                                <b-form-radio id="val2" value="2" class="mr-0"></b-form-radio>
                                <label for="val2" class="d-block" style="line-height: 100%">2</label>
                                <div class="mt-1">
                                    <img src="@/@core/assets/images/nurse/wong_baker/2.png" width="50px">
                                    <div class="fs-13 mt-10">Nyeri ringan</div>
                                </div>
                            </div>
                            <div class="col text-center">
                                <b-form-radio id="val3" value="3" class="mr-0"></b-form-radio>
                                <label for="val3" class="d-block" style="line-height: 100%">3</label>
                            </div>
                            <div class="col text-center">
                                <b-form-radio id="val4" value="4" class="mr-0"></b-form-radio>
                                <label for="val4" class="d-block" style="line-height: 100%">4</label>
                                <div class="mt-1">
                                    <img src="@/@core/assets/images/nurse/wong_baker/4.png" width="50px">
                                    <div class="fs-13 mt-10">Nyeri yang mengganggu</div>
                                </div>
                            </div>
                            <div class="col text-center">
                                <b-form-radio id="val5" value="5" class="mr-0"></b-form-radio>
                                <label for="val5" class="d-block" style="line-height: 100%">5</label>
                            </div>
                            <div class="col text-center">
                                <b-form-radio id="val6" value="6" class="mr-0"></b-form-radio>
                                <label for="val6" class="d-block" style="line-height: 100%">6</label>
                                <div class="mt-1">
                                    <img src="@/@core/assets/images/nurse/wong_baker/6.png" width="50px">
                                    <div class="fs-13 mt-10">Nyeri yang menyusahkan</div>
                                </div>
                            </div>
                            <div class="col text-center">
                                <b-form-radio id="val7" value="7" class="mr-0"></b-form-radio>
                                <label for="val7" class="d-block" style="line-height: 100%">7</label>
                            </div>
                            <div class="col text-center">
                                <b-form-radio id="val8" value="8" class="mr-0"></b-form-radio>
                                <label for="val8" class="d-block" style="line-height: 100%">8</label>
                                <div class="mt-1">
                                    <img src="@/@core/assets/images/nurse/wong_baker/8.png" width="50px">
                                    <div class="fs-13 mt-10">Nyeri hebat</div>
                                </div>
                            </div>
                            <div class="col text-center">
                                <b-form-radio id="val9" value="9" class="mr-0"></b-form-radio>
                                <label for="val9" class="d-block" style="line-height: 100%">9</label>
                            </div>
                            <div class="col text-center">
                                <b-form-radio id="val10" value="10" class="mr-0"></b-form-radio>
                                <label for="val10" class="d-block" style="line-height: 100%">10</label>
                                <div class="mt-1">
                                    <img src="@/@core/assets/images/nurse/wong_baker/10.png" width="50px">
                                    <div class="fs-13 mt-10">Nyeri sangat hebat</div>
                                </div>
                            </div>
                        </div>
                    </b-form-radio-group>
                </div>

                <template v-if="live_sub.neonatus || live_sub.child">
                    <label class="font-weight-bold fs-13 mt-10">SKALA JATUH HUMPTY DUMPTY: </label>
                    <b-table-simple small bordered>
                        <b-tr>
                            <b-td class="col-lg-4 px-12">
                                <label class="fs-13">Usia</label>
                                <b-form-radio-group
                                    @input="calculateHumpty" stacked size="sm" class="fs-13"
                                    v-model="form_inter.humpty_dumpty.age"
                                    :options="OptionAge"
                                ></b-form-radio-group>
                            </b-td>
                            <b-td class="col-lg-4 px-12">
                                <label class="fs-13">Jenis kelamin</label>
                                <b-form-radio-group
                                    @input="calculateHumpty" stacked size="sm" class="fs-13"
                                    v-model="form_inter.humpty_dumpty.gender"
                                    :options="OptionGender"
                                ></b-form-radio-group>
                            </b-td>
                            <b-td class="col-lg-4 px-12">
                                <label class="fs-13">Gangguan kognitif</label>
                                <b-form-radio-group
                                    @input="calculateHumpty" stacked size="sm" class="fs-13"
                                    v-model="form_inter.humpty_dumpty.cognitive_impairment"
                                    :options="OptionCognitiveImpairment"
                                ></b-form-radio-group>
                            </b-td>
                        </b-tr>
                        <b-tr>
                            <b-td class="col-lg-4 px-12">
                                <label class="fs-13">Diagnosis</label>
                                <b-form-radio-group
                                    @input="calculateHumpty" stacked size="sm" class="fs-13"
                                    v-model="form_inter.humpty_dumpty.diagnosis"
                                    :options="OptionDiagnosis"
                                ></b-form-radio-group>
                            </b-td>
                            <b-td class="col-lg-4 px-12">
                                <label class="fs-13">Faktor lingkungan</label>
                                <b-form-radio-group
                                    @input="calculateHumpty" stacked size="sm" class="fs-13"
                                    v-model="form_inter.humpty_dumpty.environmental_factors"
                                    :options="OptionEnvironmentalFactor"
                                ></b-form-radio-group>
                            </b-td>
                            <b-td class="col-lg-4 px-12">
                                <label class="fs-13">Pembedahan sedasi / anestesi</label>
                                <b-form-radio-group
                                    @input="calculateHumpty" stacked size="sm" class="fs-13"
                                    v-model="form_inter.humpty_dumpty.surgery_sedation_anesthesia"
                                    :options="OptionSedationAnesthesia"
                                ></b-form-radio-group>
                            </b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="3" class="px-12">
                                <label class="fs-13">Penggunaan medika mentosa</label>
                                <b-form-radio-group
                                    @input="calculateHumpty" stacked size="sm" class="fs-13"
                                    v-model="form_inter.humpty_dumpty.use_of_medication"
                                    :options="OptionMedicationUse"
                                ></b-form-radio-group>
                            </b-td>
                        </b-tr>
                    </b-table-simple>
                    <div>Skor Skala Jatuh:  {{ form_inter.humpty_dumpty.total_score }} {{ getHumptyName() }}</div>
                </template>

                <template v-if="live_sub.adult">
                    <label class="font-weight-bold fs-13 mt-10">SKALA JATUH MORSE: </label>
                    <b-table-simple small bordered>
                        <b-tr>
                            <b-td class="col-lg-4 px-12">
                                <label class="fs-13">Mempunyai riwayat jatuh, baru atau 3 bulan terakhir</label>
                                <b-form-radio-group
                                    @input="calculateMorse" stacked size="sm" class="fs-13"
                                    v-model="form_inter.morse.history_of_falls"
                                    :options="OptionFallHistory"
                                ></b-form-radio-group>
                            </b-td>
                            <b-td class="col-lg-4 px-12">
                                <label class="fs-13">Diagnosis sekunder > 1</label>
                                <b-form-radio-group
                                    @input="calculateMorse" stacked size="sm" class="fs-13"
                                    v-model="form_inter.morse.secondary_diagnosis"
                                    :options="OptionSecondaryDiagnosis"
                                ></b-form-radio-group>
                            </b-td>
                            <b-td class="col-lg-4 px-12">
                                <label class="fs-13">Ambulasi berjalan</label>
                                <b-form-radio-group
                                    @input="calculateMorse" stacked size="sm" class="fs-13"
                                    v-model="form_inter.morse.walking_ambulation"
                                    :options="OptionWalkingAmbulation"
                                ></b-form-radio-group>
                            </b-td>
                        </b-tr>
                        <b-tr>
                            <b-td class="col-lg-4 px-12">
                                <label class="fs-13">Terpasang IV line / pemberian anti koagulan (Heparin) / obat lain yang digunakan mempunya side effect jatuh</label>
                                <b-form-radio-group
                                    @input="calculateMorse" stacked size="sm" class="fs-13"
                                    v-model="form_inter.morse.side_effects_of_falling"
                                    :options="OptionFallingSideEffects"
                                ></b-form-radio-group>
                            </b-td>
                            <b-td class="col-lg-4 px-12">
                                <label class="fs-13">Cara berjalan / berpindah</label>
                                <b-form-radio-group
                                    @input="calculateMorse" stacked size="sm" class="fs-13"
                                    v-model="form_inter.morse.way_of_walking"
                                    :options="OptionWalkingWay"
                                ></b-form-radio-group>
                            </b-td>
                            <b-td class="col-lg-4 px-12">
                                <label class="fs-13">Status mental</label>
                                <b-form-radio-group
                                    @input="calculateMorse" stacked size="sm" class="fs-13"
                                    v-model="form_inter.morse.mental_status"
                                    :options="OptionMentalStatus"
                                ></b-form-radio-group>
                            </b-td>
                        </b-tr>
                    </b-table-simple>
                    <div>Skor Skala Jatuh:  {{ form_inter.morse.total_score }} {{ getMorseName() }}</div>
                </template>
            </section>

            <section class="mt-20">
                <ModernLabel label="RIWAYAT ASESMEN TERAKHIR" />
                <template v-if="last_soap">
                    <LastDoctorAssessment :result="last_soap" :regData="regData" />
                </template>
                <div v-else class="fs-13 mt-8">
                    <i>Tidak ada asesmen dokter yang ditambahkan pada registrasi ini</i>
                </div>
            </section>

            <template #modal-footer>
                <div class="d-flex justify-content-end w-100 m-0">
                    <b-button @click="validationTransfer" :disabled="active.disabled.main" size="sm" variant="primary">
                        <feather-icon icon="SaveIcon" class="mr-1" />
                        <span>PINDAHKAN PASIEN</span>
                    </b-button>
                </div>
            </template>
        </b-modal>

        <b-modal ref="confirmTransportModal" size="sm" hide-header-close>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase text-center w-100">KONFIRMASI</div>
                <feather-icon icon="XIcon" @click="closeModal()" size="18" class="pointer" />
            </template>
            
            <div class="text-center fs-13">
                <template v-if="nonTransportableReasons.length > 0">
                    <div>Pasien menunjukkan kriteria tidak transportable:</div>
                    <div class="d-flex justify-content-center">
                        <div class="col-lg-8">
                            <ul class="text-left pl-24">
                                <li v-for="(reason, index) in nonTransportableReasons" :key="index">{{ reason }}</li>
                            </ul>
                        </div>
                    </div>
                </template>
                <div>Apakah Anda yakin tetap melakukan transfer pasien?</div>
            </div>

            <template #modal-footer>
                <div class="d-flex justify-content-center w-100 m-0">
                    <b-button @click="proceedTransfer()" size="sm" variant="outline-danger-500">YA, TRANSFER</b-button>
                    <b-button @click="cancelTransfer()" size="sm" variant="w3green" class="ml-1 px-32">BATAL</b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>

<script>
import { mapState } from 'vuex'
import moment from "moment";
import dateMixin from '@/mixins/dateMixin';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import LastDoctorAssessment from './LastDoctorAssessment.vue'
import io from 'socket.io-client';

export default {
    mixins: [dateMixin],
    components: { LastDoctorAssessment},
    data() {
        return {
            socket: io(process.env.VUE_APP_SOCKET_IO_NOTIF, { path: '/socket.io', transports: ['websocket', 'polling', 'flashsocket'], withCredentials: true }),
            hfi_inpatient: '40f06534-07ec-4221-ad9d-dea08196079f',
            hfi_outpatient: '3cb626f5-9148-4fb1-8663-3d7f028b01a6',
            hfi_emergency: '444b3134-5b75-4b67-af72-a558d46716d7',

            active: {
                modal: {
                    main: false,
                },
                disabled: {
                    main: false,
                }
            },
            
            isPews: false,
            live_sub: {
                neonatus: false,
                child: false,
                adult: false,
            },

            pdCopywriting: '',
            regData: [],
            all_doctor: [],
            all_nurse: [],
            updated_ttv: '',
            updated_ns: '',
            last_soap: {},
            dataInpatient: {},

            form_inter: {
                registration_destination_past_id: '',
                nurse_id: '',
                systolic: '',
                diastolic: '',
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
                ews_downe_score: '',
                pain_score: '',
                morse: {
                    history_of_falls: '',
                    secondary_diagnosis: '',
                    walking_ambulation: '',
                    side_effects_of_falling: '',
                    way_of_walking: '',
                    mental_status: '',
                    total_score: '',
                },
                humpty_dumpty: {
                    age: '',
                    gender: '',
                    diagnosis: '',
                    cognitive_impairment: '',
                    environmental_factors: '',
                    surgery_sedation_anesthesia: '',
                    use_of_medication: '',
                    total_score: '',
                },
                doctor_in_charge_id: '',
                doctor_in_charge_id_2: '',
                doctor_in_charge_id_3: '',
                doctor_in_charge_id_4: '',
                doctor_in_charge_id_5: '',
                type_room_id: '',
                room_id: '',
                bed_id: '',
                employee_nurse_id: '',
                employee_doctor_id: '',
                mobilization: '',
                time_out: moment(new Date()).format('YYYY-MM-DD HH:mm'),
                
                // internal transfer
                registration_id: '',
                doctor_in_charge_sec_id: '', // dpjp-sebelum
                insurances: [],
                condition: '',
                indication: '',
                plan: '',
                type_emergency_id: '',
                type_visit_id: 2,
                is_surgery: '',
                is_internal: '',
            },

            temp_change: {
                doctors: [],

                insurance_id: '',
                company_id: '',
                number_insurance: '',
                service_class_id: '',
            },

            nonTransportableReasons: [],

            form_bed: {
                type_room_id: '',
                service_class_id: '',
                doctor_in_charge_id: '',
                sex: '',
                icd10_code: '',
            },
            bed_obtained: null,
        }
    },

    watch: {
        // 'form_inter.type_room_id'(value) {
        //     this.form_inter.bed_id = ''
        //     this.form_inter.room_id = ''

        //     if (value) {
        //         return this.$store.dispatch('room/getAvailableRoom', value)
        //     } else {
        //         this.$store.dispatch('room/getAvailableRoom', '')
        //         this.$store.dispatch('room/getParamBed', '')
        //     }
        // },

        // 'form_inter.room_id'(value) {
        //     this.form_inter.bed_id = ''

        //     if (value) {
        //         return this.$store.dispatch('room/getParamBed', value)
        //     } else {
        //         this.$store.dispatch('room/getParamBed', '')
        //     }
        // },

        'temp_change.insurance_id'(obj) {
            if (obj?.value == 1) {
                this.temp_change.number_insurance = ''
                this.temp_change.company_id = ''
            }
        },
    },

    computed: {
        ...mapState({ 
            OptionsOxygen: state => state.soap.vitals.OptionsOxygen,
            OptionsAware: state => state.soap.vitals.OptionsAware,
            OptionGcsEye: state => state.soap.vitals.OptionGcsEye,
            OptionGcsVerbal: state => state.soap.vitals.OptionGcsVerbal,
            OptionGcsMotoric: state => state.soap.vitals.OptionGcsMotoric,
            optSkinColor: state => state.frontoptions.general.vital_sign.skin_color,
            optBehavior: state => state.frontoptions.general.vital_sign.behavior,

            TypeRoomOptions: state => state.helper.options.type_room,
            OptionsRoom: state => state.room.options.available_room,
            OptionsBed: state => state.room.options.bed,
            OptionsTypeEmergency: state => state.helper.options.typeEmergency,
            OptionMobilization: state => state.frontoptions.general.transfer.mobilization,
            OptionsTypeVisit: state => state.frontoptions.general.checkout.type_visit,
            OptionsIndication: state => state.frontoptions.general.spri.indication,            
            OptionsCondition: state => state.frontoptions.general.spri.condition, 
            
            OptionFallHistory: state => state.frontoptions.nurse.assessment.functional.fall_history,
            OptionSecondaryDiagnosis: state => state.frontoptions.nurse.assessment.functional.secondary_diagnosis,
            OptionWalkingAmbulation: state => state.frontoptions.nurse.assessment.functional.walking_ambulation,
            OptionFallingSideEffects: state => state.frontoptions.nurse.assessment.functional.falling_side_effects,
            OptionWalkingWay: state => state.frontoptions.nurse.assessment.functional.walking_way,
            OptionMentalStatus: state => state.frontoptions.nurse.assessment.functional.mental_status,

            OptionAge: state => state.frontoptions.nurse.assessment.functional.age,
            OptionGender: state => state.frontoptions.nurse.assessment.functional.gender,
            OptionDiagnosis: state => state.frontoptions.nurse.assessment.functional.diagnosis,
            OptionCognitiveImpairment: state => state.frontoptions.nurse.assessment.functional.cognitive_impairement,
            OptionEnvironmentalFactor: state => state.frontoptions.nurse.assessment.functional.environtmental_factor,
            OptionSedationAnesthesia: state => state.frontoptions.nurse.assessment.functional.sedation_anesthesia,
            OptionMedicationUse: state => state.frontoptions.nurse.assessment.functional.medication_use,

            insurance_list: state => state.helper.options.insurance,
            company_list: state => state.helper.options.third_party_company,
            service_class_list: state => state.helper.options.service_class,
        }),

        handleEwsScore() {
            return this.isPews ? this.calculatePews : this.calculateEws
        }
    },

    methods: {    
        async findBed() {
            await this.resetFormBed()
            this.form_bed.sex = this.regData.sex
            this.form_bed.doctor_in_charge_id = this.temp_change.doctors?.length > 0 ? this.temp_change.doctors[0]?.doctor_in_charge_id : ''
            this.form_bed.type_room_id = this.form_inter.type_room_id
            
            // this.form_bed.icd10_code = this.last_soap && this.last_soap.icd10 ? this.last_soap?.icd10[0]?.code : ''
            let icd10_from_last_soap = this.last_soap?.icd10 || []
            let temp10 = []
            if (icd10_from_last_soap?.length > 0) {
                for (let i = 0; i < icd10_from_last_soap.length; i++) {
                    const el = icd10_from_last_soap[i];
                    temp10.push(el.code)
                }
                this.form_bed.icd10_code = temp10.join("|");
            }

            let temp_ins = null
            const isnrnc = this.form_inter.insurances
            if (isnrnc?.length > 0) {
                temp_ins = isnrnc.find(item => item.is_primary == 1)
                this.form_bed.service_class_id = temp_ins.service_class_id                 
            }

            if (this.form_bed.type_room_id && this.form_bed.service_class_id && this.form_bed.doctor_in_charge_id) {
                try {                
                    await this.$store.dispatch('bed/getAutoBed', this.form_bed)
                    .then(
                        response => {
                            // console.log(response);
                            this.bed_obtained = response
                            this.form_inter.bed_id = this.bed_obtained.data.bed_id
                            this.form_inter.is_surgery = this.bed_obtained.data.is_surgery
                            this.form_inter.is_internal = this.bed_obtained.data.is_internal
                        }
                    )
                    .catch(({message, debug, response}) => {
                        // console.log('response', response)                        
                        if (response.status == 404) {
                            this.bed_obtained = response
                            this.form_inter.bed_id = this.bed_obtained.data.bed_id
                            this.form_inter.is_surgery = this.bed_obtained.data.is_surgery
                            this.form_inter.is_internal = this.bed_obtained.data.is_internal
                        } else {
                            this.removeObtained()
                        }
                    })
                } catch (error) {
                    console.error('Error catch while GET bed/getAutoBed at: ', error);
                }
            } else {
                this.removeObtained()        
            }
        },

        removeObtained() {
            this.bed_obtained = null
            this.form_inter.bed_id = ''
            this.form_inter.is_surgery = ''
            this.form_inter.is_internal = ''
        },

        async resetFormBed() {
            this.form_bed.type_room_id = ''
            this.form_bed.service_class_id = ''
            this.form_bed.doctor_in_charge_id = ''
            this.form_bed.sex = ''
            this.form_bed.icd10_code = ''
        },
        
        getFallName() {
            if (this.live_sub.neonatus || this.live_sub.child) return 'Humpty Dumpty'
            if (this.live_sub.adult) return 'Morse'
            return ''
        },

        getDateUpdatedNs(obj) {
            if (this.live_sub.neonatus || this.live_sub.child) return obj?.humpty_dumpty.date_updated
            if (this.live_sub.adult) return obj?.morse.date_updated
            return '----'
        },

        calculatePews() {
            // console.log('di pews');
            let tempPews =  {
                birth_date: this.regData.birth_date || null,
                breathing: this.form_inter.breath_frequency || 0,
                oxygen: this.form_inter.oxygen_type_id || 0,
                oxygen_saturation: this.form_inter.oximetry_frequency || 0,
                pulse: this.form_inter.pulse_frequency || 0,
                skin_color: this.form_inter.skin_color || 0,
                systolic: Number(this.form_inter.systolic) || 0,
                behavior: this.form_inter.behavior || 0,
            }

            try {                
                this.$store.dispatch('ews/postPewsScore', tempPews).then((response) => {
                    this.form_inter.ews_downe_score = response?.data?.score
                }, error => {
                    console.error('Error while POST ews/postPewsScore at: ', error);
                })
            } catch (error) {
                console.error('Error catch while POST ews/postPewsScore at: ', error);
            }          
        },

        calculateEws() {
            // console.log('di dewasa');

            // AWARE
            let aware_var = Number(this.form_inter.aware) || 0
            let aware_score = aware_var != 0 ? 3 : 0 

            // SYSTOLIC
            let systolic_var = Number(this.form_inter.systolic) || 0
            let systolic_score = 0
            if (systolic_var > 210 || systolic_var < 81) {
                systolic_score = 3
            } else if (systolic_var > 80 && systolic_var < 91) {
                systolic_score = 2
            } else if (systolic_var > 90 && systolic_var < 101) {
                systolic_score = 1
            }

            // PULSE
            let pulse_var = Number(this.form_inter.pulse_frequency) || 0
            let pulse_score = 0
            if (pulse_var > 120 || pulse_var < 40) {
                pulse_score = 3
            } else if (pulse_var > 100 && pulse_var < 121) {
                pulse_score = 2
            } else if (pulse_var > 80 && pulse_var < 101) {
                pulse_score = 1
            }

            // BREATH 
            let breath_var = Number(this.form_inter.breath_frequency) || 0
            let breath_score = 0
            if (breath_var >= 25 || breath_var<= 8) {
                breath_score = 3
            } else if (breath_var >= 21 && breath_var <= 24) {
                breath_score = 2
            } else if (breath_var >= 9 && breath_var <= 11) {
                breath_score = 1
            }

            //  TEMPERATURE
            let temperature_var = Number(this.form_inter.temperature) || 0
            let temperature_score = 0
            if (temperature_var < 35) {
                temperature_score = 3
            } else if (temperature_var > 38) {
                temperature_score = 2
            } else if (temperature_var > 37 && temperature_var < 38.1) {
                temperature_score = 1
            }

            // OXYGEN SATURATION
            let saturation_var = Number(this.form_inter.oximetry_frequency) || 0
            let saturation_score = 0
            if (saturation_var <= 91) {
                saturation_score = 3
            } else if (saturation_var >= 92 && saturation_var <= 93) {
                saturation_score = 2
            } else if (saturation_var >= 94 && saturation_var <= 95) {
                saturation_score = 1
            }

            // OXYGEN
            let oxygen_var = Number(this.form_inter.oxygen_type_id) || 2
            let oxygen_score = oxygen_var == 2 ? 0 : 2

            let ews_score = 0
            ews_score = aware_score + systolic_score + pulse_score + breath_score + temperature_score + saturation_score + oxygen_score
            this.form_inter.ews_downe_score = ews_score 
        },

        calculateHumpty() {
            let age = this.form_inter.humpty_dumpty.age || 0 
            let gen = this.form_inter.humpty_dumpty.gender || 0 
            let dia = this.form_inter.humpty_dumpty.diagnosis || 0 
            let cog = this.form_inter.humpty_dumpty.cognitive_impairment || 0 
            let env = this.form_inter.humpty_dumpty.environmental_factors || 0 
            let sur = this.form_inter.humpty_dumpty.surgery_sedation_anesthesia || 0 
            let use = this.form_inter.humpty_dumpty.use_of_medication || 0 
            let temp_score = 0

            temp_score = age + gen + dia + cog + env + sur + use 
            this.form_inter.humpty_dumpty.total_score = temp_score
        },

        getHumptyName() {
            let score = Number(this.form_inter.humpty_dumpty.total_score)
            if (score >= 7 && score <= 11) return '(Risiko Rendah)'
            if (score >= 12) return '(Risiko Tinggi)'
            return ''
        },

        calculateMorse() {
            let his = this.form_inter.morse.history_of_falls || 0 
            let sec = this.form_inter.morse.secondary_diagnosis || 0 
            let wal = this.form_inter.morse.walking_ambulation || 0 
            let sid = this.form_inter.morse.side_effects_of_falling || 0 
            let way = this.form_inter.morse.way_of_walking || 0 
            let men = this.form_inter.morse.mental_status || 0 
            let temp_score = 0

            temp_score = his + sec + wal + sid + way + men
            this.form_inter.morse.total_score = temp_score
        },

        getMorseName() {
            let score = Number(this.form_inter.morse.total_score)
            if (score >= 0 && score <= 24) return '(Tidak berisiko, perawatan yang baik)'
            if (score >= 25 && score <= 50) return '(Risiko rendah, lakukan intervensi jatuh standar)'
            if (score >= 51) return '(Risiko tinggi, lakukan intervensi jatuh tinggi)'
            return ''
        },

        async liveSubNurse(obj) {
            const birthDate = moment(obj.birth_date, 'YYYY-MM-DD');
            const now = moment();
            const years = now.diff(birthDate, 'years');
            const days = now.diff(birthDate, 'days');

            this.live_sub.neonatus = days <= 28
            this.live_sub.child = days > 28 && years <= 17
            this.live_sub.adult = years >= 18
        },

        async parentTrigger(message){
            this.regData = message.regData
            this.pdCopywriting = message.pdCopywriting
            await this.liveSubNurse(message.regData)
            await this.resetForm()
            await this.assignVital()
            await this.assignNurse()
            await this.assignSoap()
            this.assignInsurance(message?.regData?.registration_insurance)

            if (message?.regData?.health_facility_id == this.hfi_outpatient || message?.regData?.health_facility_id == this.hfi_emergency) {
                this.assignSpri(message?.regData?.registration_destination_id)
            }

            this.isPews = this.pewsCriteria(message.regData?.birth_date)
            // this.assignDoctor()
            this.openMainModal()
        },

        assignInsurance(insArry) {
            this.form_inter.insurances = []
            let group = []

            if (insArry?.length > 0) {
                for (let i = 0; i < insArry.length; i++) {
                    const el = insArry[i]
                    let temp = {
                        is_primary: el?.is_primary, 
                        insurance_id: el?.insurance_id, 
                        company_id: el?.company_id || '',
                        number_insurance: el?.number_insurance || '',
                        expired: el?.expired || '',
                        service_class_id: el?.service_class_id || '',
                        insurance_name: el?.insurance_name || '', 
                        company_name: el?.company_name || '',
                        service_class_name: el?.service_class_name || '',
                    }
                    group.push(temp)
                }
            }
            this.form_inter.insurances = group
        },

        async assignSoap() {
            const params = {
                registration_destination_id: this.regData.registration_destination_id
            }

            if (this.regData.health_facility_id === this.hfi_outpatient) {
                params.is_booking = 0
            } else if (this.regData.health_facility_id === this.hfi_inpatient) {
                params.is_initial_inpatient = 1
            }

            try {                
                this.$store.dispatch('soap/getDetail', params).then((response) => {
                    let rows = response
                    let last_asse = rows.length > 0 ? rows[0] : null
                    this.last_soap = last_asse
                    this.form_inter.employee_doctor_id = last_asse?.doctor_in_charge_id || '' 

                    if ([this.hfi_inpatient, this.hfi_emergency].includes(this.regData.health_facility_id)) {
                        const inpatientIds = [
                            this.regData.doctor_in_charge_id,
                            this.regData.doctor_in_charge_id_2,
                            this.regData.doctor_in_charge_id_3,
                            this.regData.doctor_in_charge_id_4,
                            this.regData.doctor_in_charge_id_5
                        ];
                        
                        const emergencyIds = [
                            this.regData.doctor_in_charge_id_2,
                            this.regData.doctor_in_charge_id_3,
                            this.regData.doctor_in_charge_id_4,
                            this.regData.doctor_in_charge_id_5
                        ];

                        this.temp_change.doctors = (this.regData.health_facility_id == this.hfi_inpatient ? inpatientIds : emergencyIds)
                            .filter(Boolean) // buat ngilangin nilai yang falsy
                            .map(doctor_id => ({ doctor_in_charge_id: doctor_id }))
                    }

                    // if (this.regData.health_facility_id == this.hfi_inpatient) {
                    //     this.temp_change.doctors = []

                    //     let temp_arry = [
                    //         { doctor_id: this.regData.doctor_in_charge_id },
                    //         { doctor_id: this.regData.doctor_in_charge_id_2 },
                    //         { doctor_id: this.regData.doctor_in_charge_id_3 },
                    //         { doctor_id: this.regData.doctor_in_charge_id_4 },
                    //         { doctor_id: this.regData.doctor_in_charge_id_5 },
                    //     ]

                    //     for (let index = 0; index < temp_arry.length; index++) {
                    //         const element = temp_arry[index];
                            
                    //         if (element.doctor_id) {
                    //             this.temp_change.doctors.push({
                    //                 doctor_in_charge_id: element.doctor_id
                    //             })
                    //         }
                    //     }
                    // }                    
                }, error => {
                    console.error('Error while GET soap/getDetail at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET soap/getDetail at: ', error);
            }
        },

        async assignNurse() {
            const params = {
                registration_destination_id: this.regData.registration_destination_id
            }

            try {                
                this.$store.dispatch('nurse/getDetail', params).then((response) => {
                    let rows = response
                    let last_nurse = rows.length > 0 ? rows[0] : null

                    if (last_nurse) {
                        this.updated_ns = this.getDateUpdatedNs(last_nurse)
                        this.form_inter.nurse_id = last_nurse.id 
                        this.form_inter.employee_nurse_id = last_nurse != null ? last_nurse.employee_nurse_id : '' 
                        this.form_inter.pain_score = last_nurse?.pain_score

                        const morse = last_nurse?.morse
                        this.form_inter.morse.history_of_falls = morse?.history_of_falls
                        this.form_inter.morse.secondary_diagnosis = morse?.secondary_diagnosis
                        this.form_inter.morse.walking_ambulation = morse?.walking_ambulation
                        this.form_inter.morse.side_effects_of_falling = morse?.side_effects_of_falling
                        this.form_inter.morse.way_of_walking = morse?.way_of_walking
                        this.form_inter.morse.mental_status = morse?.mental_status
                        this.form_inter.morse.total_score = morse?.total_score

                        const humpty = last_nurse?.humpty_dumpty
                        this.form_inter.humpty_dumpty.age = humpty?.age
                        this.form_inter.humpty_dumpty.gender = humpty?.gender
                        this.form_inter.humpty_dumpty.diagnosis = humpty?.diagnosis
                        this.form_inter.humpty_dumpty.cognitive_impairment = humpty?.cognitive_impairment
                        this.form_inter.humpty_dumpty.environmental_factors = humpty?.environmental_factors
                        this.form_inter.humpty_dumpty.surgery_sedation_anesthesia = humpty?.surgery_sedation_anesthesia
                        this.form_inter.humpty_dumpty.use_of_medication = humpty?.use_of_medication
                        this.form_inter.humpty_dumpty.total_score = humpty?.total_score
                    }
                }, error => {
                    console.error('Error while GET nurse/getDetail at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET nurse/getDetail at: ', error);
            }
        },

        async assignVital() {
            const params = {
                registration_destination_id: this.regData.registration_destination_id
            }

            try {                
                this.$store.dispatch('vitalsign/getListVitalSign', params).then((response) => {
                    let rows = response.rows
                    let last_vital = rows.length > 0 ? rows[0] : null
                    
                    if (last_vital) {
                        this.updated_ttv = last_vital?.date_updated
                        this.form_inter.systolic = last_vital?.systolic
                        this.form_inter.diastolic = last_vital?.diastolic
                        this.form_inter.pulse_frequency = last_vital?.pulse
                        this.form_inter.breath_frequency = last_vital?.respiratory_rate
                        this.form_inter.oximetry_frequency = last_vital?.oxygen_saturation
                        this.form_inter.temperature = last_vital?.temperature
                        this.form_inter.oxygen_type_id = last_vital?.oxygen_type_id
                        this.form_inter.aware = last_vital?.aware
                        this.form_inter.eye_score = last_vital?.eye_score
                        this.form_inter.verbal_score = last_vital?.verbal_score
                        this.form_inter.motor_score = last_vital?.motor_score
                        this.form_inter.skin_color = last_vital?.skin_color
                        this.form_inter.behavior = last_vital?.behavior
                        this.form_inter.weight = last_vital?.weight
                        this.form_inter.height = last_vital?.height

                        this.handleEwsScore()
                    }                    
                }, error => {
                    console.error('Error while GET vitalsign/getListVitalSign at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET vitalsign/getListVitalSign at: ', error);
            }
        },

        openMainModal() {
            this.active.modal.main = true
        },

        // assignDoctor() {
        //     this.temp_change.doctors = []

        //     let temp_arry = [
        //         { doctor_id: this.regData.doctor_in_charge_id },
        //         { doctor_id: this.regData.doctor_in_charge_id_2 },
        //         { doctor_id: this.regData.doctor_in_charge_id_3 },
        //         { doctor_id: this.regData.doctor_in_charge_id_4 },
        //         { doctor_id: this.regData.doctor_in_charge_id_5 },
        //     ]

        //     for (let index = 0; index < temp_arry.length; index++) {
        //         const element = temp_arry[index];
                
        //         if (element.doctor_id) {
        //             this.temp_change.doctors.push({
        //                 doctor_in_charge_id: element.doctor_id
        //             })
        //         }
        //     }
        // },

        assignSpri(regDesId) {
            try {
                this.$store.dispatch('soap/getInpatientByRegId', regDesId).then((response)=>{
                    // console.log('response spri: ', response);
                    if (response.length > 0) {
                        this.dataInpatient = response[0]
                        this.form_inter.indication = this.dataInpatient?.indication
                        this.form_inter.condition = this.dataInpatient?.condition
                        this.form_inter.plan = this.dataInpatient?.plan
                        if (this.dataInpatient?.health_facility_id == this.hfi_outpatient) {
                            this.temp_change.doctors = []
                            let temp_arry = [
                                { doctor_id: this.dataInpatient?.doctor_in_charge_id },
                            ]

                            for (let index = 0; index < temp_arry.length; index++) {
                                const element = temp_arry[index];
                                
                                if (element.doctor_id) {
                                    this.temp_change.doctors.push({
                                        doctor_in_charge_id: element.doctor_id
                                    })
                                }
                            }
                        }
                    }
                })
            } catch (error) {
                console.error('Error fetching data:', error);
                this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
            }
        },

        validationTransfer() {
            this.form_inter.registration_destination_past_id = this.regData?.registration_destination_id
            this.form_inter.registration_id = this.regData?.registration_id
            this.form_inter.doctor_in_charge_sec_id = this.regData?.doctor_in_charge_id

            this.form_inter.doctor_in_charge_id = ''
            this.form_inter.doctor_in_charge_id_2 = ''
            this.form_inter.doctor_in_charge_id_3 = ''
            this.form_inter.doctor_in_charge_id_4 = ''
            this.form_inter.doctor_in_charge_id_5 = ''

            if (this.temp_change.doctors?.length > 0) {
                this.temp_change.doctors.forEach((item, index) => {
                    if (index === 0) this.form_inter.doctor_in_charge_id = item.doctor_in_charge_id || this.regData.doctor_in_charge_id
                    if (index === 1) this.form_inter.doctor_in_charge_id_2 = item.doctor_in_charge_id
                    if (index === 2) this.form_inter.doctor_in_charge_id_3 = item.doctor_in_charge_id
                    if (index === 3) this.form_inter.doctor_in_charge_id_4 = item.doctor_in_charge_id
                    if (index === 4) this.form_inter.doctor_in_charge_id_5 = item.doctor_in_charge_id
                })
            } else {
                this.form_inter.doctor_in_charge_id = this.regData.doctor_in_charge_id
            }

            // console.log('this.form_inter', this.form_inter);
            // Check is transportable or not
            this.checkTransportable()
        },

        checkTransportable() {
            this.nonTransportableReasons = [];
            
            // Cek kriteria tidak transportable
            if (this.form_inter.systolic < 70 || this.form_inter.systolic > 185) {
                this.nonTransportableReasons.push(`Sistolik ${this.form_inter.systolic} (Normal: 70-185)`);
            }
            if (this.form_inter.pulse_frequency > 140) {
                this.nonTransportableReasons.push(`Nadi ${this.form_inter.pulse_frequency} (Normal: ≤140)`);
            }
            if (this.form_inter.breath_frequency > 30) {
                this.nonTransportableReasons.push(`Frekuensi Nafas ${this.form_inter.breath_frequency} (Normal: ≤30)`);
            }
            if (this.form_inter.oximetry_frequency < 90) {
                this.nonTransportableReasons.push(`SpO2 ${this.form_inter.oximetry_frequency}% (Normal: ≥90%)`);
            }
            if (this.form_inter.temperature > 40 || this.form_inter.temperature < 36) {
                this.nonTransportableReasons.push(`Suhu ${this.form_inter.temperature}°C (Normal: 36-40)`);
            }

            if (this.nonTransportableReasons.length > 0) {
                this.$refs.confirmTransportModal.show();
            } else {
                this.proceedTransfer();
            }
        },
        
        closeModal() {
            this.$refs.confirmTransportModal.hide();
        },
        
        async proceedTransfer() {
            this.closeModal();

            try {
                this.active.disabled.main = true
                let path_method = 'registration/newPostTransferV2'

                if (this.regData?.health_facility_id == this.hfi_inpatient) {
                    // const { insurances, condition, indication, plan, type_emergency_id, type_visit_id, registration_id, doctor_in_charge_sec_id, ...rest } = this.form_inter
                    const { condition, indication, plan, type_emergency_id, type_visit_id, registration_id, doctor_in_charge_sec_id, ...rest } = this.form_inter
                    this.form_inter = {
                        ...rest,
                        registration_destination_id: this.regData?.registration_destination_id,
                    };
                    path_method = 'treatment/postNewTransfer'                    
                }

                await this.$store.dispatch(path_method, this.form_inter)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')                        
                        this.active.modal.main = false
                        // this.resetForm()
                        this.triggerToParent()
                        this.triggerToNotification()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.main = false
            }   
        },
        
        cancelTransfer() {
            this.closeModal();
        },

        triggerToParent() {
            let message = 'initial dulu'
            this.$emit("refreshRegdata", message);
        },

        triggerToNotification() {
            let params = {
                health_facility_item_id: this.bed_obtained.data.health_facility_item_id
            }

            try {
                this.$store.dispatch('position/getPosition', params)
                .then(
                    async res => {
                        // console.log('res', res);
                        if (res?.length > 0) {
                            this.socket.emit('SEND_SBAR', {
                                type: 'PATIENT_TRANSFER',
                                position_id: res[0].id,
                            })
                        }
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)    
            }
        },

        async resetForm() {
            this.form_inter.registration_destination_past_id = ''
            this.form_inter.registration_id = ''
            this.form_inter.nurse_id = ''
            this.form_inter.type_room_id = ''
            this.form_inter.room_id = ''
            this.form_inter.bed_id = ''
            this.form_inter.doctor_in_charge_id = ''
            this.form_inter.doctor_in_charge_id_2 = ''
            this.form_inter.doctor_in_charge_id_3 = ''
            this.form_inter.doctor_in_charge_id_4 = ''
            this.form_inter.doctor_in_charge_id_5 = ''
            this.form_inter.employee_nurse_id = ''
            this.form_inter.employee_doctor_id = ''
            this.form_inter.doctor_in_charge_sec_id = ''
            this.form_inter.insurances = []
            this.form_inter.aware = ''
            this.form_inter.eye_score = ''
            this.form_inter.verbal_score = ''
            this.form_inter.motor_score = ''
            this.form_inter.skin_color = ''
            this.form_inter.behavior = ''
            this.form_inter.systolic = ''
            this.form_inter.diastolic = ''
            this.form_inter.pulse_frequency = ''
            this.form_inter.breath_frequency = ''
            this.form_inter.temperature = ''
            this.form_inter.oximetry_frequency = ''
            this.form_inter.oxygen_type_id = ''
            this.form_inter.weight = ''
            this.form_inter.height = ''
            this.form_inter.pain_score = ''
            this.form_inter.ews_downe_score = ''
            this.form_inter.morse.history_of_falls = ''
            this.form_inter.morse.secondary_diagnosis = ''
            this.form_inter.morse.walking_ambulation = ''
            this.form_inter.morse.side_effects_of_falling = ''
            this.form_inter.morse.way_of_walking = ''
            this.form_inter.morse.mental_status = ''
            this.form_inter.morse.total_score = ''
            this.form_inter.humpty_dumpty.age = ''
            this.form_inter.humpty_dumpty.gender = ''
            this.form_inter.humpty_dumpty.diagnosis = ''
            this.form_inter.humpty_dumpty.cognitive_impairment = ''
            this.form_inter.humpty_dumpty.environmental_factors = ''
            this.form_inter.humpty_dumpty.surgery_sedation_anesthesia = ''
            this.form_inter.humpty_dumpty.use_of_medication = ''
            this.form_inter.humpty_dumpty.total_score = ''
            this.form_inter.mobilization = ''
            this.form_inter.time_out = moment(new Date()).format('YYYY-MM-DD HH:mm')
            this.form_inter.condition = ''
            this.form_inter.indication = ''
            this.form_inter.plan = ''
            this.form_inter.type_emergency_id = ''
            this.form_inter.type_visit_id = 2
            this.form_inter.is_surgery = ''
            this.form_inter.is_internal = ''
        },

        addDoctor() {
            if (this.temp_change.doctors?.length < 5) {
                this.temp_change.doctors.push({
                    doctor_in_charge_id: '',
                })
            } else {
                alert("maksimal 5 dokter")
            }
        },

        removeDoctor(index) {
            this.temp_change.doctors.splice(index, 1);
            this.findBed()
        },

        checkDuplicateDoctor(selectedDoctorId, selectedIndex) {
            // buat ngambil semua doctor_in_charge_id yang udah dipilih, kecuali yang lagi diubah
            const selectedDoctors = this.temp_change.doctors
            .map((doctor, index) => index !== selectedIndex ? doctor.doctor_in_charge_id : null) // buat ngindarin item yang lagi diedit
            .filter(doctorId => doctorId !== null); // kl data nya null, ga masuk daftar yg nanti bakal di bandingin
            
            // pastiin select yg dipilih ga sama kyk yg laen, trus pilihannya di kosongin
            if (selectedDoctors.includes(selectedDoctorId)) {
                alert("Dokter sudah dipilih sebelumnya. Silakan pilih dokter lain.");
                this.$set(this.temp_change.doctors[selectedIndex], 'doctor_in_charge_id', null); // Reset pilihan
            }

            this.findBed()
        },     
        
        getByNoka () {
            const param = { 
                url: 'noka',
                idx: this.temp_change.number_insurance 
            }

            try {
                this.$store.dispatch('patient/bpjs', param).then(response => {
                    const peserta = response.response.peserta
                    alert(peserta.statusPeserta.keterangan);
        
                    if (peserta.hakKelas.kode == '1') {
                        this.temp_change.service_class_id = { value: '8d3b8d25-4606-4db5-9ec1-59ec22514210', title: 'Kelas I' } 
        
                    } else if (peserta.hakKelas.kode == '2') {
                        this.temp_change.service_class_id = { value: 'f39fbb8f-082e-4c71-8802-92943b6e4683', title: 'Kelas II' } 
        
                    } else if (peserta.hakKelas.kode == '3') {
                        this.temp_change.service_class_id = { value: 'd168732a-fdef-456d-836c-750e003187e0', title: 'Kelas III' }
                    }
                })
            } catch (error) {
                console.error('Error while GET data from patient/bpjs at: ', error)
            }
        },

        keyDpjpInsurance (param_index) {
            const newInsurance = this.form_inter.insurances.map((item, index) =>
            index === param_index
                ? { ...item, is_primary: 1 }
                : { ...item, is_primary: 0 }
            );
            this.form_inter.insurances = newInsurance
            this.findBed()
        },

        addInsurance () {
            let input_insurance_id = this.temp_change.insurance_id.value
            let input_insurance_name = this.temp_change.insurance_id.title
            let input_company_id = this.temp_change.company_id.value
            let input_company_name = this.temp_change.company_id.title
            let input_service_class_id = this.temp_change.service_class_id.value
            let input_service_class_name = this.temp_change.service_class_id.title
            let input_number_insurance = this.temp_change.number_insurance

            try {
                if (input_insurance_id === 1) {
                    // cek udah ada insurance_id === 1
                    const existingInsurance = this.form_inter.insurances.find(item => item.insurance_id === 1);
                    const existingIsPrimary = this.form_inter.insurances.find(item => item.is_primary === 1);
                
                    let temp_is_primary = null
                    temp_is_primary = existingIsPrimary ? 0 : 1 
            
                    if (!existingInsurance) {
                        this.form_inter.insurances.push({
                            is_primary: temp_is_primary, 
                            insurance_id: input_insurance_id, 
                            company_id: '',
                            number_insurance: '-',
                            expired: '',
                            service_class_id: input_service_class_id,
                            
                            insurance_name: input_insurance_name, 
                            company_name: '-', 
                            service_class_name: input_service_class_name,
                        })
                        this.findBed()                        
                    } else {
                        this.notification('Warning', 'CloseIcon', 'warning', 'Tidak dapat menambahkan pembayaran Tunai/Pribadi lebih dari 1');
                    }
                
                } else if (input_insurance_id === 2) {
                    const existingInsurance = this.form_inter.insurances.find(item => item.insurance_id === 2);
                    const existingIsPrimary = this.form_inter.insurances.find(item => item.is_primary === 1);
                
                    let temp_is_primary = null
                    temp_is_primary = existingIsPrimary ? 0 : 1 
                    
                    if (!existingInsurance) { 
                        if (input_insurance_id == '' || input_company_id == '' || input_number_insurance == '' || input_service_class_id == '') {
                            this.notification('Failed','CloseIcon','danger','Lengkapi form penjamin terlebih dahulu')
                        } else {
                            this.form_inter.insurances.push({
                                is_primary: temp_is_primary, 
                                insurance_id: input_insurance_id,
                                company_id: input_company_id,
                                number_insurance: input_number_insurance,
                                service_class_id: input_service_class_id,

                                insurance_name: input_insurance_name,
                                company_name: input_company_name,
                                service_class_name: input_service_class_name,
                            })
                            this.findBed()                            
                        }
                    } else {
                        this.notification('Warning', 'CloseIcon', 'warning', 'Tidak dapat menambahkan pembayaran Asuransi lebih dari 1');
                    }
                
                } else if (input_insurance_id === 3) {
                    const existingInsurance = this.form_inter.insurances.find(item => item.insurance_id === 3);
                    const existingIsPrimary = this.form_inter.insurances.find(item => item.is_primary === 1);
                
                    let temp_is_primary = null
                    temp_is_primary = existingIsPrimary ? 0 : 1 
                    
                    if (!existingInsurance) { 
                        if (input_insurance_id == '' || input_company_id == '' || input_number_insurance == '' || input_service_class_id == '') {
                            this.notification('Failed','CloseIcon','danger','Lengkapi form penjamin terlebih dahulu')
                        } else {
                            this.form_inter.insurances.push({
                                is_primary: temp_is_primary, 
                                insurance_id: input_insurance_id,
                                company_id: input_company_id,
                                number_insurance: input_number_insurance,
                                service_class_id: input_service_class_id,

                                insurance_name: input_insurance_name,
                                company_name: input_company_name,
                                service_class_name: input_service_class_name,
                            })
                            this.findBed()                            
                        }
                    } else {
                        this.notification('Warning', 'CloseIcon', 'warning', 'Tidak dapat menambahkan pembayaran Perusahaan lebih dari 1');
                    }
                } 
                else {
                    this.notification('Failed','CloseIcon','danger','Lengkapi form penjamin terlebih dahulu')
                }
                
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','Lengkapi form penjamin terlebih dahulu')
            }

            this.temp_change.insurance_id = ''
            this.temp_change.company_id = ''
            this.temp_change.number_insurance = ''
            this.temp_change.expired = ''
            this.temp_change.service_class_id = ''
        },

        removeInsurance(param_index) {
            this.form_inter.insurances.splice(param_index, 1)
            this.findBed()
        },

        notification(a, b, c, d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d }
            })
        },

        getAllDoctorList() {
            try {                
                this.$store.dispatch('employee/getDoctorList', { is_specialist: 'all' }).then((response) => {
                    this.all_doctor = response
                }, error => {
                    console.error('Error while GET employee/getDoctorList general at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET employee/getDoctorList general at: ', error);
            }
        },

        getAllNurseList() {
            try {                
                this.$store.dispatch('employee/getNurseList').then((response) => {
                    this.all_nurse = response
                }, error => {
                    console.error('Error while GET employee/getNurseList at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET employee/getNurseList at: ', error);
            }
        },
    },

    created() {
        this.$store.dispatch('helper/getParam', 'type_room')
        this.$store.dispatch('helper/getParam', 'insurance')
        this.$store.dispatch('helper/getParamThirdPartyCompany', 'third_party_company')
        this.$store.dispatch('helper/getParamServiceClass', 'service_class')
        this.getAllDoctorList()
        this.getAllNurseList()
    }
}
</script>