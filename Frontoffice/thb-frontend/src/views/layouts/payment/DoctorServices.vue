<template>
    <div>
        <TopPaymentMenu selectedMenu="menu6" class="mt-2" />
        <div class="mt-32">
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
                                <label class="c-form-label">Instalasi</label>
                                <b-form-input></b-form-input>
                            </div>
                        </div>
                        <div class="col-lg-6 px-lg-5">
                            <div>
                                <label class="c-form-label">Tanggal Pendaftaran</label>
                                <div class="row mx-0">
                                    <b-form-datepicker class="col"></b-form-datepicker>
                                    <span class="px-3">-</span>
                                    <b-form-datepicker class="col"></b-form-datepicker>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-lg-end mt-40">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-6 col-lg px-1 my-1">
                                    <b-button pill class="btn-block" variant="outline-primary">
                                        Search <b-icon icon="search" scale="0.7" />
                                    </b-button>
                                </div>
                                <div class="col-6 col-lg px-1 my-1">
                                    <b-button pill class="btn-block" variant="outline-primary">Cancel</b-button>
                                </div>
                                <div class="col-6 col-lg px-1 my-1">
                                    <b-button pill class="btn-block" variant="outline-primary">Export Excel</b-button>
                                </div>
                                <router-link :to="{ name: 'DoctorServicesReportView' }">
                                    <div class="col-6 col-lg px-1 my-1">
                                        <b-button pill class="btn-block" variant="primary">Cetak Laporan</b-button>
                                    </div>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-32 mt-lg-40">
                <p class="text-bold-24">List Data</p>
                <div class="row mx-0 mt-8">
                    <span class="text-regular-14">Tampilkan</span>
                    <select id="per-page-select" v-model="patient.perPage"
                        class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                        <option value="5" selected>5</option>
                        <option value="10" selected>10</option>
                        <option value="15" selected>15</option>
                    </select>
                    <span class="text-regular-14">Entri</span>
                </div>
                <b-table bordered responsive hover small head-variant="light" :fields="patient.field" :items="patient.item"
                    :current-page="patient.currentPage" @filtered="patientFiltered" :per-page="patient.perPage"
                    class="mt-12 text-center">
                    <template #cell(instalation)="data">
                        <div style="min-width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(poli)="data">
                        <div style="min-width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(doctor)="data">
                        <div style="min-width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(patient)="data">
                        <div style="min-width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(service)="data">
                        <div style="min-width: 160px;">
                            <span class="text-medium-14 text-uppercase">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(price)="data">
                        <div style="min-width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(date)="data">
                        <div style="min-width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                </b-table>
                <div class="col px-0 mt-20">
                    <div class="d-flex">
                        <span class="text-regular-14">Menampilkan 1 hingga 10 dari 200 entri</span>
                        <div class="ml-auto">
                            <b-pagination v-model="patient.currentPage" :total-rows="patient.totalRows"
                                :per-page="patient.perPage" :current-page="patient.currentPage" align="fill" size="sm"
                                class="my-0"></b-pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import TopPaymentMenu from '@/@core/components/top-menu/TopPaymentMenu.vue';

export default {
    name: 'PatientPayment',
    components: {
        TopPaymentMenu
    },
    data() {
        return {
            showFilter: true,
            patient: {
                field: [
                    { key: 'instalation', label: 'Instalasi' },
                    { key: 'poli', label: 'Poli/Ruangan' },
                    { key: 'doctor', label: 'Dokter' },
                    { key: 'patient', label: 'Pasien' },
                    { key: 'service', label: 'Jasa' },
                    { key: 'price', label: 'Harga' },
                    { key: 'date', label: 'Tanggal' },
                ],
                item: [
                    {
                        instalation: 'Instalasi Rawat Jalan',
                        poli: 'Poli Nyeri',
                        doctor: 'SIMRS Adm',
                        patient: 'Rio Riswanto [Tn]',
                        service: 'Jasa Konsultasi Spesialis Mata',
                        price: '250.000',
                        date: '07-02-2023 08:40:08',
                    },
                    {
                        instalation: 'Instalasi Rawat Jalan',
                        poli: 'Poli Nyeri',
                        doctor: 'SIMRS Adm',
                        patient: 'Rio Riswanto [Tn]',
                        service: 'Jasa Konsultasi Spesialis Mata',
                        price: '250.000',
                        date: '07-02-2023 08:40:08',
                    },
                    {
                        instalation: 'Instalasi Rawat Jalan',
                        poli: 'Poli Nyeri',
                        doctor: 'SIMRS Adm',
                        patient: 'Rio Riswanto [Tn]',
                        service: 'Jasa Konsultasi Spesialis Mata',
                        price: '250.000',
                        date: '07-02-2023 08:40:08',
                    },
                    {
                        instalation: 'Instalasi Rawat Jalan',
                        poli: 'Poli Nyeri',
                        doctor: 'SIMRS Adm',
                        patient: 'Rio Riswanto [Tn]',
                        service: 'Jasa Konsultasi Spesialis Mata',
                        price: '250.000',
                        date: '07-02-2023 08:40:08',
                    },
                    {
                        instalation: 'Instalasi Rawat Jalan',
                        poli: 'Poli Nyeri',
                        doctor: 'SIMRS Adm',
                        patient: 'Rio Riswanto [Tn]',
                        service: 'Jasa Konsultasi Spesialis Mata',
                        price: '250.000',
                        date: '07-02-2023 08:40:08',
                    },
                    {
                        instalation: 'Instalasi Rawat Jalan',
                        poli: 'Poli Nyeri',
                        doctor: 'SIMRS Adm',
                        patient: 'Rio Riswanto [Tn]',
                        service: 'Jasa Konsultasi Spesialis Mata',
                        price: '250.000',
                        date: '07-02-2023 08:40:08',
                    },

                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
        }
    },
    mounted() {
        this.patient.totalRows = this.patient.item.length
    },
    methods: {
        patientFiltered(patientItems) {
            this.patient.totalRows = patientItems.length
            this.patient.currentPage = 1
        },
    },
}
</script>
<style scoped>
/* @import '../../../@core/css/main/main.min.css'; */
.card-header:hover {
    cursor: pointer;
}
</style>
