<template>
    <div>
        <TopAmbulanceMenu selectedMenu="menu1" />
        <div v-if="active_section === 'section1'" class="col mt-32">
            <div>
                <b-button @click="changeSection('section2')" pill variant="outline-indigo" class="px-3 fw-600">
                    <feather-icon icon="PlusIcon" /> Tambah
                </b-button>
            </div>
            <div class="mt-32 mt-lg-32">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="row mx-0">
                        <span class="text-regular-14">Tampilkan</span>
                        <select id="per-page-select" v-model="AmbulanceList.perPage"
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
                <b-table bordered responsive small head-variant="light" :fields="AmbulanceList.fields"
                    :items="filteredItems" :current-page="AmbulanceList.currentPage" :per-page="AmbulanceList.perPage"
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
                            AmbulanceList.totalRows }} entri</span>
                        <div class="ml-auto">
                            <b-pagination v-model="AmbulanceList.currentPage" :total-rows="AmbulanceList.totalRows"
                                :per-page="AmbulanceList.perPage" :current-page="AmbulanceList.currentPage" align="fill"
                                size="sm" class="my-0"></b-pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="col mt-32">
            <div>
                <b-button @click="changeSection('section1')" pill variant="outline-indigo" class="px-3 fw-600">
                    <b-icon icon="chevron-left" scale="0.8" /> Kembali
                </b-button>
            </div>
            <div class="card mt-32">
                <div class="card-header font-weight-bold bg-primary-200">Tambah Data</div>
                <div class="card-body d-flex justify-content-start">
                    <div class="col-lg-5">
                        <div>
                            <label class="c-form-label">No Polisi</label>
                            <b-form-input v-model="form.police_number" />
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Type Ambulance</label>
                            <b-form-input v-model="form.ambulance_type" />
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Status/Kondisi</label>
                            <v-select v-model="form.status" aria-readonly="true" :options="OptionsStatus" label="title" placeholder="-- Status --"></v-select>
                        </div>
                        <div class="d-flex justify-content-end mt-32">
                            <div class="col-lg-5">
                                <div class="row">
                                    <div class="col-6 px-0 px-lg-1">
                                        <b-button pill class="w-100" variant="outline-primary">Reset</b-button>
                                    </div>
                                    <div class="col-6 px-0 px-lg-1">
                                        <b-button pill class="w-100" variant="primary">Tambah</b-button>
                                    </div>
                                </div>
                            </div>
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
            active_section: 'section1',
            searchTerm: '',
            form: {
                police_number: '',
                ambulance_type: '',
                status: '',
            },
            OptionsStatus:[
                { value: 1, title: 'Ready' },
                { value: 2, title: 'Sedang Digunakan' },
                { value: 3, title: 'Maintenance' },
                { value: 4, title: 'Rusak' },
            ],
            AmbulanceList: {
                fields: [
                    { key: 'no', label: 'No' },
                    { key: 'police_number', label: 'No Polisi', thStyle: { minWidth: '140px' } },
                    { key: 'ambulance_type', label: 'Type Ambulance', thStyle: { minWidth: '140px' } },
                    { key: 'status', label: 'Status', thStyle: { minWidth: '140px' } },
                    { key: 'action', label: 'Aksi', thStyle: { minWidth: '140px' } },
                ],
                items: [
                    {
                        no: '1',
                        police_number: 'A 9927 J',
                        ambulance_type: 'Emergency',
                        status: 'Ready',
                    },
                    {
                        no: '2',
                        police_number: 'A 9927 J',
                        ambulance_type: 'Mobil Jenazah',
                        status: 'Ready',
                    },
                    {
                        no: '3',
                        police_number: 'A 9927 J',
                        ambulance_type: 'Emergency',
                        status: 'Ready',
                    },
                    {
                        no: '4',
                        police_number: 'A 9927 J',
                        ambulance_type: 'Full Emergency VVIP',
                        status: 'Ready',
                    },
                    {
                        no: '5',
                        police_number: 'A 9927 J',
                        ambulance_type: 'Emergency',
                        status: 'Ready',
                    },
                    {
                        no: '6',
                        police_number: 'A 9927 J',
                        ambulance_type: 'Emergency',
                        status: 'Ready',
                    },
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },

        }
    },
    mounted() {
        this.AmbulanceList.totalRows = this.AmbulanceList.items.length
    },
    computed: {
        filteredItems() {
            const searchTerm = this.searchTerm.toLowerCase().trim()
            if (!searchTerm) {
                return this.AmbulanceList.items
            }
            return this.AmbulanceList.items.filter(item => {
                return item.police_number.toLowerCase().includes(searchTerm) ||
                    item.ambulance_type.toLowerCase().includes(searchTerm) ||
                    item.status.toLowerCase().includes(searchTerm)
            })
        },
        firstRow() {
            return (this.AmbulanceList.currentPage - 1) * this.AmbulanceList.perPage + 1
        },
        lastRow() {
            return Math.min(this.AmbulanceList.currentPage * this.AmbulanceList.perPage, this.AmbulanceList.totalRows)
        },
    },
    methods: {
        changeSection(section) {
            this.active_section = section
        },
    },
}
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
</style>