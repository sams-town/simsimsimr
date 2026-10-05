<template>
    <b-overlay :show="show" rounded="sm">
        <div>
            <b-button @click="isBpjs()" size="sm" variant="pink-500" class="text-white"><b-icon icon="type-bold" scale="0.8"></b-icon> BPJS</b-button>
            <b-modal v-model="active.is_bpjs" @close="isBpjs()" title="BPJS" hide-footer>
                <div class="row">
                    <div class="col-12 col-lg-6 px-0 px-lg-3 mt-1">
                        <label class="c-form-label">Nomor Anggota </label>
                        <b-input-group>
                            <b-form-input v-model="form.insuranceInput.number_insurance"  @keyup.enter="getByNoka()"></b-form-input>
                            <b-input-group-append>
                                <b-button variant="primary-500" @click="getByNoka()"><feather-icon icon="SearchIcon" /></b-button>
                            </b-input-group-append>
                        </b-input-group>
                    </div>
                    <div class="col-12 col-lg-6 px-0 px-lg-3 mt-1">
                        <label class="c-form-label">Nomor Rujukan </label>
                        <b-input-group>
                            <b-form-input v-model="form.insuranceInput.noKunjungan"  @keyup.enter="getByKunjungan()"></b-form-input>
                            <b-input-group-append>
                                <b-button variant="primary-500" @click="getByKunjungan()"><feather-icon icon="SearchIcon" /></b-button>
                            </b-input-group-append>
                        </b-input-group>
                    </div>
                </div>
            </b-modal>
            <b-form>
                <div class="row">
                    <div class="col-lg-4">
                        <label class="c-form-label">Tipe Pendaftaran *</label>
                        <v-select v-model="form.type_register_id" aria-readonly="true" :options="OptionsTypeRegister" :reduce="OptionsTypeRegister => OptionsTypeRegister.value" label="title" placeholder="-- Tipe Pendaftaran --"></v-select>
                    </div>
                </div>
                <section>
                    <ModernLabel label="Informasi Pasien" class="mb-8 mt-72"/>
                    <section>
                        <div class="row">
                            <div class="col-lg mt-12">
                                <label class="c-form-label">Nama *</label>
                                <div class="d-flex justify-content-center align-items-center">
                                    <v-select v-model="form.alias" disabled class="col-2 pl-0" aria-readonly="true" :options="OptionsAlias" :reduce="OptionsAlias => OptionsAlias.value" label="title" placeholder="--Pilih --"></v-select>
                                    <b-form-input :disabled="active.isDisabled_patient_information" autocomplete="off" class="col" v-model="form.name" placeholder="Nama Lengkap"></b-form-input>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">No. RM *</label>
                                <b-form-input autocomplete="off" disabled v-model="form.norm" type="text"></b-form-input>
                            </div>
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">Tempat Lahir *</label>
                                <b-form-input v-model="form.birth_place" :disabled="active.isDisabled_patient_information" autocomplete="off"></b-form-input>
                            </div>
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">Tanggal Lahir *</label>
                                <b-form-input v-model="form.birth_date" :disabled="active.isDisabled_patient_information" autocomplete="off" type="date"></b-form-input>
                            </div>
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">Jenis Kelamin *</label>
                                <v-select v-model="form.sex" :disabled="active.isDisabled_patient_information" :options="OptionsSex" :reduce="OptionsSex => OptionsSex.value" required label="title" placeholder="--Pilih Kelamin --"></v-select>
                            </div>
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">Perkawinan *</label>
                                <v-select v-model="form.married_id" :disabled="active.isDisabled_patient_information" :options="OptionsMarried" :reduce="OptionsMarried => OptionsMarried.value" required label="title" placeholder="--Pilih Perkawinan --"></v-select>
                            </div>
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">No Telepon *</label>
                                <b-form-input v-model="form.phone" :disabled="active.isDisabled_patient_information" autocomplete="off" type="number"></b-form-input>
                            </div>
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">Kartu Identitas *</label>
                                <v-select v-model="form.typeidcard" :disabled="active.isDisabled_patient_information" aria-readonly="true" :options="OptionsTypeIdcard" :reduce="OptionsTypeIdcard => OptionsTypeIdcard.value" label="title" placeholder="-- Kartu Identitas --"></v-select>
                            </div>
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">No Identitas *</label>
                                <b-form-input v-model="form.idcard" :disabled="active.isDisabled_patient_information" autocomplete="off" type="number"></b-form-input>
                            </div>
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">Alamat</label>
                                <b-form-input v-model="form.address" :disabled="active.isDisabled_patient_information" autocomplete="off"></b-form-input>
                            </div>
                        </div>
                    </section>
                </section>
                <section v-if="form.type_register_id">
                    <ModernLabel label="Penanggung jawab" class="mb-8 mt-72"/>
                    <section>
                        <div v-if="form.type_register_id == '1' || form.type_register_id == '4'" class="row">
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">Nama Penanggung Jawab *</label>
                                <b-form-input autocomplete="off" v-model="form.responsible_person_name"></b-form-input>
                            </div>
                        </div>
                        <div v-if="form.type_register_id == '3'" class="row">
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">Hubungan Keluarga *</label>
                                <v-select v-model="form.responsible_id" :options="OptionsResponsible" :reduce="OptionsResponsible => OptionsResponsible.value" required label="title" placeholder="--Pilih Hubungan Keluarga --"></v-select>
                            </div>
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">Nama Penanggung Jawab *</label>
                                <b-form-input v-model="form.responsible_person_name" required autocomplete="off" type="text"></b-form-input>
                            </div>
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">Kartu Identitas *</label>
                                <v-select v-model="form.responsible_person_typeidcard" aria-readonly="true" :options="OptionsTypeIdcard" :reduce="OptionsTypeIdcard => OptionsTypeIdcard.value" label="title" placeholder="-- Kartu Identitas --"></v-select>
                            </div>
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">No Identitas *</label>
                                <b-form-input v-model="form.responsible_person_idcard" required autocomplete="off" type="number"></b-form-input>
                            </div>
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">No Telp/HP *</label>
                                <b-form-input v-model="form.responsible_person_phone" required autocomplete="off" type="number"></b-form-input>
                            </div>
                        </div>
                    </section>
                </section>
                <section >
                    <ModernLabel label="Cara Masuk & Penjamin" class="mb-20 mt-72"/>
                    <section >
                        <div class="row">
                            <div class="col-lg-3">
                                <label class="c-form-label">Cara Masuk *</label>
                                <v-select v-model="form.type_how_to_enter_id" aria-readonly="true" :options="OptionsTypeHowToEnter" :reduce="OptionsTypeHowToEnter => OptionsTypeHowToEnter" label="title" placeholder="--Cara Masuk --"></v-select>
                            </div>
                            <div class="col-lg-3 mt-12 mt-lg-0">
                                <label class="c-form-label">Pilih Nama Instansi Rujukan *</label>
                                <v-select v-model="form.how_to_enter_id" aria-readonly="true" :options="OptionsHowToEnter" :reduce="OptionsHowToEnter => OptionsHowToEnter" label="title" placeholder="--Cara Masuk --"></v-select>
                            </div>
                        </div>
                        <section class="mt-32">
                            <div>
                                <b-table fixed small bordered responsive :items="form.insuranceTable.items" :fields="form.insuranceTable.fields" head-variant="light" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="text-center">
                                    <template #cell(is_primary)=row>
                                        <template v-if="row.item.is_primary === 0">
                                            <b-button variant="warning-600" size="sm" @click="keyInsurance(row.item.number_insurance)">
                                                <feather-icon icon="KeyIcon" />
                                            </b-button>
                                        </template>
                                        <template v-if="row.item.is_primary === 1">
                                            <b-button variant="info-600" size="sm">Utama</b-button>
                                        </template>
                                    </template>
                                    <template #cell(Aksi)=data>
                                        <b-button variant="danger-600" size="sm" @click="deleteInsurance(data.index)">
                                            <feather-icon icon="TrashIcon" />
                                        </b-button>                                
                                    </template>
                                </b-table>
                            </div>
                            <div class="d-flex justify-content-end mt-32">
                                <b-button pill size="sm" variant="indigo-200" @click="active.insurence_form = true" class="px-3">
                                    <feather-icon icon="PlusIcon" class="mr-2" />
                                    <span class="text-indigo-700 fw-medium">Penjamin</span>
                                </b-button>
                            </div>
                            <div v-show="active.insurence_form" class="mt-2 mt-lg-0">
                                <div>
                                    <div class="row mx-0">
                                        <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                                            <label class="c-form-label">Jenis Penjamin</label>
                                            <v-select v-model="form.insuranceInput.insurance_id" :options="OptionsInsurance" :reduce="OptionsInsurance => OptionsInsurance" label="title" placeholder="-- Jenis Penjamin --"></v-select>
                                        </div>
                                        <div class="col" v-if="form.insuranceInput.insurance_id && form.insuranceInput.insurance_id.title === 'Asuransi'">
                                            <div class="row">
                                                <div class="col-12 col-lg-6 px-0 px-lg-3 mt-1">
                                                    <label class="c-form-label">Nama Penjamin </label>
                                                    <v-select v-model="form.insuranceInput.company_id" :options="Options3rdCompany" :reduce="Options3rdCompany => Options3rdCompany" label="title" placeholder="-- Nama Penjamin --"></v-select>
                                                </div>
                                                <div class="col-12 col-lg-6 px-0 px-lg-3 mt-1">
                                                    <label class="c-form-label">Nomor Anggota </label>
                                                    <b-input-group>
                                                        <b-form-input v-model="form.insuranceInput.number_insurance"  @keyup.enter="getByNoka()"></b-form-input>
                                                        <b-input-group-append>
                                                            <b-button variant="primary-500" @click="getByNoka()"><feather-icon icon="SearchIcon" /></b-button>
                                                        </b-input-group-append>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="form.insuranceInput.insurance_id && form.insuranceInput.insurance_id.title === 'Asuransi'" class="row mx-0 mt-lg-10">
                                        <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                                            <label class="c-form-label">Hak Kelas </label>
                                            <v-select v-model="form.insuranceInput.service_class_id" :options="OptionsServiceClass" :reduce="OptionsServiceClass => OptionsServiceClass" label="title" placeholder="-- Hak Kelas --"></v-select>
                                        </div>
                                        <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1" style="display:none;">
                                            <label class="c-form-label">Tanggal Akhir </label>
                                            <b-form-input v-model="form.insuranceInput.expired" type="date"></b-form-input>
                                        </div>
                                    </div>
                                </div>
                                <div class="col mt-16 mt-lg-24 d-flex">
                                    <div class="ml-auto row mx-0">
                                        <b-button @click="active.insurence_form = false" pill variant="outline-indigo" class="px-3 mr-2">Batal</b-button>
                                        <b-button @click.prevent="validationFormInsurance" pill variant="indigo" class="px-3">Tambah Penjamin</b-button>
                                    </div>
                                </div>
                            </div>
                        </section>                    
                    </section>
                </section>
                <section>
                    <ModernLabel label="Instalasi" class="mb-8 mt-72"/>
                    <section>
                        <div v-if="trigger.temp_health_facility_name == 'IGD'" class="row">
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">Instalasi *</label>
                                <v-select v-model="form.health_facility_id" disabled aria-readonly="true" :options="OptionsHealthFacility" :reduce="OptionsHealthFacility => OptionsHealthFacility" label="title" placeholder="-- Instalasi --"></v-select>
                            </div>
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">Poli</label>
                                <v-select v-model="form.health_facility_item_id" :disabled="active.isDisabled_health_facility" aria-readonly="true" :options="OptionsHealthFacilityItem" :reduce="OptionsHealthFacilityItem => OptionsHealthFacilityItem" label="title" placeholder="-- Poli --"></v-select>
                            </div>
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">Dokter</label>
                                <v-select v-model="form.doctor_in_charge_id" aria-readonly="true" :options="OptionsDoctor" :reduce="OptionsDoctor => OptionsDoctor" label="title" placeholder="-- Dokter --"></v-select>
                            </div>
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">Tipe Emergency</label>
                                <v-select v-model="form.type_emergency_id" aria-readonly="true" :options="OptionsTypeEmergency" :reduce="OptionsTypeEmergency => OptionsTypeEmergency" label="title" placeholder="-- Tipe Emergency --"></v-select>
                            </div>
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">Tipe Kasus Emergency</label>
                                <v-select v-model="form.type_emergency_case_id" aria-readonly="true" :options="OptionsTypeEmergencyCase" :reduce="OptionsTypeEmergencyCase => OptionsTypeEmergencyCase" label="title" placeholder="-- Kasus Emergency --"></v-select>
                            </div>
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">Nama Lokasi</label>
                                <b-form-input v-model="form.name_location" autocomplete="off" />
                            </div>
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">Lokasi LAKA</label>
                                <b-form-input v-model="form.case_location" autocomplete="off" />
                            </div>                        
                        </div>
                        <div v-if="trigger.temp_health_facility_name == 'Rawat Inap'" class="row">
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">Instalasi *</label>
                                <v-select v-model="form.health_facility_id" disabled aria-readonly="true" :options="OptionsHealthFacility" :reduce="OptionsHealthFacility => OptionsHealthFacility" label="title" placeholder="-- Instalasi --"></v-select>
                            </div>
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">Instalasi Sub</label>
                                <v-select v-model="form.health_facility_item_id" :disabled="active.isDisabled_health_facility" aria-readonly="true" :options="OptionsHealthFacilityItem" :reduce="OptionsHealthFacilityItem => OptionsHealthFacilityItem" label="title" placeholder="-- Poli --"></v-select>
                            </div>
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">Dokter</label>
                                <v-select v-model="form.doctor_in_charge_id" aria-readonly="true" :options="OptionsDoctor" :reduce="OptionsDoctor => OptionsDoctor" label="title" placeholder="-- Dokter --"></v-select>
                            </div>
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">Tipe Ruangan</label>
                                <v-select v-model="form.type_room_id" :options="TypeRoomOptions" :reduce="TypeRoomOptions => TypeRoomOptions.value" label="title" placeholder="-- Pilih Tipe Ruangan --"></v-select>
                            </div>
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">Ruangan</label>
                                <v-select v-model="form.room_id" :options="OptionsRoom" :reduce="OptionsRoom => OptionsRoom.value" label="title" placeholder="-- Ruangan --"></v-select>
                            </div>
                            <div class="col-lg-3 mt-12">
                                <label class="c-form-label">Bed</label>
                                <v-select v-model="form.bed_id" :options="OptionsBed" :reduce="OptionsBed => OptionsBed.value" label="title" placeholder="-- Bed --"></v-select>
                            </div>
                        </div>
                        <div v-if="trigger.temp_health_facility_name == 'Poliklinik'" class="row">
                            <template v-if="form.type_register_id == '1'">
                                <div class="col-lg-3 mt-12">
                                    <label class="c-form-label">Pilih Dokter</label>
                                    <b-input-group>
                                        <v-select v-model="form.doctor_in_charge_id" class="col px-0" disabled aria-readonly="true" :options="OptionsDoctor" :reduce="OptionsDoctor => OptionsDoctor" label="title" placeholder="-- Dokter --"></v-select>
                                        <b-input-group-append>
                                            <b-button variant="primary-500" @click="active.today_appointment_modal = !active.today_appointment_modal"><feather-icon icon="SearchIcon" /></b-button>
                                        </b-input-group-append>
                                    </b-input-group>
                                </div>
                                <div class="col-lg-3 mt-12">
                                    <label class="c-form-label">Poli</label>
                                    <v-select v-model="form.health_facility_item_id" disabled aria-readonly="true" :options="OptionsHealthFacilityItem" :reduce="OptionsHealthFacilityItem => OptionsHealthFacilityItem" label="title" placeholder="-- Poliklinik --"></v-select>
                                </div>
                            </template>
                            <template v-if="form.type_register_id == '2'">
                                <div class="col-lg-3 mt-12">
                                    <label class="c-form-label">PENUNJANG</label>
                                    <v-select v-model="form.health_facility_item_id_external" aria-readonly="true" :options="OptionsHealthFacilityItemExternal" :reduce="OptionsHealthFacilityItemExternal => OptionsHealthFacilityItemExternal" label="title" placeholder="-- Poliklinik --"></v-select>
                                </div>
                            </template>
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Diagnosa Awal *</label>
                            <v-select v-model="form.icd10_id" aria-readonly="true" @search="onSearchIcd10" :options="OptionsIcd10" :reduce="OptionsIcd10 => OptionsIcd10" label="title" placeholder="-- Diagnosa Awal --"></v-select>
                        </div>
                        <b-modal v-model="active.today_appointment_modal" scrollable size="lg" title="Pilih Dokter" hide-footer>
                            <section class="card">
                                <div class="card-header rounded border-bottom-0 bg-neutral-100">
                                    <div class="row">
                                        <div class="col-lg">
                                            <div class="row">
                                                <div class="col-lg-2 mt-12 mt-lg-0" style="display:none"> 
                                                    <label class="c-form-label">Instalasi *</label>
                                                    <v-select v-model="today_appointment_filter.health_facility_id" disabled aria-readonly="true" class="bg-white" :options="OptionsHealthFacility" :reduce="OptionsHealthFacility => OptionsHealthFacility.value" label="title" placeholder="--Instalasi --"></v-select>
                                                </div>
                                                <div class="col-lg-6 mt-12 mt-lg-0">
                                                    <label class="c-form-label">Poli</label>
                                                    <v-select v-model="today_appointment_filter.health_facility_item_id" aria-readonly="true" class="bg-white" :options="OptionsHealthFacilityItem" :reduce="OptionsHealthFacilityItem => OptionsHealthFacilityItem.value" label="title" placeholder="--Poliklinik --"></v-select>
                                                </div>
                                                <div class="col-lg-4 mt-12 mt-lg-0">
                                                    <label class="c-form-label">Dokter</label>
                                                    <v-select v-model="today_appointment_filter.doctor_id" aria-readonly="true" class="bg-white" :options="OptionsDoctor" :reduce="OptionsDoctor => OptionsDoctor.value" label="title" placeholder="--Dokter --"></v-select>
                                                </div>
                                                <div class="col-lg-2 mt-24 mt-lg-0">
                                                    <div class="d-flex align-items-lg-end h-100">
                                                        <b-button @click="FilterSchedule" variant="primary" class="btn-block">Cari</b-button>
                                                        <b-button @click="ClearFilterSchedule" variant="outline-primary" class="ml-1">Clear</b-button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            <section class="mt-12" style="min-height: 350px;">
                                <b-table bordered responsive :fields="doctor_list_fields" :items="doctor_list_items" thead-class="text-bold-14" tbody-tr-class="text-regular-12">
                                    <template #cell(schedule_item) = row>
                                        <div>
                                            {{ row.item.hour_start }} - {{ row.item.hour_end }}
                                        </div>
                                    </template>
                                    <template #cell(action) = row>
                                        <div>
                                            <b-button @click="selectedEntrySchedule(row.item)" size="sm" variant="primary">Pilih Jadwal</b-button>
                                        </div>
                                    </template>
                                </b-table>
                            </section>
                        </b-modal>
                        <b-modal v-model="active.refer_bpjs" scrollable size="lg" title="Rujukan BPJS" hide-footer>
                            <section class="card">
                                <div class="card-header rounded border-bottom-0 bg-neutral-100">
                                    <div class="row">
                                        <div class="col-lg">
                                            <div class="row">
                                                <b-table bordered responsive small head-variant="light" style="height: 400px" :items="refer_bpjs_data" :fields="ReferBpjsTable.fields" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                                                    <template #cell(action)="row">
                                                        <b-button size="sm" variant="primary-500" @click="pickControl(row.item)">Pilih</b-button>
                                                    </template>
                                                </b-table>
                                            </div>
                                            <div class="row">
                                                <b-table small hover responsive head-variant="light" :items="itemsPatient" :fields="fieldsPatient" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                                                    <template #cell(action)="row">
                                                        <div class="d-flex">
                                                            <b-button size="sm" @click="getPatientOnReg(row.item)" variant="info" class="mr-1">Daftarkan</b-button>
                                                        </div>
                                                    </template>
                                                </b-table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </b-modal>
                    </section>
                </section>
                <!-- <hr> -->
                <section class="d-flex justify-content-end mt-40">
                    <b-button pill variant="outline-primary" class="px-3 mr-2">Reset</b-button>
                    <b-button pill variant="primary" type="submit" :disabled="isDisabled" @click.prevent="validationForm" class="px-3">Simpan</b-button>
                </section>
            </b-form>
        </div>
    </b-overlay>
</template>
<script>
import vSelect from 'vue-select'
import _ from 'lodash'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import { mapState } from 'vuex'
import DatePicker from 'v-calendar'
import moment from '@/@core/assets/plugins/momentjs/moment'
import ModernLabel from '@/@core/components/label/ModernLabel.vue'

export default {
    components: { vSelect, DatePicker, ModernLabel },
    props: {
        dataSelected: {},
        dataFromCombinedAddPatient: {},
        dataFromAppointment: {},
    },
    data() {
        return {
            paramsPatient: {
                offset: 0,
                limit: 1,
                noka: '',
            },
            show: false,
            modelConfig: {
                type: 'string',
                mask: 'WWW, YYYY-MM-DD',
            },
            ReferBpjsTable: {
                fields: [
                    { key: 'tglKunjungan', label: 'Tgl.Kunjungan' },
                    { key: 'noKunjungan', label: 'No.Rujukan' },
                    { key: 'keteranganstatusPeserta', label: 'Status.Kartu' },
                    { key: 'nama', label: 'Nama' },
                    { key: 'nmProviderPerujuk', label: 'PPK Perujuk' },
                    { key: 'nama_poli', label: 'Sub/Specialist' },
                    { key: 'nama_poli', label: 'Sub/Specialist' },
                    { key: 'action', label: '' }
                ],
            },
            OptionsHealthFacilityItemExternal:[
                { value: '68be017e-9aa8-4847-8d89-646323f52d23', title: 'LABORATORIUM' },
                { value: '0233f15e-cd67-4c92-a3c1-b23928279d1d', title: 'RADIOLOGI' },
            ],
            active: {
                insurence_form: false,
                today_appointment_modal: false,
                isDisabled_health_facility: false,
                isDisabled_patient_information: true,
                refer_bpjs:false,
                is_bpjs:false
            },
            trigger: {
                temp_health_facility_name: '',
                // get_temp_health_facility_id: '',
                // get_temp_health_facility_item_id: '',
                // get_temp_doctor_in_charge_id: '',
                // get_temp_appointment_id: '',
            },
            today_appointment_filter: {
                day: '',
                health_facility_id: '3cb626f5-9148-4fb1-8663-3d7f028b01a6',
                health_facility_item_id: '',
                doctor_id: '',
            },
            selectedEntry: null,
            selectedRadio: null,
            doctorSelect: null,
            no_sep: false,
            isDisabled: false,
            penjamin: false,
            form: {
                norm:'',
                type_register_id: 1,

                married_id: '', // trigger
                type_room_id: '', // trigger
                room_id: '', // trigger
                idcard: '',
                typeidcard: '',
                alias: '',
                name:'',
                birth_date:'',
                birth_place:'',
                sex: '',
                address: '',
                phone: '',
                responsible_id: '', 
                responsible_person_idcard: '', 
                responsible_person_typeidcard: '', 
                responsible_person_name:'', 
                responsible_person_phone: '', 

                type_how_to_enter_id:'', // trigger
                how_to_enter_id:'',
                icd10_id:'',

                insuranceInput: {},
                insuranceToInput : [
                    { 
                        is_primary: 1,
                        insurance_id: 1,
                        company_id: '',
                        service_class_id: '',
                        number_insurance: '-',
                        expired: '',
                    }
                ],
                insuranceTable : {
                    fields: [
                        { key: 'is_primary', label: '' },
                        { key: 'insurance_name', label: 'Jenis Penjamin' },
                        { key: 'company_name', label: 'Nama Penjamin' },
                        { key: 'number_insurance', label: 'No Anggota' },
                        { key: 'service_class_name', label: 'Kelas Layanan' },
                        { key: 'Aksi' },
                    ],
                    items: [
                        { 
                            is_primary: 1, 
                            insurance_name: 'Tunai/Pribadi', 
                            company_name: '-', 
                            number_insurance: '-',
                            expired: '-',
                            service_class_name: '-',
                        }
                    ]
                },
                health_facility_id:'',
                health_facility_item_id:'',
                doctor_in_charge_id:'',
                bed_id:'', 
                type_emergency_id: '', 
                type_emergency_case_id: '', 
                case_location: '', 
                name_location: '',
                appointment_id:'', //back
            },
            refer_bpjs_data:[],
            OptionsIcd10:[],
            doctor_list_items_data: null,
            pickControlRefer:{}
        }
    },
    watch: {
        doctor_list_items (obj) {
            if(obj){
                this.doctor_list_items_data = obj
            }
        },
        'form.type_register_id': function () {
            this.form.health_facility_item_id = ''
            this.form.doctor_in_charge_id = ''
            this.form.type_room_id = '' // trigger
            this.form.room_id = '' // trigger
            this.form.bed_id = '' 
            this.form.type_emergency_id = {value:0, title:'-'}
            this.form.type_emergency_case_id = {value:0, title:'-'} 
            this.form.case_location = ''
            this.form.name_location = ''
            return this.getHealthFacility
        },
        'form.type_how_to_enter_id' (obj) {
            return this.$store.dispatch('howtoenter/getParam', obj.value)
        },
        'form.health_facility_id' (obj) {
            if (obj) {
                // this.form.health_facility_item_id = ''
                // this.form.doctor_in_charge_id = ''
                this.trigger.temp_health_facility_name = obj.title
                this.$store.dispatch('healthfacilityitem/getParam', obj.value)
                return this.getHealthFacilityItem
            } else {
                this.trigger.temp_health_facility_id = ''
            }
        },
        'form.health_facility_item_id' (obj) {
            if (obj) {
                return this.$store.dispatch('employee/getParam', obj.value)
            }
        },
        'today_appointment_filter.health_facility_id' (value){
            if (value) {
                return this.$store.dispatch('healthfacilityitem/getParam', value)
            }
        },
        'today_appointment_filter.health_facility_item_id' (value){
            if (value) {
                return this.$store.dispatch('employee/getParam', value)
            }
        },
        'form.type_room_id' (value){
            if (value) {
                return this.$store.dispatch('room/getParamRoom', value)
            }
        },
        'form.room_id' (value){
            if (value) {
                return this.$store.dispatch('room/getParamBed', value)
            }
        },
        'form.birth_date': function () {
            return this.getAlias
        },
        'form.sex': function () {
            return this.getAlias
        },
        'form.married_id': function () {
            return this.getAlias
        },
        dataSelected(value) {
            console.log('dataSelected: ', value);
            this.form.alias = value.alias
            this.form.name = value.name
            this.form.norm = value.norm
            this.form.birth_date = value.birth_date
            this.form.sex = value.sex
            this.form.married_id = value.married_id
            this.form.phone = value.phone
            this.form.typeidcard = value.typeidcard
            this.form.idcard = value.idcard
            this.form.responsible_id = value.patient_responsible_person.responsible_id, 
            this.form.responsible_person_name = value.patient_responsible_person.name
            this.form.responsible_person_idcard = value.patient_responsible_person.idcard 
            this.form.responsible_person_typeidcard = value.patient_responsible_person.typeidcard
            this.form.responsible_person_phone = value.patient_responsible_person.phone

            this.form.insuranceTable.items = value.patient_insurance
            this.form.insuranceToInput = value.patient_insurance
            // console.log('insurancetoinput: ',this.form.insuranceToInput, 'insurancetable: ', this.form.insuranceTable.items)

        },
        dataFromCombinedAddPatient(value) {
            console.log('combinedData di regis_form', value);
            this.form.norm = value.norm
            this.form.name = value.name
            this.form.birth_date = value.birth_date
            this.form.sex = value.sex
            this.form.phone = value.phone
            this.form.married_id = value.married_id
            this.form.typeidcard = value.typeidcard
            this.form.idcard = value.idcard
            
            this.form.responsible_id = value.responsible_id, 
            this.form.responsible_person_name = value.responsible_person_name
            this.form.responsible_person_idcard = value.responsible_person_idcard
            this.form.responsible_person_typeidcard = value.responsible_person_typeidcard
            this.form.responsible_person_phone = value.responsible_person_phone   
            
            this.form.insuranceTable.items = value.insuranceToInput
            this.form.insuranceToInput = value.insuranceToInput

            if (value.appointment_id) {
                this.form.appointment_id = value.appointment_id
            }

            if (value.health_facility_id && value.health_facility_item_id && value.doctor_in_charge_id) {
                this.form.health_facility_id = { value:value.health_facility_id, title:value.health_facility_name }
                this.form.health_facility_item_id = { value:value.health_facility_item_id, title:value.health_facility_item_name }
                this.form.doctor_in_charge_id = { value:value.doctor_in_charge_id, title:value.doctor_in_charge_name }
            }
            
        },
        dataFromAppointment(value) {
            console.log('dataFromAppointment: ', value);
            if (value.norm != '' && value.norm != null) {
                this.form.alias = value.alias
                this.form.name = value.name
                this.form.norm = value.norm
                this.form.birth_date = value.birth_date
                this.form.sex = value.sex
                this.form.married_id = value.married_id
                this.form.phone = value.phone
                this.form.idcard = value.idcard
                this.form.typeidcard = value.typeidcard
                this.form.responsible_person_name = value.responsible_person_name
                this.form.responsible_id = value.responsible_person_responsible_id, 
                this.form.responsible_person_idcard = value.responsible_person_idcard
                this.form.responsible_person_typeidcard = value.responsible_person_typeidcard
                this.form.responsible_person_phone = value.responsible_person_phone
                this.form.type_how_to_enter_id = 2
                this.form.how_to_enter_id = { value:'83ded3be-bf34-4e95-ba22-58f3cece3197', title:'Klinik' }
                this.form.appointment_id = value.id
                this.form.health_facility_id = { value:value.health_facility_id, title:value.health_facility_name }
                this.form.health_facility_item_id = { value:value.health_facility_item_id, title:value.health_facility_item_name }
                this.form.doctor_in_charge_id = { value:value.doctor_in_charge_id, title:value.doctor_in_charge_name }
                this.validationForm()
            }
        }
    },
    methods: {
        pickControl(data) {
            let hakKelas = ''
            if (data.kodehakKelas == '1') {
                hakKelas = {value:'8d3b8d25-4606-4db5-9ec1-59ec22514210',title:'Kelas I'} 
            } else if (data.kodehakKelas == '2') {
                hakKelas = {value:'f39fbb8f-082e-4c71-8802-92943b6e4683',title:'Kelas II'} 
            } else if (data.kodehakKelas == '3') {
                hakKelas = {value:'d168732a-fdef-456d-836c-750e003187e0',title:'Kelas III'}
            }
            this.form.insuranceTable.items = []
            this.form.insuranceTable.items.push({
                is_primary: 1, 
                insurance_name: 'Asuransi', 
                company_name: 'BPJS Kesehatan', 
                number_insurance: data.noKartu,
                expired: data.tglTAT,
                service_class_name: data.keteranganhakKelas,
            })
            this.form.insuranceToInput = []
            this.form.insuranceToInput.push({
                is_primary: 1, 
                insurance_id: 2, 
                company_id: '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8',
                number_insurance: data.noKartu,
                expired: data.tglTAT,
                service_class_id: hakKelas,
            })
            if (data.noKartu) {
                this.$store.dispatch('patient/getBackdateNoka', data.noKartu)
                .then(res => {
                    this.$store.dispatch('patient/getAll', {norm:res})
                    .then(res => {
                    },
                    err => {
                    })
                })
            }
        },
        isBpjs() {
            this.active.is_bpjs = !this.active.is_bpjs
        },
        getByKunjungan () {
            this.$store.dispatch('patient/bpjs', {url:'Rujukan',idx:this.form.insuranceInput.noKunjungan})
            .then(response_refer => {
                console.log(response_refer)
                if (response_refer.status == 201) {
                    alert(response_refer.message)
                } else {
                    this.refer_bpjs_data = response_refer.data
                }
                this.active.refer_bpjs = true
            })
            // this.validationFormInsurance()
        },
        getByNoka () {
            this.$store.dispatch('patient/bpjs', {url:'noka',idx:this.form.insuranceInput.number_insurance})
            .then(response => {
                alert(response.response.peserta.statusPeserta.keterangan);
                if (response.response.peserta.hakKelas.kode == '1') {
                    this.form.insuranceInput.service_class_id = {value:'8d3b8d25-4606-4db5-9ec1-59ec22514210',title:'Kelas I'} 
                } else if (response.response.peserta.hakKelas.kode == '2') {
                    this.form.insuranceInput.service_class_id = {value:'f39fbb8f-082e-4c71-8802-92943b6e4683',title:'Kelas II'} 
                } else if (response.response.peserta.hakKelas.kode == '3') {
                    this.form.insuranceInput.service_class_id = {value:'d168732a-fdef-456d-836c-750e003187e0',title:'Kelas III'}
                }
                this.form.insuranceInput.expired = response.response.peserta.tglTAT
                // this.form.name = response.response.peserta.nama
                // this.form.idcard = response.response.peserta.nik
                // this.form.birth_date = response.response.peserta.tglLahir
                // if (response.response.peserta.sex == 'P') {
                //     this.form.sex = 0
                // } else {
                //     this.form.sex = 1
                // }
                this.refer_bpjs_data = []
                this.$store.dispatch('patient/bpjs', {url:'RujukanList',idx:this.form.insuranceInput.number_insurance})
                .then(response_refer => {
                    console.log(response_refer)
                    if (response_refer.status == 201) {
                        alert(response_refer.message)
                    } else {
                        this.refer_bpjs_data = response_refer.data
                    }
                    this.active.refer_bpjs = true
                })
            // this.validationFormInsurance()
            })
        },
        getPatientOnReg (value) {
            this.form.alias = value.alias
            this.form.name = value.name
            this.form.norm = value.norm
            this.form.birth_date = value.birth_date
            this.form.sex = value.sex
            this.form.married_id = value.married_id
            this.form.phone = value.phone
            this.form.typeidcard = value.typeidcard
            this.form.idcard = value.idcard
            this.active.refer_bpjs = false
            this.active.is_bpjs = false
            this.form.type_how_to_enter_id = {value:2,title:'Rujukan Puskesmas/Klinik'}
            this.$store.dispatch('howtoenter/getParam', 2)
            this.form.how_to_enter_id = {value:'cd67965b-b409-474c-a6a9-b4be1a1a9d5a',title:'Puskesmas A'}
            // this.form.type_how_to_enter_id = {value:'4c5237fc-a5bf-46f5-ba10-1511ec524e15',title:'Rujukan Rumah Sakit'}
            this.form.responsible_id = value.patient_responsible_person.responsible_id
            this.form.responsible_person_name = value.patient_responsible_person.name
            this.form.responsible_person_idcard = value.patient_responsible_person.idcard 
            this.form.responsible_person_typeidcard = value.patient_responsible_person.typeidcard
            this.form.responsible_person_phone = value.patient_responsible_person.phone

            this.form.insuranceTable.items = value.patient_insurance
            this.form.insuranceToInput = value.patient_insurance
            // this.$emit('getPatientOnReg', data)
            // console.log('getPatientOnReg',data);
        },
        deleteInsurance (index) {
            this.form.insuranceTable.items.splice(index, 1)
            this.form.insuranceToInput.splice(index, 1)
            console.log('index: ', index, 'data: ', this.form.insuranceToInput)
        },
        keyInsurance (index) {
            const newinsuranceTable = this.form.insuranceTable.items.map(p =>
            p.number_insurance === index
                ? { ...p, is_primary: 1 }
                : { ...p, is_primary: 0 }
            );
            this.form.insuranceTable.items = newinsuranceTable

            const newinsuranceToInput = this.form.insuranceToInput.map(p =>
            p.number_insurance === index
                ? { ...p, is_primary: 1 }
                : { ...p, is_primary: 0 }
            );
            this.form.insuranceToInput = newinsuranceToInput
        },
        validationFormInsurance () {
            if (this.form.insuranceInput.insurance_id.value === 1) {
                this.form.insuranceTable.items.push({
                    insurance_name: this.form.insuranceInput.insurance_id.title, 
                    is_primary: 1, 
                    company_name: '-', 
                    number_insurance: '-',
                    expired: '-',
                    service_class_name: '-',
                })
                this.form.insuranceToInput.push({
                    is_primary: 1, 
                    insurance_id: this.form.insuranceInput.insurance_id.value, 
                    company_id: '',
                    number_insurance: '-',
                    expired: '',
                    service_class_id: '',
                })
            } else {
                this.form.insuranceTable.items.push({
                    is_primary: 0, 
                    insurance_name: this.form.insuranceInput.insurance_id.title, 
                    company_name: this.form.insuranceInput.company_id.title, 
                    number_insurance: this.form.insuranceInput.number_insurance,
                    expired: this.form.insuranceInput.expired,
                    service_class_name: this.form.insuranceInput.service_class_id.title,
                })
                this.form.insuranceToInput.push({
                    is_primary: 0, 
                    insurance_id: this.form.insuranceInput.insurance_id.value, 
                    company_id: this.form.insuranceInput.company_id.value,
                    number_insurance: this.form.insuranceInput.number_insurance,
                    expired: this.form.insuranceInput.expired,
                    service_class_id: this.form.insuranceInput.service_class_id.value,
                })
            }
            console.log(this.form.insuranceTable.items)
            console.log(this.form.insuranceToInput)
            this.form.insuranceInput.insurance_id = ''
            this.form.insuranceInput.company_id = ''
            this.form.insuranceInput.number_insurance = ''
            this.form.insuranceInput.expired = ''
            this.form.insuranceInput.service_class_id = ''
            this.active.insurence_form = false
        },
        calculateAge() {
            const birth_date = new Date(this.form.birth_date);
            const today = new Date();
            const ageInMilliseconds = today - birth_date;
            const ageInYears = ageInMilliseconds / 31556952000;
            this.age = Math.floor(ageInYears);
        },
        FilterSchedule () {
            const today_date = new Date()
            const today = moment(today_date).format('ddd')

            switch (today) {
                case 'Mon':
                    this.today_appointment_filter.day = '1'
                    break;
                case 'Tue':
                    this.today_appointment_filter.day = '2'
                    break;
                case 'Wed':
                    this.today_appointment_filter.day = '3'
                    break;
                case 'Thu':
                    this.today_appointment_filter.day = '4'
                    break;
                case 'Fri':
                    this.today_appointment_filter.day = '5'
                    break;
                case 'Sat':
                    this.today_appointment_filter.day = '6'
                    break;
                case 'Sun':
                    this.today_appointment_filter.day = '7'
                    break;
                default:
                    break;
            }
        
            // console.log('date:', today_date, 'day: ', today, 'day_number: ', this.today_appointment_filter.day);
            
            if (this.today_appointment_filter.day) {
                this.show = true
                this.isDisabledFilter = true
                this.$store.dispatch('doctorscheduleitem/getAll', this.today_appointment_filter)
                .then(res => {
                    if (this.doctor_list_items_data) {
                        if (this.doctor_list_items_data.length == 0 || this.doctor_list_items_data == null) {
                            this.notification('Notification','BellIcon','primary','There is no doctor on duty today')
                        }
                    }
                    this.show = false
                    this.isDisabledFilter = false 
                },
                err => {
                    this.show = false
                    this.isDisabledFilter = false
                })
            } else {
                this.notification('Failed','CloseIcon','warning','Date is required')
            }
        },
        ClearFilterSchedule(){
            this.today_appointment_filter.health_facility_id = '3cb626f5-9148-4fb1-8663-3d7f028b01a6'
            this.today_appointment_filter.day = ''
            this.today_appointment_filter.health_facility_item_id = ''
            this.today_appointment_filter.doctor_id = ''
            this.$store.dispatch('doctorscheduleitem/clear')
        },
        selectedEntrySchedule (selected) {
            console.log('selected: ', selected);
            // this.form.health_facility_id = { value:selected.health_facility_id, title:selected.health_facility_name }
            this.form.health_facility_item_id = { value:selected.health_facility_item_id, title:selected.health_facility_item_name }
            this.form.doctor_in_charge_id = { value:selected.doctor_id, title:selected.doctor_name }
            this.active.today_appointment_modal = false
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
        active_payment(selected) {
            this.penjamin = selected
        },
        validationForm () {
            const formData = this.form 
            console.log('fromdata: ', formData);
            this.isDisabled = true
            this.$store.dispatch('registration/formPostPut', formData)
            .then(res => {
                console.log('response: ', res);
                // this.notification('Success','CheckIcon','success','You have successfully')
                if (res.status == 200 || res.status == 201) {
                    this.notification('Success','CheckIcon','success','You have successfully')
                } else if (res.status == 404) {
                    this.notification('Warning','AlertCircleIcon','warning','Registration can be done on the recorded appointment date')
                } else if (res.status == 422) {
                    this.notification('Warning','AlertCircleIcon','warning',"The doctor's schedule was missed")
                }
                this.isDisabled = false
                
            }, 
            err => {
                this.isDisabled = false
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
            })
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        }
    },
    computed: {
        ...mapState({ 
            fieldsPatient: state => state.patient.fields, 
            itemsPatient: state => state.patient.items, 

            OptionsHealthFacility: state => state.healthfacility.options.healthfacility,
            OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
            OptionsDoctor: state => state.employee.options.employee,
            OptionsHowToEnter: state => state.howtoenter.options.howtoenter,
            
            OptionsTypeHowToEnter: state => state.helper.options.howtoenter,
            OptionsInsurance: state => state.helper.options.insurance,
            Options3rdCompany: state => state.helper.options.third_party_company,
            OptionsServiceClass: state => state.helper.options.service_class,
            OptionsTypeRegister: state => state.helper.options.typeRegister,

            doctor_list_items: state => state.doctorscheduleitem.items,
            doctor_list_fields: state => state.doctorscheduleitem.fields,

            OptionsResponsible: state => state.helper.options.responsible,
            OptionsTypeIdcard: state => state.helper.options.type_idcard,
            OptionsTypeEmergency: state => state.helper.options.typeEmergency,
            OptionsTypeEmergencyCase: state => state.helper.options.typeEmergencyCase,
            OptionsSex: state => state.helper.options.sex,
            OptionsAlias: state => state.helper.options.alias,
            OptionsMarried: state => state.helper.options.married,
            TypeRoomOptions: state => state.helper.options.type_room,
            // TypeStatusBedOptions: state => state.helper.options.type_status_bed,
            OptionsRoom: state => state.room.options.room,
            OptionsBed: state => state.room.options.bed,

        }),
        getAlias() {
            this.calculateAge()
            if (this.form.married_id != 1) {
                if (this.form.sex == 0) {
                    return this.form.alias = 5
                } else {
                    return this.form.alias = 6
                }
            } else {
                if (this.age >= 0 && this.age <= 17) {
                    return this.form.alias = 1
                } else {
                    if (this.form.sex == 0) {
                        return this.form.alias = 4
                    } else {
                        return this.form.alias = 6
                    }
                }
            }
        },
        getHealthFacility() {
            if (this.form.type_register_id == '1') { // 1 = Rawat Jalan
                this.form.health_facility_id = { value: '3cb626f5-9148-4fb1-8663-3d7f028b01a6', title: 'Poliklinik' }
                this.active.isDisabled_patient_information = true
            } else if (this.form.type_register_id == '2'){ // 2 = External
                this.active.isDisabled_patient_information = true
            } else if (this.form.type_register_id == '3'){ // 3 = Emergency
                this.form.health_facility_id = { value: '444b3134-5b75-4b67-af72-a558d46716d7', title: 'IGD' }
                this.active.isDisabled_health_facility = true
                if (this.form.norm) {
                    this.active.isDisabled_patient_information = true
                }else {
                    this.active.isDisabled_patient_information = false
                }
            } else if (this.form.type_register_id == '4'){ // 4 = Rawat Inap
                this.form.health_facility_id = { value: '40f06534-07ec-4221-ad9d-dea08196079f', title: 'Rawat Inap' }
                this.active.isDisabled_patient_information = true
                this.active.isDisabled_health_facility = false
            }
        },
        getHealthFacilityItem() {
            if (this.form.health_facility_id.title == 'IGD') {
                this.form.health_facility_item_id = { value: "cca59ec4-d77a-45fd-b51f-aae069da22ea", title: "IGD" }
            }
        }

    },
    created () {
        this.getHealthFacility
        this.$store.dispatch('doctorscheduleitem/clear')
        // this.$store.dispatch('healthfacility/getParam', 'healthfacility')

        // this.$store.dispatch('helper/getParamThirdPartyCompany', 'third_party_company')
        // this.$store.dispatch('helper/getParamServiceClass', 'service_class')

        // this.$store.dispatch('helper/getParam', 'type_how_to_enter')
        // this.$store.dispatch('helper/getParam', 'insurance')
        // this.$store.dispatch('helper/getParam', 'type_register')

        // this.$store.dispatch('helper/getParam', 'responsible')
        // this.$store.dispatch('helper/getParam', 'typeidcard')
        // this.$store.dispatch('helper/getParam', 'type_emergency')
        // this.$store.dispatch('helper/getParam', 'type_emergency_case')
        // this.$store.dispatch('helper/getParam', 'sex')
        // this.$store.dispatch('helper/getParam', 'alias')
        // this.$store.dispatch('helper/getParam', 'married')
        // this.$store.dispatch('helper/getParam', 'type_room')
        // this.$store.dispatch('helper/getParam', 'type_status_bed')
        if (this.form.type_register_id == '3' && this.form.norm ) {
            this.active.isDisabled_patient_information = true
        }
            
    },
    updated() {
        if (this.form.type_register_id == '3' && this.form.norm ) {
            this.active.isDisabled_patient_information = true
        }
    },
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>