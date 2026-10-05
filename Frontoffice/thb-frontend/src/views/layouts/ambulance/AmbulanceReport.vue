<template>
    <div>
        <TopAmbulanceMenu selectedMenu="menu5" />
        <div class="col mt-32">
            <div class="card">
                <div class="card-header bg-primary-200 font-weight-bold">Filter Data</div>
                <div class="card-body d-flex justify-content-center">
                    <div class="col-lg-6">
                        <div>
                            <label class="c-form-label">Nama Pasien</label>
                            <b-form-input v-model="filter_form.name" />
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Tujuan</label>
                            <b-form-input v-model="filter_form.goals" />
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">No Polisi</label>
                            <b-form-input v-model="filter_form.police_number" />
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Filter Tanggal</label>
                            <div class="row">
                                <div class="col-lg">
                                    <b-form-input type="date" v-model="filter_form.start_date" />
                                </div>
                                <span class="mx-3 text-center">s/d</span>
                                <div class="col-lg">
                                    <b-form-input type="date" v-model="filter_form.end_date" />
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-24">
                            <div class="col-lg-7">
                                <div class="row">
                                    <div class="col-6 col-lg px-1">
                                        <b-button pill class="w-100" variant="outline-indigo">Bersihkan</b-button>
                                    </div>
                                    <div class="col-6 col-lg px-1">
                                        <router-link :to="{ name: 'AmbulanceReportPrintView' }" target="_blank">
                                            <b-button pill class="w-100" variant="outline-indigo">
                                                <feather-icon icon="PrinterIcon" class="mr-2" />Print
                                            </b-button>
                                        </router-link>
                                    </div>
                                    <div class="col-lg px-1 py-2 py-lg-0">
                                        <b-button pill class="w-100" variant="indigo">Cari</b-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-32 mt-lg-40">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="row mx-0">
                        <span class="text-regular-14">Tampilkan</span>
                        <select id="per-page-select" v-model="AmbulanceReport.perPage"
                            class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                            <option value="5" selected>5</option>
                            <option value="10" selected>10</option>
                            <option value="15" selected>15</option>
                        </select>
                        <span class="text-regular-14">Entri</span>
                    </div>
                    <div class="col-6 col-lg-3 d-flex align-items-center position-relative">
                        <b-form-input size="sm" autocomplete="off" placeholder="Search" v-model="searchTerm"
                            class="rounded-pill pl-5"></b-form-input>
                        <b-icon icon="search" class="position-absolute pl-3 text-neutral-400" />
                    </div>
                </div>
                <b-table bordered responsive head-variant="light" :fields="AmbulanceReport.fields"
                    :items="filteredItems" :current-page="AmbulanceReport.currentPage" :per-page="AmbulanceReport.perPage"
                    thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                    <template #thead-top="data">
                        <b-tr>
                            <b-th colspan="8"><span></span></b-th>
                            <b-th colspan="2">Petugas</b-th>
                            <b-th><span></span></b-th>
                        </b-tr>
                    </template>
                    <template #cell(police_number)="data">
                        <div>
                            <span class="text-medium-14 text-uppercase">{{ data.value }}</span>
                        </div>
                    </template>
                </b-table>
                <div class="col px-0 mt-20">
                    <div class="d-flex">
                        <span class="text-regular-14">Menampilkan {{ firstRow }} hingga {{ lastRow }} dari {{
                            AmbulanceReport.totalRows }} entri</span>
                        <div class="ml-auto">
                            <b-pagination v-model="AmbulanceReport.currentPage" :total-rows="AmbulanceReport.totalRows"
                                :per-page="AmbulanceReport.perPage" :current-page="AmbulanceReport.currentPage" align="fill"
                                size="sm" class="my-0"></b-pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import TopAmbulanceMenu from '@/@core/components/top-menu/TopAmbulanceMenu.vue';

export default {
    components: {
        TopAmbulanceMenu
    },
    data() {
        return {
            searchTerm: '',
            filter_form: {
                name: '',
                goal: '',
                police_number: '',
                start_date: '',
                end_date: '',
            },
            AmbulanceReport: {
                fields: [
                    { key: 'no', label: 'No' },
                    { key: 'start_date', label: 'Tanggal Mulai', thStyle: { minWidth: '140px' } },
                    { key: 'end_date', label: 'Tanggal Selesai', thStyle: { minWidth: '140px' } },
                    { key: 'patient', label: 'Nama Pasien', thStyle: { minWidth: '140px' } },
                    { key: 'goal', label: 'Tujuan', thStyle: { minWidth: '140px' } },
                    { key: 'transportation', label: 'Kendaraan', thStyle: { minWidth: '140px' } },
                    { key: 'police_number', label: 'No Polisi', thStyle: { minWidth: '140px' } },
                    { key: 'status', label: 'Status', thStyle: { minWidth: '140px' } },
                    { key: 'nurse', label: 'Perawat', thStyle: { minWidth: '140px' } },
                    { key: 'driver', label: 'Driver', thStyle: { minWidth: '140px' } },
                    { key: 'total', label: 'Total', thStyle: { minWidth: '140px' } },
                ],
                items: [
                    { no: '1', start_date: 'a', end_date: '-', patient: '-', goal: '-', transportation: '-', police_number: '-', status: '-', nurse: '-', driver: '-', total: '-', },
                    { no: '2', start_date: '-', end_date: 'b', patient: '-', goal: '-', transportation: '-', police_number: '-', status: '-', nurse: '-', driver: '-', total: '-', },
                    { no: '3', start_date: '-', end_date: '-', patient: 'c', goal: '-', transportation: '-', police_number: '-', status: '-', nurse: '-', driver: '-', total: '-', },
                    { no: '4', start_date: '-', end_date: '-', patient: '-', goal: 'd', transportation: '-', police_number: '-', status: '-', nurse: '-', driver: '-', total: '-', },
                    { no: '5', start_date: '-', end_date: '-', patient: '-', goal: '-', transportation: 'e', police_number: '-', status: '-', nurse: '-', driver: '-', total: '-', },
                    { no: '6', start_date: '-', end_date: '-', patient: '-', goal: '-', transportation: '-', police_number: 'f', status: '-', nurse: '-', driver: '-', total: '-', },

                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },

        }
    },
    mounted() {
        this.AmbulanceReport.totalRows = this.AmbulanceReport.items.length
    },
    computed: {
        filteredItems() {
            const searchTerm = this.searchTerm.toLowerCase().trim()
            if (!searchTerm) {
                return this.AmbulanceReport.items
            }
            return this.AmbulanceReport.items.filter(item => {
                return item.start_date.toLowerCase().includes(searchTerm) ||
                    item.end_date.toLowerCase().includes(searchTerm) ||
                    item.patient.toLowerCase().includes(searchTerm) ||
                    item.goal.toLowerCase().includes(searchTerm) ||
                    item.transportation.toLowerCase().includes(searchTerm) ||
                    item.police_number.toLowerCase().includes(searchTerm) ||
                    item.status.toLowerCase().includes(searchTerm) ||
                    item.nurse.toLowerCase().includes(searchTerm) ||
                    item.driver.toLowerCase().includes(searchTerm) ||
                    item.total.toLowerCase().includes(searchTerm)
            })
        },
        firstRow() {
            return (this.AmbulanceReport.currentPage - 1) * this.AmbulanceReport.perPage + 1
        },
        lastRow() {
            return Math.min(this.AmbulanceReport.currentPage * this.AmbulanceReport.perPage, this.AmbulanceReport.totalRows)
        },
    },
}
</script>
