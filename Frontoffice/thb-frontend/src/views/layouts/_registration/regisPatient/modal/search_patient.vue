<template>
    <div class="col px-0 d-flex align-self-stretch">
        <b-button @click="onSearchPatient()" size="sm" variant="pink-500" class="col text-white">
            <b-icon icon="search" scale="0.8"></b-icon>
            <span> Cari Pasien</span>
        </b-button>

        <b-modal @close="onSearchPatient()" v-model="modalSearchPatientShow" size="lg" title="Cari Pasien" hide-footer>
            <div class="card">
                <div class="card-header bg-indigo">
                    <p class="my-0 fw-600 c-text-16-18 text-light">Pencarian Pasien</p>
                </div>
                <div class="card-body">
                    <section>
                        <div class="col px-0">
                            <div class="row mx-0">
                                <div class="col">
                                    <label class="c-form-label">No RM</label>
                                    <b-form-input v-model="params.norm" autocomplete="off"></b-form-input>
                                </div>
                                <div class="col">
                                    <label class="c-form-label">No Peserta BPJS(Data Pasien Tidak ada)</label>
                                    <b-form-input v-model="params.noka" autocomplete="off"></b-form-input>
                                </div>
                                <div class="col mt-12 mt-lg-0">
                                    <label class="c-form-label">Nama Pasien</label>
                                    <b-form-input v-model="params.name" autocomplete="off"></b-form-input>
                                </div>
                            </div>
                            <div class="row mx-0">
                                <div class="col mt-12">
                                    <label class="c-form-label">No Identitas</label>
                                    <b-form-input v-model="params.idcard" autocomplete="off" type="number"></b-form-input>
                                </div>
                                <div class="col mt-12">
                                    <label class="c-form-label">Tempat, tanggal lahir</label>
                                    <div class="row mx-1">
                                        <b-form-input v-model="params.birth_place" autocomplete="off" class="col mr-1"></b-form-input>
                                        <b-form-input v-model="params.birth_date" autocomplete="off" type="date" class="col"></b-form-input>
                                    </div>
                                </div>
                            </div>
                            <div class="col mt-12" style="display: none;">
                                <label class="c-form-label">Alamat</label>
                                <b-form-input v-model="params.address" autocomplete="off" class="col-8"></b-form-input>
                            </div>
                            <div class="col mt-3 d-flex justify-content-end">
                                <b-button type="submit" variant="primary" class="mr-1" :disabled="isDisabledFilter" @click.prevent="filterData"> 
                                    <feather-icon icon="SearchIcon"/> Filter
                                </b-button>
                                <b-button variant="outline-primary" @click.prevent="filterReset">
                                    <feather-icon icon="XIcon"/> Clear Filter
                                </b-button>
                            </div>
                        </div>
                    </section>
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
                    <div class="col px-0 mt-3">
                        <b-table small hover responsive head-variant="light" :items="items" :fields="fields" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                            <template #cell(action)="row">
                                <div class="d-flex">
                                    <b-button size="sm" @click="getPatientOnReg(row.item)" variant="info" class="mr-1">Daftarkan</b-button>
                                    <b-button size="sm" @click="getPatientOnAppointment(row.item)" variant="success-500">Appointment</b-button>
                                </div>
                            </template>
                        </b-table>
                    </div>
                    <div class="col px-0 mt-20">
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
            </div>
        </b-modal>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import vSelect from 'vue-select'
export default {
    components: {
        vSelect
    },
    props: {
        dataTriggerSearch: {},
    },
    data() {
        return {
            params: {
                offset: 0,
                limit: 10,
                norm: '',
                name: '',
                idcard: '',
                birth_date: '',
                birth_place: '',
                address: '',
                noka: '',
                // province_id: '',
                // regency_id: '',
                // district_id: '',
                // village_id: '',
                // zip_code: '',
            },
            currentPage: 1,
            perPage: 10,
            firstRows: 1,
            lastRows: 10,
            isDisabled: false,
            isDisabledFilter: false,
            show: false,
            modalSearchPatientShow: false,
            dir: false,
            sortBy: '',
            sortDesc: false,
            sortDirection: 'asc',
            filter: null,
            filterOn: []
        }
    },
    watch: {
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.$store.dispatch('patient/getAll',this.params)
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.$store.dispatch('patient/getAll',this.params)
            }
        },
        dataTriggerSearch(value){
            if (value) {
                this.modalSearchPatientShow = true
            }
        }
    },
    computed: {
        ...mapState({ 
            totalRows: state => state.patient.totalRows, 
            fields: state => state.patient.fields, 
            items: state => state.patient.items, 
            pageOptions: state => state.patient.pageOptions, 
        }),
        sortOptions() {
            // Create an options list from our fields
            return this.fields.filter(f => f.sortable).map(f => ({ text: f.label, value: f.key }))
        },
        dateFormat () {
            return create_at => this.$options.filters.formatDate(create_at)
        },
        direction () {
            if (store.state.appConfig.isRTL) {
                this.dir = true
                return this.dir
            }
            this.dir = false
            return this.dir
        }
    },
    methods: {
        onSearchPatient () {
            this.modalSearchPatientShow = !this.modalSearchPatientShow
        },
        getPatientOnReg (data) {
            this.modalSearchPatientShow = !this.modalSearchPatientShow
            this.$emit('getPatientOnReg', data)
            // console.log('getPatientOnReg',data);
        },
        getPatientOnAppointment (data) {
            this.modalSearchPatientShow = !this.modalSearchPatientShow
            this.$emit('getPatientOnAppointment', data)
            // console.log('getPatientOnAppointment',data);
        },
        filterData () {
            this.show = true
            this.isDisabledFilter = true
            console.log(this.isDisabledFilter)
            if (this.params.noka) {
                this.$store.dispatch('patient/getBackdateNoka', {id:this.params.noka,mr:this.params.norm})
                .then(res => {
                    console.log(res)
                    this.params.norm = res
                    this.$store.dispatch('patient/getAll', this.params)
                    .then(res => {
                        this.show = false
                        this.isDisabledFilter = false
                    },
                    err => {
                        this.show = false
                        this.isDisabledFilter = false
                    })
                })
            } else {
                this.$store.dispatch('patient/getAll', this.params)
                .then(res => {
                    this.show = false
                    this.isDisabledFilter = false
                },
                err => {
                    this.show = false
                    this.isDisabledFilter = false
                })
            }
        },
        filterReset () {
            this.params.offset = 0
            this.params.limit = this.perPage
            this.params.norm = ''
            this.params.name = ''
            this.params.idcard = ''
            this.params.birth_date = ''
            this.params.birth_place = ''
            this.params.address = ''
            this.params.noka = ''
            // this.params.province_id = '',
            // this.params.regency_id = '',
            // this.params.district_id = '',
            // this.params.village_id = '',
            // this.params.zip_code = '',
        }
    },
    mounted() {
        this.$store.state.totalRows = this.items.length
    },
    created () {
        // this.$store.dispatch('patient/getAll', this.params)
        // this.$store.dispatch('helper/getParamProvince', 'province')
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>