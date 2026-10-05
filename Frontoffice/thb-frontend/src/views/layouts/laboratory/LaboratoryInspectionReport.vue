<template>
    <div>
        <TopLaboratoryMenu selectedMenu="menu3" />
        <div class="col mt-32">
            <div class="card">
                <div @click="showFilter = !showFilter" class="card-header d-flex justify-content-between bg-primary-200">
                    <span class="text-bold-18">Filter Data</span>
                    <b-icon v-if="showFilter" icon="chevron-up" />
                    <b-icon v-else icon="chevron-down" />
                </div>
                <div v-if="showFilter" class="card-body">
                    <div class="row">
                        <div class="col-lg-6 px-lg-5">
                            <div>
                                <label class="c-form-label">No Pendaftaran</label>
                                <b-input-group>
                                    <b-form-input type="number"></b-form-input>
                                    <b-input-group-append>
                                        <b-button variant="neutral-200" disabled class="border"><b-icon
                                                icon="search" /></b-button>
                                    </b-input-group-append>
                                </b-input-group>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">No RM</label>
                                <b-input-group>
                                    <b-form-input type="number"></b-form-input>
                                    <b-input-group-append>
                                        <b-button variant="neutral-200" disabled class="border"><b-icon
                                                icon="search" /></b-button>
                                    </b-input-group-append>
                                </b-input-group>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Jenis pasien</label>
                                <b-form-radio-group size="sm">
                                    <b-form-radio value="1">Pasien Lama</b-form-radio>
                                    <b-form-radio value="2">Pasien Baru</b-form-radio>
                                </b-form-radio-group>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Kelamin</label>
                                <b-form-radio-group size="sm">
                                    <b-form-radio value="1">Laki-laki</b-form-radio>
                                    <b-form-radio value="2">Perempuan</b-form-radio>
                                </b-form-radio-group>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Instalasi</label>
                                <b-form-select></b-form-select>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Instalasi Sub</label>
                                <b-form-select></b-form-select>
                            </div>
                        </div>
                        <div class="col-lg-6 px-lg-5">
                            <div class="mt-12 mt-lg-0">
                                <label class="c-form-label">Dokter</label>
                                <b-form-select></b-form-select>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Tanggal Request</label>
                                <div class="row mx-0">
                                    <b-form-datepicker class="col"></b-form-datepicker>
                                    <span class="px-3">-</span>
                                    <b-form-datepicker class="col"></b-form-datepicker>
                                </div>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Cara Masuk</label>
                                <b-form-select></b-form-select>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Cara Bayar</label>
                                <b-form-select></b-form-select>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Status</label>
                                <b-form-select></b-form-select>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Pemerikasaans</label>
                                <b-form-select></b-form-select>
                            </div>
                        </div>
                    </div>
                    <!-- here button -->
                </div>
            </div>
            <div class="mt-32 mt-lg-40">
                <p class="text-bold-24">List Data</p>
                <div class="row mx-0 mt-8">
                    <span class="text-regular-14">Tampilkan</span>
                    <select id="per-page-select" v-model="inspection_report.perPage"
                        class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                        <option value="5" selected>5</option>
                        <option value="10" selected>10</option>
                        <option value="15" selected>15</option>
                    </select>
                    <span class="text-regular-14">Entri</span>
                </div>
                <b-table bordered responsive head-variant="light" hover small :fields="inspection_report.field"
                    :items="inspection_report.item" :current-page="inspection_report.currentPage"
                    @filtered="patientFiltered" :per-page="inspection_report.perPage" tbody-tr-class="text-medium-14"
                    class="mt-12 text-center">
                    <template #cell(registration_code)="data">
                        <div style="min-width: 140px;">
                            <span>{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(payment_method)="data">
                        <div style="min-width: 140px;">
                            <span>{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(entry_method)="data">
                        <div style="min-width: 140px;">
                            <span>{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(no_rm)="data">
                        <div style="min-width: 140px;">
                            <span>{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(patient_name)="data">
                        <div style="min-width: 160px;">
                            <span class="text-uppercase">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(patient_type)="data">
                        <div style="min-width: 140px;">
                            <span>{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(gender)="data">
                        <div style="min-width: 140px;">
                            <span>{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(installation)="data">
                        <div style="min-width: 140px;">
                            <span>{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(doctor)="data">
                        <div style="min-width: 140px;">
                            <span>{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(poli)="data">
                        <div style="min-width: 140px;">
                            <span>{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(inspection)="data">
                        <div style="min-width: 140px;">
                            <span>{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(status)="data">
                        <div style="min-width: 140px;">
                            <span>{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(request_date)="data">
                        <div style="min-width: 140px;">
                            <span>{{ data.value }}</span>
                        </div>
                    </template>
                </b-table>
                <div class="col px-0 mt-20">
                    <div class="d-flex">
                        <span class="text-regular-14">Menampilkan {{ firstRow }} hingga {{ lastRow }} dari
                            {{ inspection_report.totalRows }} entri</span>

                        <div class="ml-auto">
                            <b-pagination v-model="inspection_report.currentPage" :total-rows="inspection_report.totalRows"
                                :per-page="inspection_report.perPage" :current-page="inspection_report.currentPage"
                                align="fill" size="sm" class="my-0"></b-pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import TopLaboratoryMenu from '@/@core/components/top-menu/TopLaboratoryMenu.vue';

export default {
    name: 'LaboratoryListView',
    components: {
        TopLaboratoryMenu,
    },
    data() {
        return {
            showFilter: true,
            inspection_report: {
                field: [
                    { key: 'registration_code', label: 'Kode Pendaftaran' },
                    { key: 'payment_method', label: 'Cara Bayar' },
                    { key: 'entry_method', label: 'Cara Masuk' },
                    { key: 'no_rm', label: 'No RM' },
                    { key: 'patient_name', label: 'Nama Pasien [A/n]' },
                    { key: 'patient_type', label: 'Jenis Pasien' },
                    { key: 'gender', label: 'Kelamin Pasien' },
                    { key: 'installation', label: 'Instalasi Asal' },
                    { key: 'poli', label: 'Poli/Ruangan Asal' },
                    { key: 'doctor', label: 'Dokter' },
                    { key: 'inspection', label: 'Pemeriksaan' },
                    { key: 'status', label: 'Status' },
                    { key: 'request_date', label: 'Tanggal Request' },
                ],
                item: [
                    {
                        registration_code: '-',
                        payment_method: '-',
                        entry_method: '-',
                        no_rm: '-',
                        patient_name: '-',
                        patient_type: '-',
                        gender: '-',
                        installation: '-',
                        poli: '-',
                        doctor: '-',
                        inspection: '-',
                        status: '-',
                        request_date: '-',
                    },
                    {
                        registration_code: '-',
                        payment_method: '-',
                        entry_method: '-',
                        no_rm: '-',
                        patient_name: '-',
                        patient_type: '-',
                        gender: '-',
                        installation: '-',
                        poli: '-',
                        doctor: '-',
                        inspection: '-',
                        status: '-',
                        request_date: '-',
                    },
                    {
                        registration_code: '-',
                        payment_method: '-',
                        entry_method: '-',
                        no_rm: '-',
                        patient_name: '-',
                        patient_type: '-',
                        gender: '-',
                        installation: '-',
                        poli: '-',
                        doctor: '-',
                        inspection: '-',
                        status: '-',
                        request_date: '-',
                    },
                    {
                        registration_code: '-',
                        payment_method: '-',
                        entry_method: '-',
                        no_rm: '-',
                        patient_name: '-',
                        patient_type: '-',
                        gender: '-',
                        installation: '-',
                        poli: '-',
                        doctor: '-',
                        inspection: '-',
                        status: '-',
                        request_date: '-',
                    },
                    {
                        registration_code: '-',
                        payment_method: '-',
                        entry_method: '-',
                        no_rm: '-',
                        patient_name: '-',
                        patient_type: '-',
                        gender: '-',
                        installation: '-',
                        poli: '-',
                        doctor: '-',
                        inspection: '-',
                        status: '-',
                        request_date: '-',
                    },
                    {
                        registration_code: '-',
                        payment_method: '-',
                        entry_method: '-',
                        no_rm: '-',
                        patient_name: '-',
                        patient_type: '-',
                        gender: '-',
                        installation: '-',
                        poli: '-',
                        doctor: '-',
                        inspection: '-',
                        status: '-',
                        request_date: '-',
                    },

                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
        }
    },
    mounted() {
        this.inspection_report.totalRows = this.inspection_report.item.length
    },
    computed: {
        firstRow() {
            return (this.inspection_report.currentPage - 1) * this.inspection_report.perPage + 1
        },
        lastRow() {
            return Math.min(this.inspection_report.currentPage * this.inspection_report.perPage, this.inspection_report.totalRows)
        },
    },
    methods: {
        patientFiltered(patientItems) {
            this.inspection_report.totalRows = patientItems.length
            this.inspection_report.currentPage = 1
        },
    },
}
</script>
<style>
.card-header:hover {
    cursor: pointer;
}</style>