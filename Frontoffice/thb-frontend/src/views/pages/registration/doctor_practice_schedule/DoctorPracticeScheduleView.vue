<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Pendaftaran' },
                { text: 'Jadwal Dokter' },
                // { text: this.form_transaction.no_order }
            ]"
        >
        <topMenu selectedMenu="menu5" class="mt-2" />
        <div class="card mt-3">
            <div class="card-body">
                <div class="col px-0">
                    <p class="fw-600 c-text-18-20">{{ head_title }}</p>
                    <div class="col px-0 mt-8 mt-lg-20">
                        <div class="flex flex-wrap">
                            <b-button pill @click="goToTab('sheet')" class="mr-2" :variant="active.tab == 'sheet' ? 'primary' : 'outline-primary'">
                                <feather-icon icon="MenuIcon" class="mr-1" />List Jadwal
                            </b-button>
                            <b-button pill @click="goToTab('additional')" class="mr-2" :variant="active.tab == 'additional' ? 'primary' : 'outline-primary'">
                                <feather-icon icon="PlusIcon" class="mr-1" />Tambah Jadwal
                            </b-button>
                            <b-button pill @click="goToTab('rest')" class="mr-2" :variant="active.tab == 'rest' ? 'primary' : 'outline-primary'">
                                Cuti Dokter
                            </b-button>
                        </div>
                    </div>
                </div>
                <section v-if="active.tab == 'sheet'">
                    <div class="row mt-20 mt-lg-32">
                        <div class="col-12 col-lg-4">
                            <div class="card-header rounded bg-primary-300 border-bottom-0 text-semi-16 mb-16 text-neutral-700">
                                Pilih Tanggal
                            </div>
                            <DatePicker v-model="date" color="purple" title-position="left" is-expanded />
                        </div>
                        <div class="col-12 col-lg mt-12 mt-lg-0">
                            <div class="card">
                                <div class="card-header bg-primary-300">
                                    <div class="d-flex">
                                        <p class="text-neutral-700 text-semi-16">Jadwal Dokter Harian</p>
                                        <p class="text-neutral-700 text-regular-14 ml-auto">Rabu, 14 Dec 2022</p>
                                    </div>
                                </div>
                                <div class="card-body" style="background-color: #dcf3f3; min-height: 400px;">
                                    <div class="col px-0">
                                        <v-select placeholder="-- pilih --" class="bg-white"></v-select>
                                    </div>
                                    <b-table bordered :fields="daily_schedule.fields" :items="daily_schedule.items" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 mt-lg-20"></b-table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section v-if="active.tab == 'additional'">
                    <section class="mt-4">
                        <ModernLabel label="Tambah Jadwal Dokter" class="mt-32" />
                        <div class="mt-16">
                            <div class="row">
                                <div class="col-lg-2">
                                    <label class="c-form-label">Instalasi</label>
                                    <v-select v-model="trigger.health_facility_id" :options="OptionsHealthFacility" :reduce="OptionsHealthFacility => OptionsHealthFacility" label="title" placeholder="-- Instalasi --"></v-select>
                                </div>
                                <div class="col-lg-3 mt-12 mt-lg-0">
                                    <label class="c-form-label">Poli</label>
                                    <v-select v-model="add_schedule_form.health_facility_item_id" aria-readonly="true" :options="OptionsHealthFacilityItem" :reduce="OptionsHealthFacilityItem => OptionsHealthFacilityItem.value" label="title" placeholder="-- Poli --"></v-select>
                                </div>
                                <div class="col-lg-3 mt-12 mt-lg-0">
                                    <label class="c-form-label">Dokter</label>
                                    <v-select v-model="add_schedule_form.doctor_id" aria-readonly="true" :options="OptionsDoctor" :reduce="OptionsDoctor => OptionsDoctor.value" label="title" placeholder="-- Dokter --"></v-select>
                                </div>
                                <div class="col-lg-2 mt-12 mt-lg-0">
                                    <label class="c-form-label">Kode</label>
                                    <v-select v-model="add_schedule_form.code" aria-readonly="true" :options="OptionsCode" :reduce="OptionsCode => OptionsCode.value" label="title" placeholder="-- Kode --"></v-select>
                                </div>
                                <div class="col-lg-2 mt-12 mt-lg-0">
                                    <label class="c-form-label">Hari</label>
                                    <v-select v-model="trigger.day_id" @input="onAddSchedule($event)" :options="OptionsDay" :reduce="OptionsDay => OptionsDay" label="title" placeholder="-- Pilih Hari --"></v-select>
                                </div>
                            </div>
                            <b-table-simple fixed class="mt-24" size="sm">
                                <b-thead class="text-bold-14">
                                    <b-tr>
                                        <b-th>Hari</b-th>
                                        <b-th>Waktu Mulai</b-th>
                                        <b-th>Waktu Akhir</b-th>
                                        <b-th>Kuota</b-th>
                                        <b-th>Kuota Booking</b-th>
                                        <b-th style="width: 10%;">Aksi</b-th>
                                        <!-- <b-th>Kuota JKN</b-th> -->
                                    </b-tr>
                                </b-thead>
                                <b-tbody class="text-regular-12">
                                    <b-tr v-for="(item, index) in add_schedule_form.doctor_schedule_items" :key="index">
                                        <b-td>
                                            <v-select disabled v-model="item.day" :options="OptionsDay" :reduce="OptionsDay => OptionsDay.value" label="title" placeholder="-- Pilih Hari --"></v-select>
                                        </b-td>
                                        <b-td>
                                            <b-form-input v-model="item.hour_start" type="time" />
                                        </b-td>
                                        <b-td>
                                            <b-form-input v-model="item.hour_end" type="time" />
                                        </b-td>
                                        <b-td>
                                            <b-input-group append="Pasien">
                                                <b-form-input type="number" v-model="item.quota" placeholder="kuota" />
                                            </b-input-group>
                                        </b-td>
                                        <b-td>
                                            <b-input-group append="Pasien">
                                                <b-form-input type="number" v-model="item.quota_bo" placeholder="kuota booking"/>
                                            </b-input-group>
                                        </b-td>
                                        <td>
                                            <b-button variant="danger-600" size="sm" @click="onDeleteSchedule(index)" class="mr-2">
                                                <feather-icon icon="TrashIcon" />
                                            </b-button>
                                        </td>
                                        <!-- <b-td>
                                            <b-input-group append="Pasien">
                                                <b-form-input type="number" v-model="item.quota_jkn" placeholder="kuota JKN"/>
                                            </b-input-group>
                                        </b-td> -->
                                    </b-tr>
                                </b-tbody>
                            </b-table-simple>
                            <div class="d-flex justify-content-end mt-24">
                                <b-button @click="ResetAddScheduleForm" variant="outline-primary" class="mr-2">Reset</b-button>
                                <b-button @click.prevent="validationForm" variant="primary">Submit</b-button>
                            </div>
                        </div>
                    </section>
                    <!-- <hr> -->
                    <section class="card mt-8 mt-lg-72">
                        <div class="card-header font-weight-bold bg-primary-200">Filter Data</div>
                        <div class="card-body d-flex justify-content-center">
                            <div class="col-lg-8">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label class="c-form-label">Departemen</label>
                                        <v-select placeholder="-- Departemen --"></v-select>
                                    </div>
                                    <div class="col-lg-6 mt-12 mt-lg-0">
                                        <label class="c-form-label">Dokter</label>
                                        <v-select placeholder="-- Dokter --"></v-select>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-24">
                                    <b-button pill variant="outline-primary">
                                        <feather-icon icon="XIcon" /> Clear Filter
                                    </b-button>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="mt-20 mt-lg-24">
                        <b-table-simple small bordered responsive>
                            <b-thead class="text-bold-14 text-center">
                                <b-tr>
                                    <b-th style="min-width: 250px;">Poliklinik</b-th>
                                    <b-th rowspan="2" style="vertical-align: middle; min-width: 140px;">Senin</b-th>
                                    <b-th rowspan="2" style="vertical-align: middle; min-width: 140px;">Selasa</b-th>
                                    <b-th rowspan="2" style="vertical-align: middle; min-width: 140px;">Rabu</b-th>
                                    <b-th rowspan="2" style="vertical-align: middle; min-width: 140px;">Kamis</b-th>
                                    <b-th rowspan="2" style="vertical-align: middle; min-width: 140px;">Jumat</b-th>
                                    <b-th rowspan="2" style="vertical-align: middle; min-width: 140px;">Sabtu</b-th>
                                    <b-th rowspan="2" style="vertical-align: middle; min-width: 140px;">Minggu</b-th>
                                </b-tr>
                                <b-tr>
                                    <b-th>Dokter</b-th>
                                </b-tr>
                            </b-thead>
                            <b-tbody class="text-regular-12">
                                <!-- Loop through grouped data -->
                                <template v-for="(groupedByHealthFacility, healthFacilityItemId) in groupedData">
                                    <b-tr>
                                        <b-td colspan="8" class="bg-neutral-100 font-weight-bold">{{ groupedByHealthFacility[0].health_facility_item_name }}</b-td>
                                    </b-tr>
                                    <!-- Loop through grouped data by doctor -->
                                    <template v-for="(groupedByDoctor, doctorId) in groupByDoctor(groupedByHealthFacility)">
                                        <b-tr>
                                            <b-td>
                                                <label @click="onDetailScheduleModal(groupedByDoctor[0].doctor_id, groupedByDoctor[0].doctor_name, groupedByDoctor[0].code)">{{ groupedByDoctor[0].doctor_name }}</label>
                                            </b-td>
                                            <td v-for="day in days" :key="day">
                                                <template v-for="schedule in groupedByDoctor">
                                                    <div class="bg-neutral-100 my-1">
                                                        <template v-if="schedule.doctor_schedule_items">
                                                            <template v-for="scheduleItem in schedule.doctor_schedule_items">
                                                                <div v-if="scheduleItem.day === days.indexOf(day) + 1">
                                                                    <label class="fw-medium my-2">
                                                                        <p class="text-primary-500 font-weight-bold" style="white-space: nowrap;">{{ scheduleItem.hour_start }} - {{ scheduleItem.hour_end }}</p>
                                                                    </label>
                                                                </div>
                                                            </template>
                                                        </template>
                                                    </div>
                                                </template>
                                            </td>
                                        </b-tr>
                                    </template>
                                </template>
                            </b-tbody>
                        </b-table-simple>

                        <b-modal scrollable v-model="active.schedule_modal" @close="onDetailScheduleModal('','','')" size="lg" hide-footer title="Detail Jadwal Dokter">
                            <ModernLabel :label="`${this.active.temp_in_modal.doctor_name}`" id="top" />
                            <div class="row mt-16">
                                <!-- <div class="col-lg-8 border-right custom_scroll">
                                    <div v-for="(list, index) in getDataByDoctorId" :key="list.id" class="card bg-neutral-100" :class="index == 0 ? 'mt-0' : 'mt-12'">
                                        <div class="card-body">
                                            <div>
                                                <p class="text-bold-16">[ {{ list.day_name }} ] {{ list.hour_start }} - {{ list.hour_end }}</p>
                                                <p class="text-regular-12">Kode Antrian: {{ list.code }}</p>
                                                <p class="text-regular-12">Kuota: {{ list.quota }} | Kuota Booking: {{ list.quota_bo }} | Kuota JKN: {{ list.quota_jkn }}</p>
                                            </div>
                                            <div class="d-flex mt-16">
                                                <b-badge @click="onSelectToEdit(list.id)" variant="success-500" class="px-2 font-weight-normal pointer mr-1">Edit</b-badge>
                                                <b-badge @click="onRemoveSchItem({ id: list.id, day_name: list.day_name, hour_start: list.hour_start, hour_end: list.hour_end })" variant="danger-600" class="px-2 font-weight-normal pointer">Hapus</b-badge>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                                <div class="col-lg-4 border-right custom_scroll">
                                    <div v-for="(list, index) in getDataByDoctorId" :key="list.id" class="card bg-neutral-100" :class="index == 0 ? 'mt-0' : 'mt-12'">
                                        <div class="card-body">
                                            <div>
                                                <p class="text-bold-16">[ {{ list.day_name }} ] {{ list.hour_start }} - {{ list.hour_end }}
                                                    <b-badge @click="onSelectToEdit(list.id)" variant="success-500" class="px-2 font-weight-normal pointer mr-1">Edit</b-badge>
                                                    <!-- <b-badge @click="onRemoveSchItem({ id: list.id, day_name: list.day_name, hour_start: list.hour_start, hour_end: list.hour_end })" variant="danger-600" class="px-2 font-weight-normal pointer"></b-badge> -->
                                                </p>
                                                <!-- <p class="text-regular-12">Kode Antrian: {{ list.code }}</p>
                                                <p class="text-regular-12">Kuota: {{ list.quota }} | Kuota Booking: {{ list.quota_bo }} | Kuota JKN: {{ list.quota_jkn }}</p> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg">
                                    <div v-show="active.editable">
                                        <div class="row mx-0">
                                            <div class="col-lg mt-12 mt-lg-0">
                                                <label class="c-form-label">DATA HFIS</label>
                                                <b-table-simple fixed small bordered responsive  head-variant="light" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="text-center">
                                                    <b-tr>
                                                        <b-td>Dokter</b-td>
                                                        <b-td>Hari</b-td>
                                                        <b-td>Waktu</b-td>
                                                    </b-tr>
                                                    <b-tr v-for="(item, index) in update_schedule_form.vclaim" :key="index" v-if="item.kodedokter === update_schedule_form.bride_bpjs">
                                                        <b-td>{{ item.namahari }}</b-td>
                                                        <b-td>{{ item.jadwal }}</b-td>
                                                        <b-td>{{ item.namadokter }}</b-td>
                                                    </b-tr>
                                                </b-table-simple>
                                            </div>
                                        </div>
                                        <div class="row mx-0">
                                            <div class="col-lg-8 mt-12 mt-lg-0">
                                                <label class="c-form-label">Dokter</label>
                                                <b-form-input v-model="trigger.update_doctor_name" disabled />
                                            </div>
                                            <div class="col-lg-4 mt-12 mt-lg-0">
                                                <label class="c-form-label">Kode Antrian</label>
                                                <b-form-input v-model="update_schedule_form.code" />
                                            </div>
                                            <div class="col-lg-6 mt-12 mt-lg-0">
                                                <label class="c-form-label">Poli</label>
                                                <b-form-input v-model="trigger.update_health_facility_item_name" disabled />
                                            </div>
                                            <div class="col-lg-6 mt-12 mt-lg-0">
                                                <label class="c-form-label">Hari</label>
                                                <v-select v-model="update_schedule_form.day" :options="OptionsDay" :reduce="OptionsDay => OptionsDay.value" label="title" placeholder="-- Pilih Hari --"></v-select>
                                            </div>
                                        </div>
                                        <div class="row mx-0 mt-0 mt-lg-12">
                                            <div class="col-lg mt-12 mt-lg-0">
                                                <label class="c-form-label">Waktu Mulai</label>
                                                <b-form-input type="time" v-model="update_schedule_form.hour_start" />
                                            </div>
                                            <div class="col-lg mt-12 mt-lg-0">
                                                <label class="c-form-label">Waktu Akhir</label>
                                                <b-form-input type="time" v-model="update_schedule_form.hour_end" />
                                            </div>
                                        </div>
                                        <div class="row mx-0 mt-0 mt-lg-12">
                                            <div class="col-lg mt-12 mt-lg-0">
                                                <label class="c-form-label">Kuota</label>
                                                <b-input-group append="Pasien">
                                                    <b-form-input v-model="update_schedule_form.quota" />
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg mt-12 mt-lg-0">
                                                <label class="c-form-label">Kuota Booking</label>
                                                <b-input-group append="Pasien">
                                                    <b-form-input v-model="update_schedule_form.quota_bo" />
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg mt-12 mt-lg-0">
                                                <label class="c-form-label">Kuota JKN</label>
                                                <b-input-group append="Pasien">
                                                    <b-form-input v-model="update_schedule_form.quota_jkn" />
                                                </b-input-group>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end mt-32 px-3">
                                            <b-button @click="CancelCoEdit" variant="outline-orange-500" class="mr-2">Cancel</b-button>
                                            <b-button @click="ResetUpdateScheduleForm" variant="outline-primary" class="mr-2">Reset</b-button>
                                            <!-- <b-button @click.prevent="validationUpdateFormHFIS" variant="primary">Submit HFIS</b-button> -->
                                            <b-button @click.prevent="validationUpdateForm" variant="primary">Submit</b-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </b-modal>
                    </section>
                </section>

                <section v-if="active.tab == 'rest'">
                    <div class="card mt-4">
                        <div class="card-header bg-primary-200 font-weight-bold">Filter Data</div>
                        <div class="card-body d-flex justify-content-center">
                            <div class="col-lg-8">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label class="c-form-label">Nama Dokter</label>
                                        <v-select placeholder="-- Nama Dokter --"></v-select>
                                    </div>
                                    <div class="col-lg-6 mt-12 mt-lg-0">
                                        <label class="c-form-label">Dokter Pengganti</label>
                                        <v-select placeholder="-- Dokter Pengganti --"></v-select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 mt-12">
                                        <label class="c-form-label">Sejak Tanggal</label>
                                        <b-form-input type="date" />
                                    </div>
                                    <div class="col-lg-6 mt-12">
                                        <label class="c-form-label">s.d. Tanggal</label>
                                        <b-form-input type="date" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 mt-12">
                                        <label class="c-form-label">Alasan</label>
                                        <b-form-input />
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap justify-content-lg-end mt-24">
                                    <b-button pill variant="outline-primary" class="mr-2">Batal</b-button>
                                    <b-button pill variant="outline-primary" class="mr-2">Cari</b-button>
                                    <b-button pill variant="primary">Simpan Cuti</b-button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-16 mt-lg-32">
                        <div class="col px-0">
                            <b-table bordered responsive :fields="rest.fields" :items="rest.items" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 mt-lg-20">
                                <template #cell(aksi)>
                                    <b-button size="sm" variant="danger-500">
                                        <feather-icon icon="XCircleIcon" /> Hapus
                                    </b-button>
                                </template>
                            </b-table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        </Layout>
    </div>
</template>
<script>
import topMenu from '@/@core/components/top-menu/topMenu.vue'
import DatePicker from 'v-calendar'
import { mapState } from 'vuex'
import vSelect from 'vue-select'
import ModernLabel from '@/@core/components/label/ModernLabel.vue'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default {
    components: { topMenu, DatePicker, vSelect, ModernLabel },
    data() {
        return {
            days: ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"],
            getDataByDoctorId: [],
            show: false,
            date: '',
            active: {
                tab: 'additional',
                schedule_modal: false,
                editable: false,
                temp_in_modal: {
                    doctor_id: '',
                    code: '',
                    doctor_name: ''
                }
            },
            trigger: {
                day_id: '',
                health_facility_id: '',
                update_day_id: '',
                update_doctor_name: '',
                update_health_facility_item_name: '',
            },
            add_schedule_form: {
                id: '',
                doctor_id: '',
                health_facility_item_id: '',
                code: '',
                doctor_schedule_items: []
            },
            update_schedule_form: {
                id: '',
                doctor_schedule_id: '',
                doctor_id: '',
                code: '',
                health_facility_item_id: '',
                day: '',
                hour_start: '',
                hour_end: '',
                bride_bpjs: '',
                code: '',
                quota: '',
                quota_bo: '',
                quota_jkn: '',
                vclaim: [],
            },
            daily_schedule: {
                fields: [
                    { key: 'doctor', label: 'Nama Dokter' },
                    { key: 'time', label: 'Jam' },
                ],
                items: [
                    { doctor: 'Drg. Teuku Ahmad, Sp. BM', time: 'Tidak ada Jadwal' },
                    { doctor: 'Drg. Tri Julianti, Sp. KGA', time: '13:00 - 15:00'   },
                ]
            },
            selected_box: false,
            head_title: 'Form Jadwal Praktek Dokter Harian',
            schedule: {
                fields: [
                    { key: 'doctor', label: 'Nama Dokter' },
                    { key: 'time', label: 'Jam' },
                ],
                items: [
                    { doctor: 'Drg. Teuku Ahmad, Sp. BM', time: 'Tidak ada Jadwal' },
                    { doctor: 'Drg. Tri Julianti, Sp. KGA', time: '13:00 - 15:00' },
                ]
            },
            rest: {

                fields: [
                    { key: 'no', label: 'No' },
                    { key: 'dokter', label: 'Dokter' },
                    { key: 'pengganti', label: 'Dokter Pengganti' },
                    { key: 'mulai', label: 'Tanggal Mulai' },
                    { key: 'akhir', label: 'Tanggal Akhir' },
                    { key: 'alasan', label: 'Alasan' },
                    { key: 'user', label: 'User' },
                    { key: 'aksi', label: 'Aksi' }
                ],
                items: [
                    {
                        no: '1',
                        dokter: 'Dr. Winda Azwani, Sp A (K)',
                        pengganti: 'Semua Dokter yang satu Departemen dengan Dr. Winda Azwani, Sp A (K)',
                        mulai: '14 Des 2022',
                        akhir: '14 Des 2022',
                        alasan: 'Terinfo Poli Dokter Tidak Prakter',
                        user: 'Rahmadevi Ayu',
                    },
                    {
                        no: '2',
                        dokter: 'Dr. Winda Azwani, Sp A (K)',
                        pengganti: 'Semua Dokter yang satu Departemen dengan Dr. Winda Azwani, Sp A (K)',
                        mulai: '14 Des 2022',
                        akhir: '14 Des 2022',
                        alasan: 'Terinfo Poli Dokter Tidak Prakter',
                        user: 'Rahmadevi Ayu',
                    },
                    {
                        no: '3',
                        dokter: 'Dr. Winda Azwani, Sp A (K)',
                        pengganti: 'Semua Dokter yang satu Departemen dengan Dr. Winda Azwani, Sp A (K)',
                        mulai: '14 Des 2022',
                        akhir: '14 Des 2022',
                        alasan: 'Terinfo Poli Dokter Tidak Prakter',
                        user: 'Rahmadevi Ayu',
                    }
                ],
            },
        }
    },
    watch : {
        'trigger.health_facility_id' (obj) {
            if (obj) {
                return this.$store.dispatch('healthfacilityitem/getParam', obj.value)
            }
        },
        'add_schedule_form.health_facility_item_id' (value) {
            if (value) {
                this.$store.dispatch('helper/getCode', value) 
                this.$store.dispatch('employee/getParam', value)
            }
        },
    },
    methods: {
        onDetailScheduleModal (doctor_id, doctor_name, code){
            
            // empty data
            this.getDataByDoctorId = []
            this.ResetUpdateScheduleForm()
            
            // check if doctor_id and item is not empty
            if (doctor_id && doctor_name) {
                this.active.schedule_modal = true
                this.active.temp_in_modal = {
                    doctor_id: doctor_id,
                    code: code,
                    doctor_name: doctor_name
                }
                this.$store.dispatch('doctorscheduleitem/getByDoctorId', this.active.temp_in_modal.doctor_id)
                .then(response => {
                    this.getDataByDoctorId = response
                    // console.log('getDataByDoctorId: ', this.getDataByDoctorId)
                },
                err=>{
                    console.log("Failed to GET Data by 'doctor_id'")
                })
            } else {
                this.active.schedule_modal = false
                this.active.temp_in_modal = {
                    doctor_id: '',
                    code: '',
                    doctor_name: ''
                }
            }
        },
        onSelectToEdit (id) {
            this.ResetUpdateScheduleForm()
            this.active.editable = true
            
            this.$store.dispatch('doctorscheduleitem/getById', id)
            .then(res => {
                // if response is array, change to be an object, because the data should be an object
                const data = Array.isArray(res) ? res[0] : res
                this.trigger.update_doctor_name = data.doctor_name
                this.trigger.update_health_facility_item_name = data.health_facility_item_name
                this.update_schedule_form.id = data.id
                this.update_schedule_form.code = data.code
                this.update_schedule_form.doctor_schedule_id = data.doctor_schedule_id
                this.update_schedule_form.doctor_id = data.doctor_id
                this.update_schedule_form.bride_bpjs = data.bride_bpjs
                this.update_schedule_form.health_facility_item_id = data.health_facility_item_id
                this.update_schedule_form.day = data.day
                this.update_schedule_form.hour_start = data.hour_start
                this.update_schedule_form.hour_end = data.hour_end
                this.update_schedule_form.quota = data.quota
                this.update_schedule_form.quota_bo = data.quota_bo
                this.update_schedule_form.quota_jkn = data.quota_jkn
                this.update_schedule_form.vclaim = data.vclaim.response
                // console.log('data_id: ', data.vlcaim)
            })
        },
        onRemoveSchItem(obj){
            const userConfirmed = confirm('Apakah anda yakin ingin menghapus ' + obj.day_name + ' ' + obj.hour_start + ' - ' + obj.hour_end + ' ?')
            if (obj.id) {
                if (userConfirmed == true) {
                    this.$store.dispatch('doctorscheduleitem/remove', obj.id)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.$store.dispatch('doctorscheduleitem/getDetail')
                            this.$store.dispatch('doctorschedule/getDetail')
                            this.onDetailScheduleModal (this.active.temp_in_modal.doctor_id, this.active.temp_in_modal.doctor_name, this.active.temp_in_modal.code)
                        },
                        err => {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        }
                    )
                    // console.log('confirm: ', userConfirmed + ' | id: ' + obj.id)
                }
            } else {
                this.notification('Failed','CloseIcon','warning','The data you deleted was not found')
            }
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        onAddSchedule (event) {
            this.trigger.day_id = ''
            this.add_schedule_form.doctor_schedule_items.push({ day:event.value, hour_start: null, hour_end: null, quota: 0, quota_bo: 0, quota_jkn: 0, action: 'action'})
        },
        onDeleteSchedule (index) {
            this.add_schedule_form.doctor_schedule_items.splice(index, 1)
        },
        ResetAddScheduleForm () {
            this.add_schedule_form = {
                doctor_id: '',
                health_facility_item_id: '',
                code: '',
                doctor_schedule_items: []
            }
        },
        validationForm () {
            console.log('clicked');
            this.show = true
            const formData = this.add_schedule_form

            if (formData.doctor_id && formData.health_facility_item_id && formData.doctor_schedule_items.length > 0) {
                // let status = false
                // let arr_status = []
                // for (let i = 0; i < formData.doctor_schedule_items.length; i++) {
                //     let quota = parseInt(formData.doctor_schedule_items[i].quota)
                //     let quota_bo = parseInt(formData.doctor_schedule_items[i].quota_bo)
                //     if (quota_bo >= quota) {
                //         status = true
                //     } else {
                //         status = false
                //     }
                //     arr_status.push(status)
                    
                // }
                // console.log('arr_status: ', arr_status + ' | quota_bo: ', quota_bo + ' | quota: ', quota);
                // if (arr_status.hasOwnProperty(true)) {
                //     this.show = false
                //     this.notification('Failed','CloseIcon','warning','Kuota Booking tidak boleh lebih besar dari Kuota')
                //     return
                // } else {
                    this.$store.dispatch('doctorschedule/formPostPut', formData)
                    .then(res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.$store.dispatch('doctorschedule/getDetail')
                        this.ResetAddScheduleForm()
                        this.show = false
                    }, 
                    err => {
                        this.show = false
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    })
                // }
            } else {
                this.show = false
                this.notification('Failed','CloseIcon','warning','Form cannot be empty')
            }
                        
        },
        ResetUpdateScheduleForm () {
            this.trigger.update_doctor_name = ''
            this.trigger.update_health_facility_item_name = ''
            this.update_schedule_form = {
                id: '',
                doctor_schedule_id: '',
                doctor_id: '',
                health_facility_item_id: '',
                day: '',
                hour_start: '',
                hour_end: '',
                code: '',
                quota: '',
                quota_bo: '',
                quota_jkn: '',
            }
        },
        CancelCoEdit(){
            this.active.editable = false
            this.ResetUpdateScheduleForm()
        },
        validationUpdateForm () {
            this.show = true
            const formData = this.update_schedule_form
            if (formData.id) {
                this.$store.dispatch('doctorscheduleitem/formPostPut', formData)
                .then(res => {
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.$store.dispatch('doctorscheduleitem/getDetail')
                    this.$store.dispatch('doctorschedule/getDetail')
                    this.onDetailScheduleModal (this.active.temp_in_modal.doctor_id, this.active.temp_in_modal.doctor_name, this.active.temp_in_modal.code)
                    this.ResetUpdateScheduleForm()
                    this.$store.dispatch('doctorschedule/getDetail')
                    this.show = false
                }, 
                err => {
                    this.show = false
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                })
            } else {
                this.show = false
                this.notification('Failed','CloseIcon','warning','Form cannot be empty')
            }
        },
        validationUpdateFormHFIS () {
            this.show = true
            const formData = this.update_schedule_form
            if (formData.id) {
                this.$store.dispatch('doctorscheduleitem/formPostPutHFIS', formData)
                .then(res => {
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.$store.dispatch('doctorscheduleitem/getDetail')
                    this.$store.dispatch('doctorschedule/getDetail')
                    this.onDetailScheduleModal (this.active.temp_in_modal.doctor_id, this.active.temp_in_modal.doctor_name, this.active.temp_in_modal.code)
                    this.ResetUpdateScheduleForm()
                    this.show = false
                }, 
                err => {
                    this.show = false
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                })
            } else {
                this.show = false
                this.notification('Failed','CloseIcon','warning','Form cannot be empty')
            }
        },
        goToTab(tab) {
            this.active.tab = tab;

            this.add_schedule_form = {
                id: '',
                doctor_id: '',
                health_facility_item_id: '',
                code: '',
                doctor_schedule_items: []
            }

            if (tab == 'sheet') {
                this.head_title = 'Form Jadwal Praktek Dokter Harian'
            } else if (tab == 'additional') {
                this.head_title = 'Form Tambah Jadwal Praktek Dokter'
            } else if (tab == 'rest') {
                this.head_title = 'Cuti Dokter'
            }
        },
        groupByDoctor(items) {
            const grouped = {};
            items.forEach(item => {
                if (!grouped[item.doctor_id]) {
                    grouped[item.doctor_id] = [];
                }
                grouped[item.doctor_id].push(item);
            });
            return grouped;
        }
    },
    computed: {
        ...mapState({ 
            items: state => state.doctorschedule.items,
            OptionsDay: state => state.helper.options.day,
            OptionsCode: state => state.helper.options.code,
            OptionsHealthFacility: state => state.healthfacility.options.healthfacility,
            OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
            OptionsDoctor: state => state.employee.options.employee,
        }),

        groupedData() {
            const groupedByHealthFacility = {};
            this.items.forEach(item => {
                if (!groupedByHealthFacility[item.health_facility_item_id]) {
                    groupedByHealthFacility[item.health_facility_item_id] = [];
                }
                groupedByHealthFacility[item.health_facility_item_id].push(item);
            });

            const result = {};
            for (const healthFacilityItemId in groupedByHealthFacility) {
                result[healthFacilityItemId] = [];
                const groupedByDoctor = this.groupByDoctor(groupedByHealthFacility[healthFacilityItemId]);
                for (const doctorId in groupedByDoctor) {
                    result[healthFacilityItemId].push(...groupedByDoctor[doctorId]);
                }
            }

            return result;
        }
    },
    created() {
        this.$store.dispatch('helper/getParam', 'day')
        this.$store.dispatch('healthfacility/getParam', 'healthfacility')
        this.$store.dispatch('doctorschedule/getDetail')
    },

}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>
<style>

td label {
    width: 100%;
    cursor: pointer;
}

td label p {
    text-align: center;
}

.custom_scroll {
    overflow-y: scroll; 
    min-height: 100%; 
    max-height: 600px;
}

.custom_scroll::-webkit-scrollbar{
  background: #fff;
  width: 4px;
}
.custom_scroll::-webkit-scrollbar-thumb {
  background-color: #f3d5ff;
  border-radius: 2px;
}

</style>