<template>
    <div>
        <section>
            <add_patient :dataFromViewPatientMaster="dataFromViewPatientMaster" />
            <update_patient :dataFromUpdatePatientMaster="dataFromUpdatePatientMaster" />
        </section>
        <section class="mt-24">
            <div>
                <section class="card">
                    <div class="card-header font-weight-bold bg-primary-200"> Filter Data</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <div class="col-lg-10">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div>
                                            <label class="c-form-label">No RM</label>
                                            <b-form-input v-model="params.norm" />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">No Identitas</label>
                                            <b-form-input v-model="params.idcard" />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Nama</label>
                                            <b-form-input v-model="params.name" />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Tempat Lahir</label>
                                            <b-form-input v-model="params.birth_place" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mt-12 mt-lg-0">
                                            <label class="c-form-label">Tanggal Lahir</label>
                                            <b-form-input type="date" v-model="params.birth_date" />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Alamat</label>
                                            <v-select v-model="params.id_province" :options="OptionsProvince" :reduce="OptionsProvince => OptionsProvince" label="title" placeholder="-- Provinsi --"></v-select>
                                            <v-select v-model="params.regency_id" :options="OptionsRegency" :reduce="OptionsRegency => OptionsRegency" label="title" placeholder="-- Kota/Kabupaten --" class="mt-2"></v-select>
                                            <v-select v-model="params.district_id" :options="OptionsDistrict" :reduce="OptionsDistrict => OptionsDistrict" label="title" placeholder="-- Kecamatan --" class="mt-2"></v-select>
                                            <v-select v-model="params.village_id" :options="OptionsVillage" :reduce="OptionsVillage => OptionsVillage" label="title" placeholder="-- Kelurahan --" class="mt-2"></v-select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-24">
                                    <b-button @click.prevent="filterData" :disabled="isDisabledFilter" variant="primary" class="mr-1">
                                        <feather-icon icon="SearchIcon" /> Filter
                                    </b-button>
                                    <b-button @click.prevent="filterReset" :disabled="isDisabledFilter" variant="outline-primary" >
                                        <feather-icon icon="XIcon" /> Clear Filter
                                    </b-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="mt-32">
                    <div class="row mx-0">
                        <div class="text-nowrap mb-md-0">
                            <span class="text-regular-14">Tampilkan</span>
                            <b-form-select v-model="perPage" size="sm" :options="pageOptions" class="mx-2" />
                            <span class="text-regular-14">Entri</span>
                        </div>
                    </div>
                    <b-table bordered responsive hover small head-variant="light" :fields="fields" :items="items" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                        <template #cell(name)="row">
                            {{ row.item.name }} [{{ row.item.alias_name }}]
                        </template>
                        <template #cell(action)="row">
                            <div class="d-flex">
                                <b-button @click="getFromUpdatePatientMaster(row.item)" variant="info-400" size="sm" class="mr-1">Lihat</b-button>

                                <b-button @click="goToDetailPage(row.item.norm)" variant="info-600" size="sm" class="ml-2">Detail</b-button>

                                <!-- <router-link :to="{ path: '/master/patient/detail/'+ row.item.norm}"> 
                                    <b-button variant="info-600" size="sm" class="ml-2">Detail</b-button>
                                </router-link> -->
                                <!-- <b-button variant="info-600" size="sm" class="mr-1">Ubah</b-button> -->
                                <!-- <b-button variant="danger-600" size="sm" class="mr-1">Deactivation</b-button> -->
                            </div>
                        </template>
                    </b-table>
                    <div class="col px-0 mt-20">
                        <div class="d-flex">
                            <span class="text-regular-14">Menampilkan {{ firstRows }} hingga {{ lastRows }} dari {{
                                totalRows }} entri</span>
                            <div class="ml-auto">
                                <b-pagination v-model="currentPage" :total-rows="totalRows" :per-page="perPage" align="fill"
                                    size="sm" class="my-0"></b-pagination>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </section>

    </div>
</template>
<script>
import add_patient from '@/views/layouts/_registration/regisPatient/modal/add_patient.vue'
import update_patient from '@/views/layouts/_registration/regisPatient/modal/update_patient.vue'
import { mapState } from 'vuex'
import vSelect from 'vue-select'
import router from '@/router/index';

export default {
    name: 'RoomMaster',
    components: {
        update_patient, add_patient, vSelect
    },
    data() {
        return {
            dataFromViewPatientMaster: '',
            dataFromUpdatePatientMaster: '',
            active: {
                modal_add_patient: false,
            },
            params: {
                offset: 0,
                limit: 20,
                norm: '',
                idcard: '',
                name: '',
                birth_place: '',
                birth_date: '',
                id_province: '',
                district_id: '',
                regency_id: '',
                village_id: '',
            },
            currentPage: 1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            isDisabled: false,
            isDisabledFilter: false,
            show: false,
            dir: false,
            // sortBy: '',
            // sortDesc: false,
            // sortDirection: 'asc',
            // filter: null,
            // filterOn: [],
            // status: [{
            //     0: 'Active', 1: 'Deleted'
            // },
            // {
            //     0: 'primary', 1: 'danger'
            // }],
        }
    },
    watch: {
        // 'items': function( val) {
        //     console.log(val)
        // },
        currentPage: {
            handler: function (value) {
                console.log(value)
                this.firstRows = this.perPage * (value - 1) + 1
                this.lastRows = this.perPage * (value) + 1
                this.params.offset = this.perPage * (value - 1)
                this.params.limit = this.perPage
                this.$store.dispatch('patient/getAll', this.params)
            }
        },
        perPage: {
            handler: function (value) {
                this.params.limit = this.perPage
                this.$store.dispatch('patient/getAll', this.params)
            }
        },

        'params.id_province'(obj) {
            return this.$store.dispatch('helper/getParamRegency', obj.value)
        },
        'params.regency_id'(obj) {
            return this.$store.dispatch('helper/getParamDistrict', obj.value)
        },
        'params.district_id'(obj) {
            return this.$store.dispatch('helper/getParamVillage', obj.value)
        },
    },
    computed: {
        ...mapState({
            items: state => state.patient.items,
            fields: state => state.patient.patientMaster.fields,
            pageOptions: state => state.patient.pageOptions,
            totalRows: state => state.patient.totalRows,
            OptionsProvince: state => state.helper.options.province,
            OptionsRegency: state => state.helper.options.regency,
            OptionsDistrict: state => state.helper.options.district,
            OptionsVillage: state => state.helper.options.village,
        }),
        sortOptions() {
            // Create an options list from our fields
            return this.fields.filter(f => f.sortable).map(f => ({ text: f.label, value: f.key }))
        },
        dateFormat() {
            return create_at => this.$options.filters.formatDate(create_at)
        },
        direction() {
            if (store.state.appConfig.isRTL) {
                this.dir = true
                return this.dir
            }
            this.dir = false
            return this.dir
        },
    },

    methods: {
        goToDetailPage(norm){
            const obj = {
                norm: norm
            }
            // console.log('onjd :', obj);
            if (obj && obj.norm) {
                localStorage.setItem('obj_norm', JSON.stringify(obj))
                this.$router.push({ path: `/master/patient/detail/` })
            }
        },
        getFromUpdatePatientMaster(data) {
            this.$emit('getFromUpdatePatientMaster', data)
            this.dataFromUpdatePatientMaster = data
        },
        resetForm() {
            this.params.norm = ''
            this.params.idcard = ''
            this.params.name = ''
            this.params.regency_id = ''
            this.params.district_id = ''
            this.params.birth_place = ''
            this.params.birth_date = ''
            this.params.id_province = ''
            this.params.village_id = ''
        },
        filterData() {
            this.show = true;
            this.isDisabledFilter = true;

            this.$store.dispatch('patient/getAll', this.params)
                .then(() => {
                    this.show = false;
                    this.isDisabledFilter = false;
                })
                .catch(error => {
                    console.error(error);
                    this.show = false;
                    this.isDisabledFilter = false;
                });
        },

        filterReset() {
            this.params.norm = ''
            this.params.idcard = ''
            this.params.name = ''
            this.params.birth_place = ''
            this.params.birth_date = ''
            this.params.id_province = ''
            this.params.district_id = ''
            this.params.regency_id = ''
            this.params.village_id = ''
            this.params.offset = 0
            this.params.limit = this.perPage
            this.$store.dispatch('patient/getAll', this.params)
        },
    },

    mounted() {
        this.$store.state.totalRows = this.items.length
        this.show = true
    },

    created() {
        this.$store.dispatch('patient/getAll', this.params)
        this.$store.dispatch('helper/getParamProvince', 'province')
        this.show = true
    },
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>