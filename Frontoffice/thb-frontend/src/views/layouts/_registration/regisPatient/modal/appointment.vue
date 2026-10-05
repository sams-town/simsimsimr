<template>
    <div>
        <b-button @click="toggleMainModal" size="sm" variant="pink-500" class="text-white"><b-icon icon="calendar" scale="0.8"></b-icon> Appointment</b-button>
        <b-modal v-model="active.main_modal" scrollable size="lg" title="Appointment" hide-footer>
            <div class="col px-0 d-flex">
                <b-button pill @click="toggleAddModal" variant="indigo" class="px-3 ml-auto">
                    <b-icon icon="plus"></b-icon>
                    <span>Tambah</span>
                </b-button>
            </div>
            <div class="card mt-16">
                <div class="card-header bg-indigo">
                    <p class="my-0 fw-600 c-text-16-18 text-light">Pencarian Pasien</p>
                </div>
                <div class="card-body">
                    <div>
                        <div class="col px-0">
                            <div class="row mx-0">
                                <div class="col-lg-6">
                                    <label class="c-form-label">No RM</label>
                                    <b-form-input v-model="params.norm"></b-form-input>
                                </div>
                                <div class="col-lg-6 mt-12 mt-lg-0">
                                    <label class="c-form-label">Nama Pasien</label>
                                    <b-form-input type="text" v-model="params.name"></b-form-input>
                                </div>
                            </div>
                            <div class="row mx-0">
                                <div class="col-lg-6 mt-12">
                                    <label class="c-form-label">Tempat tanggal lahir</label>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <b-form-input type="text" v-model="params.birth_place"></b-form-input>
                                        </div>
                                        <div class="col-lg-6 mt-2 mt-lg-0">
                                            <b-form-input type="date" v-model="params.birth_date"></b-form-input>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mt-12">
                                    <label class="c-form-label">Jenis Kelamin</label>
                                    <v-select v-model="params.sex" :options="OptionsSex" :reduce="OptionsSex => OptionsSex.value" label="title" placeholder="--Pilih Kelamin --"></v-select>
                                </div>
                            </div>
                            <div class="row mx-0">
                                <div class="col-lg-6 mt-12">
                                    <label class="c-form-label">No Telepon/ No Handphone</label>
                                    <b-form-input type="number" v-model="params.phone"></b-form-input>
                                </div>
                                <div class="col-lg-6 mt-12">
                                    <label class="c-form-label">Instalasi</label>
                                    <v-select v-model="params.health_facility_id" aria-readonly="true" :options="OptionsHealthFacility" :reduce="OptionsHealthFacility => OptionsHealthFacility.value" label="title" placeholder="--Instalasi --"></v-select>
                                </div>
                            </div>
                            <div class="row mx-0">
                                <div class="col-lg-6 mt-12">
                                    <label class="c-form-label">Poli</label>
                                    <v-select v-model="params.health_facility_item_id" aria-readonly="true" :options="OptionsHealthFacilityItem" :reduce="OptionsHealthFacilityItem => OptionsHealthFacilityItem.value" label="title" placeholder="--Poliklinik --"></v-select>
                                </div>
                                <div class="col-lg-6 mt-12">
                                    <label class="c-form-label">Dokter</label>
                                    <v-select v-model="params.doctor_in_charge_id" aria-readonly="true" :options="OptionsDoctor" :reduce="OptionsDoctor => OptionsDoctor.value" label="title" placeholder="--Dokter --"></v-select>
                                </div>
                            </div>
                            <div class="row mx-0">
                                <div class="col-lg-8 px-0 mt-12">
                                    <div class="d-flex align-items-center">
                                        <div class="col">
                                            <label class="c-form-label">Tanggal Appointment Mulai</label>
                                            <b-form-input type="date" v-model="params.date_start"></b-form-input>
                                        </div>
                                        <span>-</span>
                                        <div class="col">
                                            <label class="c-form-label">Tanggal Appointment Akhir</label>
                                            <b-form-input type="date" v-model="params.date_end"></b-form-input>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="row mx-0">
                                <div class="col-lg-6 mt-12">
                                    <label class="c-form-label">Waktu Appointment</label>
                                    <b-form-input type="date" v-model="params.date_appointment"></b-form-input>
                                </div>
                            </div> -->
                            <div class="col mt-3 d-flex justify-content-end">
                                <b-button type="submit" variant="primary" class="mr-1" :disabled="isDisabledFilter" @click.prevent="filterData">
                                    <feather-icon icon="SearchIcon"/> Filter
                                </b-button>
                                <b-button variant="outline-primary" @click.prevent="filterReset">
                                    <feather-icon icon="XIcon"/> Clear Filter
                                </b-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-16">
                <div class="card-body">
                    <div class="row mx-0">
                        <b-form-group label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                            <span class="text-regular-14">Tampilkan</span>
                            <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
                            <span class="text-regular-14">Entri</span>
                        </b-form-group>
                    </div>
                    <div class="col px-0">
                        <b-table bordered responsive hover small head-variant="light" :items="items" :fields="fields" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                            <template #cell(norm)="row">
                                <template v-if="!row.item.nobooking">
                                    <b-badge @click="getFromAppointment(row.item)" variant="info-600" class="pointer p-2 font-weight-normal">{{ row.value != '' ? row.value : 'Ambil No RM' }}</b-badge>
                                </template>
                                <template v-else>
                                    {{ row.value }}
                                </template>
                            </template>
                            <template #cell(name)="row">
                                {{ row.item.name }}, <br>
                                {{ row.item.birth_date }}, <br>
                                {{ row.item.birth_place }}, <br>
                                {{ row.item.sex_name }}
                            </template>
                            <template #cell(health_facility_item_name)="row">
                                {{ row.item.health_facility_item_name }}, <br>
                                {{ row.item.doctor_in_charge_name }}
                            </template>
                            <template #cell(date_appointment)="row">
                                {{ row.item.date_appointment }}, <br>
                                {{ row.item.time_appointment }}
                            </template>
                            <template #cell(nobooking)="row">
                                <template v-if="row.item.nobooking">
                                    Mobile JKN <b>{{ row.item.nobooking }}</b>
                                </template>
                                <template v-if="row.item.type_source_id === 3">
                                    Website RS
                                </template>
                                <template v-if="row.item.type_source_id === 0 && !row.item.nobooking">
                                    SIMRS
                                </template>
                            </template>
                            <template #cell(action)="row">
                                <div class="d-flex justify-content-center">
                                    <!-- <b-button @click="rowOnEdit(row.item)" variant="warning-500" size="sm" class="mr-1">
                                        <feather-icon icon="EditIcon" />
                                    </b-button> -->
                                    <b-button @click="rowOnDelete(row.item.id)" variant="danger-500" size="sm">
                                        <feather-icon icon="TrashIcon" />
                                        <b-link v-b-modal.modal-center></b-link>
                                        Batalkan
                                    </b-button>
                                </div>
                            </template>
                        </b-table>
                    </div>
                    <div class="d-flex">
                        <span class="text-regular-14">Menampilkan {{ firstRows }} hingga <template v-if="lastRows < totalRows">{{ lastRows }}</template> <template v-else>{{ totalRows }}</template> dari {{ totalRows }} entri</span>
                        <div class="ml-auto">
                            <b-pagination v-model="currentPage" :total-rows="totalRows"
                                :per-page="perPage" :current-page="currentPage"
                                align="fill" size="sm" class="my-0"></b-pagination>
                        </div>
                    </div>
                </div>
            </div>
        </b-modal>
        <b-modal v-model="active.modal_add" size="sm" hide-footer hide-header hide-backdrop content-class="shadow">
            <div class="p-2 text-white bg-indigo-600" style="margin: -1rem;">Pilih Pasien</div>
            <!-- <create_appointment /> -->
            <div class="row mx-0 gap-2 text-white mt-4">
                <div @click="toggleNewPatient" class="col-12 col-lg px-2">
                    <div class="rounded pointer bg-info-800">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-center">
                                <img src="../../../../../@core/assets/icons/registration/user-plus.svg" class="mr-2" width="40px" alt="">
                                <p class="px-0 text-center fw-500 c-text-18-20">Pasien Baru</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- @click="toggleOldPatient" -->
                <div @click="onSearchOldPatient()" class="col-12 col-lg px-2">
                    <div class="rounded pointer bg-orange-500">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-center">
                                <img src="../../../../../@core/assets/icons/registration/user.svg" class="mr-2" width="40px" alt="">
                                <p class="px-0 text-center fw-500 c-text-18-20">Pasien Lama</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col px-2 mt-3 d-flex justify-content-end">
                <b-button @click="toggleAddModal" pill variant="indigo" class="px-20">Close</b-button>
            </div>
        </b-modal>
        <b-modal v-model="active.modal_new_patient" size="lg" title="Pasien Baru" hide-footer no-close-on-backdrop>
            <div class="card">
                <div class="card-header bg-indigo">
                    <p class="my-0 text-semi-16 text-light">Data Pasien</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="c-form-label">Nama Pasien</label>
                            <b-form-input @input="create_new_appointment_form.name = $event.toUpperCase()" v-model="create_new_appointment_form.name"></b-form-input>
                        </div>
                        <div class="col-lg-6 mt-12 mt-lg-0">
                            <label class="c-form-label">Tempat, tanggal lahir</label>
                            <div class="row">
                                <div class="col-lg-6">
                                    <b-form-input v-model="create_new_appointment_form.birth_place"></b-form-input>
                                </div>
                                <div class="col-lg-6 mt-2 mt-lg-0">
                                    <b-form-input type="date" v-model="create_new_appointment_form.birth_date"></b-form-input>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mt-12">
                            <label class="c-form-label">Jenis Kelamin</label>
                            <v-select v-model="create_new_appointment_form.sex" :options="OptionsSex" :reduce="OptionsSex => OptionsSex.value" label="title" placeholder="--Pilih Kelamin --"></v-select>
                        </div>
                        <div class="col-lg-6 mt-12">
                            <label class="c-form-label">No Telp/No Hp</label>
                            <b-form-input type="number" v-model="create_new_appointment_form.phone"></b-form-input>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mt-12">
                            <label class="c-form-label">Waktu Appointment</label>
                            <b-input-group>
                                <b-form-input type="date" disabled v-model="create_new_appointment_form.date_appointment"></b-form-input>
                                <b-input-group-append>
                                    <b-button variant="primary-500" @click="toggleSchedule('new_patient')"><feather-icon icon="SearchIcon" /></b-button>
                                </b-input-group-append>
                            </b-input-group>
                        </div>
                        <div class="col-lg-6 mt-12">
                            <label class="c-form-label">Nomor Antrian</label>
                            <b-form-input disabled v-model="create_new_appointment_form.queue"></b-form-input>
                        </div>
                    </div>
                    <div class="mt-32 d-flex justify-content-end">
                        <b-button @click="clearCreateAppointment" pill variant="outline-primary-500" class="mr-2">Reset</b-button>
                        <b-button @click="onSubmitAppointment" pill variant="primary-500">Submit</b-button>
                    </div>
                </div>
            </div>
        </b-modal>
        <b-modal v-model="active.modal_schedule" size="xl" title="Jadwal Dokter Harian" scrollable hide-footer no-close-on-backdrop @close="toggleSchedule('closed')">
            <div class="row">
                <!-- top-left section -->
                <section class="col-lg-6">
                    <section class="card">
                        <div class="card-header font-weight-bold bg-primary-200">Filter</div>
                        <div class="card-body">
                            <div class="row">
                                <!-- <div class="col-lg-6"> -->
                                    <!-- :available-dates="availableDates" -->
                                    <!-- <DatePicker v-model="trigger.sch_date_appointment" required :model-config="modelConfig" color="purple" title-position="left" is-expanded is-required :min-date='new Date()' /> -->

                                <!-- </div> -->
                                <div class="col-lg-12">
                                    <div>
                                        <div class="mt-12 mt-lg-0 d-none">
                                            <label class="c-form-label">Instalasi</label>
                                            <v-select disabled v-model="trigger.sch_health_facility_id" aria-readonly="true" :options="OptionsHealthFacility" :reduce="OptionsHealthFacility => OptionsHealthFacility.value" label="title" placeholder="-- Instalasi --"></v-select>
                                        </div>
                                        <div class="mt-12 mt-lg-0">
                                            <label class="c-form-label">Poli</label>
                                            <v-select v-model="params_schedule.health_facility_item_id" aria-readonly="true" :options="OptionsHealthFacilityItem" :reduce="OptionsHealthFacilityItem => OptionsHealthFacilityItem.value" label="title" placeholder="-- Poli --"></v-select>
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Dokter</label>
                                            <v-select v-model="params_schedule.doctor_id" aria-readonly="true" :options="OptionsDoctor" :reduce="OptionsDoctor => OptionsDoctor.value" label="title" placeholder="-- Dokter --"></v-select>
                                        </div>
                                        <div class="mt-12 mt-lg-0">
                                            <label class="c-form-label">Tanggal</label>
                                            <b-form-input type="date" v-model="trigger.sch_date_appointment"></b-form-input>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-24">
                                        <b-button @click="ClearFilterSchedule" variant="outline-primary" class="mr-1">
                                            <feather-icon icon="XIcon" class="mr-1" /> Clear
                                        </b-button>
                                        <b-button @click="FilterSchedule" variant="primary">
                                            <feather-icon icon="SearchIcon" class="mr-1" /> Cari
                                        </b-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- bottom-left section -->
                    <section class="card mt-24">
                        <div class="card-header bg-neutral-100">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="text-bold-16">Daftar Dokter</div>
                                <div class="text-regular-14">{{ trigger.sve_date_appointment }}</div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <b-table bordered responsive :fields="doctor_list_fields" :items="doctor_list_items" thead-class="text-bold-14" tbody-tr-class="text-regular-12">
                                <template #cell(action) = row>
                                    <div class="w-100 text-center">
                                        <b-button size="sm" variant="outline-primary" @click="pushDetailToBO(row.item.id, trigger.qbo_date_appointment)">Pilih</b-button>
                                    </div>
                                </template>
                                <template #cell(schedule_item) = row>
                                    <div>
                                        {{ row.item.hour_start }} - {{ row.item.hour_end }}
                                    </div>
                                </template>
                            </b-table>
                        </div>
                    </section>
                </section>
                <!-- right section -->
                <div class="col-lg-6">
                    <div class="card bg-neutral-100">
                        <div class="card-body">
                            <div>
                                <div class="text-uppercase text-bold-14">Nama Dokter</div>
                                <div>{{ quota_bo_items_data? quota_bo_items_data.rows.doctor_name : '-' }}</div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 mt-12">
                                    <div class="text-uppercase text-bold-14">Tgl Praktek</div>
                                    <div>{{ quota_bo_items_data? quota_bo_items_data.rows.date_appointment : '-' }}</div>
                                </div>
                                <div class="col-lg-4 mt-12">
                                    <div class="text-uppercase text-bold-14">Jam Praktek</div>
                                    <div>{{ quota_bo_items_data? quota_bo_items_data.rows.hour_start : '-' }} s/d {{ quota_bo_items_data? quota_bo_items_data.rows.hour_end : '-' }}</div>
                                </div>
                                <div class="col-lg-4 mt-12">
                                    <div class="text-uppercase text-bold-14">Lama Periksa /Pasien</div>
                                    <div>{{ quota_bo_items_data? quota_bo_items_data.rows.minutes_long : '-' }} Menit</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class=" mt-16">
                        <b-table bordered responsive :fields="quota_bo_fields" :items="quota_bo_items_data? quota_bo_items_data.rows.queue : []" thead-class="text-bold-14" tbody-tr-class="text-regular-12">
                            <template #cell(queue)="data">
                                <div class="text-center">{{ data.value }}</div>
                            </template>
                            <template #cell(reserve)="row">
                                <div>
                                    <b-button v-if="row.value == 0" @click="getSelectedData(row.item.queue, )" variant="outline-primary" size="sm">Pilih Waktu ini</b-button>
                                    <div v-else>WALK IN</div>
                                </div>
                            </template>
                        </b-table>
                    </div>
                </div>
            </div>
        </b-modal>
        <b-modal v-model="active.modal_old_patient" size="lg" title="Pasien Lama" hide-footer no-close-on-backdrop>
            <div class="card">
                <div class="card-header bg-indigo">
                    <p class="my-0 text-semi-16 text-light">Data Pasien</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg">
                            <label class="c-form-label">Nomor RM</label>
                            <b-form-input v-model="create_old_appointment_form.norm"></b-form-input>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mt-12">
                            <label class="c-form-label">Waktu Appointment</label>
                            <b-input-group>
                                <b-form-input type="date" disabled v-model="create_old_appointment_form.date_appointment"></b-form-input>
                                <b-input-group-append>
                                    <b-button variant="primary-500" @click="toggleSchedule('old_patient')"><feather-icon icon="SearchIcon" /></b-button>
                                </b-input-group-append>
                            </b-input-group>
                        </div>
                        <div class="col-lg-6 mt-12">
                            <label class="c-form-label">Nomor Antrian</label>
                            <b-form-input disabled v-model="create_old_appointment_form.queue"></b-form-input>
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="col-lg-6 mt-12">
                            <label class="c-form-label">Waktu Appointment</label>
                            <b-form-input type="datetime-local" v-model="create_old_appointment_form.date_appointment"></b-form-input>
                        </div>
                        <div class="col-lg-6 mt-12">
                            <label class="c-form-label">Instalasi</label>
                            <v-select v-model="create_old_appointment_form.health_facility_id" aria-readonly="true" :options="OptionsHealthFacility" :reduce="OptionsHealthFacility => OptionsHealthFacility" label="title" placeholder="--Instalasi --"></v-select>
                        </div>
                    </div> -->
                    <!-- <div v-if="create_old_appointment_form.health_facility_id.title === 'Poliklinik'" class="row">
                        <div class="col-lg-6 mt-12">
                            <label class="c-form-label">Poli</label>
                            <v-select v-model="create_old_appointment_form.health_facility_item_id" aria-readonly="true" :options="OptionsHealthFacilityItem" :reduce="OptionsHealthFacilityItem => OptionsHealthFacilityItem" label="title" placeholder="--Poliklinik --"></v-select>
                        </div>
                        <div class="col-lg-6 mt-12">
                            <label class="c-form-label">Dokter</label>
                            <v-select v-model="create_old_appointment_form.doctor_in_charge_id" aria-readonly="true" :options="OptionsDoctor" :reduce="OptionsDoctor => OptionsDoctor" label="title" placeholder="--Dokter --"></v-select>
                        </div>
                    </div> -->
                    <div class="mt-32 d-flex justify-content-end">
                        <b-button @click="clearCreateAppointment" pill variant="outline-primary-500" class="mr-2">Reset</b-button>
                        <b-button @click="onSubmitOldAppointment" pill variant="primary-500">Submit</b-button>
                    </div>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import moment from 'moment'
import DatePicker from 'v-calendar'
import vSelect from 'vue-select'
import { mapState } from 'vuex'
// import ModernLabel from '@/@core/components/label/ModernLabel.vue'

export default {
    components: {
        vSelect, DatePicker, //ModernLabel
    },
    props: {
        dataFromSearch: {},
    },
    data() {
        return {
            modelConfig: {
                type: 'string',
                mask: 'WWW, YYYY-MM-DD',
            },
            trigger: {
                qbo_date_appointment: '', // for quota BO
                qbo_schedule_item_id: '', // for quota BO and selected date_appointment
                sve_date_appointment: '', // for display on card header
                sch_date_appointment: '', // input filter
                sch_health_facility_id: '3cb626f5-9148-4fb1-8663-3d7f028b01a6', // input filter instalasi poliklinik

            },
            currentPage: 1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            isDisabled: false,
            isDisabledFilter: false,
            show: false,
            active: {
                purpose_open_modal: '',
                main_modal: false,
                modal_add: false,
                modal_new_patient: false,
                modal_old_patient: false,
                modal_schedule: false,
            },
            params: {
                offset: 0,
                limit: 20,
                norm: '',
                name: '',
                birth_place: '',
                birth_date: '',
                sex: '',
                phone: '',
                health_facility_id: '',
                health_facility_item_id: '',
                doctor_in_charge_id: '',
                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date_end: moment(new Date()).format('YYYY-MM-DD'),
                // date_appointment: '',
            },
            params_schedule: {
                day: '',
                health_facility_item_id: '',
                doctor_id: '',
            },
            create_new_appointment_form: {
                id: '',
                name: '',
                birth_date: '',
                birth_place: '',
                sex: '',
                phone: '',
                date_appointment: '',
                doctor_schedule_item_id: '',
                queue: '',
            },
            create_old_appointment_form: {
                id: '',
                norm: '',
                date_appointment: '',
                doctor_schedule_item_id: '',
                queue: '',
            },
            quota_bo_items_data:null,
        }
    },
    watch: {
        quota_bo_items (obj) {
            if(obj){
                this.quota_bo_items_data = obj
            }
        },
        // 'create_new_appointment_form.health_facility_id' (obj) {
        //     return this.$store.dispatch('healthfacilityitem/getParam', obj.value)
        // },
        // 'create_new_appointment_form.health_facility_item_id' (obj) {
        //     return this.$store.dispatch('employee/getParam', obj.value)
        // },
        // 'create_old_appointment_form.health_facility_id' (obj) {
        //     return this.$store.dispatch('healthfacilityitem/getParam', obj.value)
        // },
        // 'create_old_appointment_form.health_facility_item_id' (obj) {
        //     return this.$store.dispatch('employee/getParam', obj.value)
        // },
        'params.health_facility_id' (value) {
            return this.$store.dispatch('healthfacilityitem/getParam', value)
        },
        'params.health_facility_item_id' (value) {
            return this.$store.dispatch('employee/getParam', value)
        },
        'trigger.sch_date_appointment' (value) {
            if (value) {
                const date = moment(value).format('ddd')
                this.trigger.qbo_date_appointment = moment (value).format('YYYY-MM-DD')
                switch (date) {
                    case 'Mon':
                        this.params_schedule.day = '1'
                        break;
                    case 'Tue':
                        this.params_schedule.day = '2'
                        break;
                    case 'Wed':
                        this.params_schedule.day = '3'
                        break;
                    case 'Thu':
                        this.params_schedule.day = '4'
                        break;
                    case 'Fri':
                        this.params_schedule.day = '5'
                        break;
                    case 'Sat':
                        this.params_schedule.day = '6'
                        break;
                    case 'Sun':
                        this.params_schedule.day = '7'
                        break;
                    default:
                        break;
                }
            }else {
                this.trigger.sch_date_appointment = ''
                this.params_schedule.day = ''
                this.trigger.qbo_date_appointment = ''
            }
        },
        'trigger.sch_health_facility_id' (value) {
            return this.$store.dispatch('healthfacilityitem/getParam', value)
        },
        'params_schedule.health_facility_item_id' (value) {
            return this.$store.dispatch('employee/getParam', value)
        },
        dataFromSearch (obj) {
            this.create_old_appointment_form.norm = obj.norm
            this.active.modal_old_patient = true
        },
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.$store.dispatch('appointment/getAll',this.params)
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.$store.dispatch('appointment/getAll',this.params)
            }
        },
    },
    mounted() {
        // this.filterData()
    },
    methods: {
        onSearchOldPatient(){
            // console.log('clicked')
            this.active.main_modal = false
            this.active.modal_add = false
            this.$emit('onSearchOldPatient', 'clicked')
        },
        pushDetailToBO(sch_item_id, booking_date){
            this.trigger.qbo_schedule_item_id = sch_item_id
            const formData = {
                doctor_schedule_item_id: this.trigger.qbo_schedule_item_id,
                booking_date: booking_date
            }
            this.$store.dispatch('doctorscheduleitem/getDetailQuotaBO', formData)
        },
        getSelectedData(selected_queue){
            let init_date_appointment = ''
            if (this.quota_bo_items_data) {
                init_date_appointment = this.quota_bo_items_data.rows.date_appointment
            }

            if (this.active.purpose_open_modal === 'new_patient') {
                this.create_new_appointment_form.date_appointment = init_date_appointment
                this.create_new_appointment_form.doctor_schedule_item_id = this.trigger.qbo_schedule_item_id
                this.create_new_appointment_form.queue = selected_queue
                // console.log('new appointment', this.create_new_appointment_form);
                this.toggleSchedule('closed')
            } else if (this.active.purpose_open_modal === 'old_patient'){
                this.create_old_appointment_form.date_appointment = init_date_appointment
                this.create_old_appointment_form.doctor_schedule_item_id = this.trigger.qbo_schedule_item_id
                this.create_old_appointment_form.queue = selected_queue
                // console.log('old appointment', this.create_old_appointment_form);
                this.toggleSchedule('closed')
            }
        },
        toggleMainModal() {
            this.active.main_modal = !this.active.main_modal
        },
        toggleAddModal() {
            this.active.modal_add = !this.active.modal_add
            this.clearCreateAppointment()
        },
        toggleNewPatient() {
            this.active.modal_new_patient = !this.active.modal_new_patient
            this.clearCreateAppointment()
        },
        toggleOldPatient() {
            this.active.modal_old_patient = !this.active.modal_old_patient
            this.clearCreateAppointment()
        },
        toggleSchedule(purpose) {
            switch (purpose) {
                case 'old_patient':
                    this.active.modal_schedule = true
                    this.active.purpose_open_modal = purpose
                    // console.log(purpose);
                    break;
                case 'new_patient':
                    this.active.modal_schedule = true
                    this.active.purpose_open_modal = purpose
                    // console.log(purpose);
                    break;
                case 'closed':
                    this.active.modal_schedule = false
                    // clear
                    this.ClearFilterSchedule()
                    this.trigger.qbo_date_appointment = ''
                    this.trigger.qbo_schedule_item_id = ''
                    this.quota_bo_items_data = null

                    if (this.active.purpose_open_modal == 'old_patient'){
                        this.modal_old_patient = true
                        this.modal_new_patient = false
                        this.active.purpose_open_modal = ''
                    } else if (this.active.purpose_open_modal == 'new_patient'){
                        this.modal_new_patient = true
                        this.modal_old_patient = false
                        this.active.purpose_open_modal = ''
                    }

                    break;
                default:
                    this.active.modal_schedule = false
                    break;
            }
            // this.clearCreateAppointment()
        },
        FilterSchedule () {
            if (this.params_schedule.day) {
                this.trigger.sve_date_appointment = this.trigger.sch_date_appointment
                this.show = true
                this.isDisabledFilter = true
                this.$store.dispatch('doctorscheduleitem/getAll', this.params_schedule)
                .then(res => {
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
            this.trigger.sve_date_appointment = ''
            this.trigger.sch_date_appointment = ''
            this.params_schedule.day = ''
            this.params_schedule.health_facility_item_id = ''
            this.params_schedule.doctor_id = ''
            this.$store.dispatch('doctorscheduleitem/clear')
        },
        filterData () {
            this.show = true
            this.isDisabledFilter = true
            this.$store.dispatch('appointment/getAll', this.params)
            .then(res => {
                this.show = false
                this.isDisabledFilter = false
            },
            err => {
                this.show = false
                this.isDisabledFilter = false
            })
        },
        filterReset(){
            this.params.offset = 0
            this.params.limit = this.perPage
            this.params.norm = ''
            this.params.name = ''
            this.params.birth_place = ''
            this.params.birth_date = ''
            this.params.sex = ''
            this.params.phone = ''
            this.params.health_facility_id = ''
            this.params.health_facility_item_id = ''
            this.params.doctor_in_charge_id = ''
            this.params.date_start = moment(new Date()).format('YYYY-MM-DD'),
            this.params.date_end = moment(new Date()).format('YYYY-MM-DD'),

            this.$store.dispatch('appointment/getAll', this.params)
        },
        clearCreateAppointment(){
            // new patinet appointment
            this.create_new_appointment_form.name = ''
            this.create_new_appointment_form.birth_place = ''
            this.create_new_appointment_form.birth_date = ''
            this.create_new_appointment_form.sex = ''
            this.create_new_appointment_form.phone = ''
            this.create_new_appointment_form.date_appointment = ''
            this.create_new_appointment_form.queue = ''
            this.create_new_appointment_form.doctor_schedule_item_id = ''

            // old-patient appointment
            this.create_old_appointment_form.norm = ''
            this.create_old_appointment_form.date_appointment = ''
            this.create_old_appointment_form.queue = ''
            this.create_old_appointment_form.doctor_schedule_item_id = ''
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        onSubmitAppointment(){
            this.$store.dispatch('appointment/formPostPut', this.create_new_appointment_form)
            .then(response => {
                if (response.status === 201 || response.status === 200) {
                    this.$store.dispatch('appointment/getDetail').then(() => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.toggleNewPatient()
                        this.active.modal_add = false
                    })
                } else {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    // console.log('status', response.status);
                }
            },
            error => {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.log('error: ', error);
            })
        },
        onSubmitOldAppointment(){
            this.$store.dispatch('appointment/formPostPut', this.create_old_appointment_form)
            .then(response => {
                if (response.status === 201 || response.status === 200) {
                    this.$store.dispatch('appointment/getDetail').then(() => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.toggleOldPatient()
                        this.active.modal_add = false
                    })
                } else {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                }
            },
            error => {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
            })
        },
        rowOnEdit(obj){
            if (obj.norm == '' || obj.norm == null) {
                this.toggleNewPatient()
                this.create_new_appointment_form.id = obj.id
                this.create_new_appointment_form.name = obj.name
                this.create_new_appointment_form.birth_place = obj.birth_place
                this.create_new_appointment_form.birth_date = obj.birth_date
                this.create_new_appointment_form.sex = obj.sex
                this.create_new_appointment_form.phone = obj.phone
                this.create_new_appointment_form.date_appointment = obj.date_appointment
                this.create_new_appointment_form.queue = obj.queue
                this.create_new_appointment_form.doctor_schedule_item_id = obj.doctor_schedule_item_id
            } else {
                this.toggleOldPatient()
                this.create_old_appointment_form.id = obj.id
                this.create_old_appointment_form.norm = obj.norm
                this.create_old_appointment_form.date_appointment = obj.date_appointment
                this.create_old_appointment_form.queue = obj.queue
                this.create_old_appointment_form.doctor_schedule_item_id = obj.doctor_schedule_item_id
            }
        },
        rowOnDelete (id) {
            this.$bvModal
                .msgBoxConfirm('Please confirm that you want to delete everything.', {
                title: 'Please Confirm', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('appointment/remove', id)
                    .then(response => {
                        if (response.status === 200) {
                            this.$store.dispatch('appointment/getAll', this.params)
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

        getFromAppointment (data) {
            this.toggleMainModal()
            this.$emit('getFromAppointment', data)
        }
    },
    computed: {
        upperName: {
            get() {
                return this.create_new_appointment_form.name;
            },
            set(value) {
                this.create_new_appointment_form.name = value.toUpperCase();
            },
        },
        ...mapState({
            // from appointment
            fields: state => state.appointment.fields,
            items: state => state.appointment.items,
            totalRows: state => state.appointment.totalRows,
            pageOptions: state => state.patient.pageOptions,

            // from helper
            OptionsSex: state => state.helper.options.sex,

            // from health facility
            OptionsHealthFacility: state => state.healthfacility.options.healthfacility,
            OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,

            // from employee
            OptionsDoctor: state => state.employee.options.employee,

            // from doctor schedule item
            doctor_list_items: state => state.doctorscheduleitem.items,
            doctor_list_fields: state => state.doctorscheduleitem.fields,
            quota_bo_items: state => state.doctorscheduleitem.quotaBOItems,
            quota_bo_fields: state => state.doctorscheduleitem.quota_bo_fields,
        }),
        // availableDates() {
        //     const tomorrow = new Date();
        //     tomorrow.setDate(tomorrow.getDate() + 1);

        //     return {
        //         start: tomorrow,
        //         end: null,
        //     };
        // },
    },
    created () {
        // this.$store.dispatch('helper/getParam', 'sex')
        // this.$store.dispatch('healthfacility/getParam', 'healthfacility')
        // this.$store.dispatch('healthfacilityitem/getParam', this.trigger.sch_health_facility_id)
        this.$store.dispatch('doctorscheduleitem/clear')
        this.show = true
    },
}
</script>