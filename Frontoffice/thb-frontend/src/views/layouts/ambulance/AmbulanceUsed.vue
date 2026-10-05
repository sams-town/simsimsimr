<template>
    <div>
        <TopAmbulanceMenu selectedMenu="menu3" />
        <div class="col mt-32">
            <div class="card">
                <div class="card-header font-weight-bold bg-primary-200">Filter Data</div>
                <div class="card-body d-flex justify-content-center">
                    <div class="col-lg-5">
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
                        <div class="d-flex justify-content-end mt-32">
                            <div class="col-lg-5">
                                <div class="row">
                                    <div class="col-6 px-0 px-lg-1">
                                        <b-button pill class="w-100" variant="outline-primary">Bersihkan</b-button>
                                    </div>
                                    <div class="col-6 px-0 px-lg-1">
                                        <b-button pill class="w-100" variant="primary">Cari</b-button>
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
                        <select id="per-page-select" v-model="AmbulanceUsed.perPage"
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
                <b-table bordered responsive hover small head-variant="light" :fields="AmbulanceUsed.fields"
                    :items="filteredItems" :current-page="AmbulanceUsed.currentPage" :per-page="AmbulanceUsed.perPage"
                    thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                    <template #cell(police_number)="data">
                        <div>
                            <span class="text-uppercase">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(action)>
                        <div class="d-flex justify-content-lg-center">
                            <b-button size="sm" variant="warning-500" class="mr-1">
                                <feather-icon icon="EditIcon" />
                            </b-button>
                            <b-button size="sm" variant="danger-500">
                                <feather-icon icon="TrashIcon" />
                            </b-button>
                        </div>
                    </template>
                </b-table>
                <div class="col px-0 mt-20">
                    <div class="d-flex">
                        <span class="text-regular-14">Menampilkan {{ firstRow }} hingga {{ lastRow }} dari {{
                            AmbulanceUsed.totalRows }} entri</span>
                        <div class="ml-auto">
                            <b-pagination v-model="AmbulanceUsed.currentPage" :total-rows="AmbulanceUsed.totalRows"
                                :per-page="AmbulanceUsed.perPage" :current-page="AmbulanceUsed.currentPage" align="fill"
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
import vSelect from 'vue-select'

export default {
    components: {
        TopAmbulanceMenu, vSelect
    },
    data() {
        return {
            searchTerm: '',
            filter_form: {
                name: '',
                goals: '',
                police_number: '',
            },
            AmbulanceUsed: {
                fields: [
                    { key: 'no', label: 'No', },
                    { key: 'patient', label: 'Pasien', thStyle: { minWidth: '140px' } },
                    { key: 'driver', label: 'Sopir', thStyle: { minWidth: '140px' } },
                    { key: 'companion', label: 'Pendamping', thStyle: { minWidth: '140px' } },
                    { key: 'goal', label: 'Tujuan', thStyle: { minWidth: '140px' } },
                    { key: 'transportation', label: 'Kendaraan', thStyle: { minWidth: '140px' } },
                    { key: 'police_number', label: 'No Polisi', thStyle: { minWidth: '140px' } },
                    { key: 'start_date', label: 'Tanggal Mulai', thStyle: { minWidth: '140px' } },
                    { key: 'action', label: 'Aksi', thStyle: { minWidth: '100px' } },
                ],
                items: [
                    { no: '1', patient: 'a', driver: '-', companion: '-', goal: '-', transportation: '-', police_number: '-', start_date: '-', },
                    { no: '2', patient: '-', driver: 'b', companion: '-', goal: '-', transportation: '-', police_number: '-', start_date: '-', },
                    { no: '3', patient: '-', driver: '-', companion: 'c', goal: '-', transportation: '-', police_number: '-', start_date: '-', },
                    { no: '4', patient: '-', driver: '-', companion: '-', goal: 'd', transportation: '-', police_number: '-', start_date: '-', },
                    { no: '5', patient: '-', driver: '-', companion: '-', goal: '-', transportation: 'e', police_number: '-', start_date: '-', },
                    { no: '6', patient: '-', driver: '-', companion: '-', goal: '-', transportation: '-', police_number: 'f', start_date: 'g', },
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },

        }
    },
    mounted() {
        this.AmbulanceUsed.totalRows = this.AmbulanceUsed.items.length
    },
    computed: {
        filteredItems() {
            const searchTerm = this.searchTerm.toLowerCase().trim()
            if (!searchTerm) {
                return this.AmbulanceUsed.items
            }
            return this.AmbulanceUsed.items.filter(item => {
                return item.patient.toLowerCase().includes(searchTerm) ||
                    item.driver.toLowerCase().includes(searchTerm) ||
                    item.companion.toLowerCase().includes(searchTerm) ||
                    item.goal.toLowerCase().includes(searchTerm) ||
                    item.transportation.toLowerCase().includes(searchTerm) ||
                    item.police_number.toLowerCase().includes(searchTerm) ||
                    item.start_date.toLowerCase().includes(searchTerm)
            })
        },
        firstRow() {
            return (this.AmbulanceUsed.currentPage - 1) * this.AmbulanceUsed.perPage + 1
        },
        lastRow() {
            return Math.min(this.AmbulanceUsed.currentPage * this.AmbulanceUsed.perPage, this.AmbulanceUsed.totalRows)
        },
    },
}
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
</style>