<template>
    <div>
        <section>
            <add_patient :dataFromViewPatientMaster="dataFromViewPatientMaster" />
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
                                            <b-form-input v-model="FilterForm.rm_number" />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">No Identitas</label>
                                            <b-form-input v-model="FilterForm.idntity_number" />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Nama</label>
                                            <b-form-input v-model="FilterForm.name" />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Tempat Lahir</label>
                                            <b-form-input v-model="FilterForm.birth_place" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mt-12 mt-lg-0">
                                            <label class="c-form-label">Tanggal Lahir</label>
                                            <b-form-input type="date" v-model="FilterForm.birth_date" />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Alamat</label>
                                            <v-select v-model="FilterForm.province" :options="OptionsProvince"
                                                :reduce="OptionsProvince => OptionsProvince" label="title"
                                                placeholder="-- Provinsi --"></v-select>
                                            <v-select v-model="FilterForm.regency" :options="OptionsRegency"
                                                :reduce="OptionsRegency => OptionsRegency" label="title"
                                                placeholder="-- Kota/Kabupaten --" class="mt-2"></v-select>
                                            <v-select v-model="FilterForm.district" :options="OptionsDistrict"
                                                :reduce="OptionsDistrict => OptionsDistrict" label="title"
                                                placeholder="-- Kecamatan --" class="mt-2"></v-select>
                                            <v-select v-model="FilterForm.village" :options="OptionsVillage"
                                                :reduce="OptionsVillage => OptionsVillage" label="title"
                                                placeholder="-- Kelurahan --" class="mt-2"></v-select>
                                            <!-- <div class="row mt-2">
                                                <div class="col-lg-8 mb-2 mb-lg-0">
                                                    <b-form-input placeholder="Nama jalan"
                                                        v-model="FilterForm.street_name" />
                                                </div>
                                                <div class="col">
                                                    <b-form-input placeholder="RT" v-model="FilterForm.rt" />
                                                </div>
                                                <div class="col">
                                                    <b-form-input placeholder="RW" v-model="FilterForm.rw" />
                                                </div>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-24">
                                    <b-button type="submit" variant="primary" class="mr-1" :disabled="isDisabledFilter"
                                        @click.prevent="filterData">
                                        <feather-icon icon="SearchIcon" /> Filter
                                    </b-button>
                                    <b-button variant="outline-primary">
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
                    <b-table bordered responsive hover small head-variant="light" :fields="fields" :items="items"
                        thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                        <template #cell(name)="row">
                            {{ row.item.name }} [{{ row.item.alias_name }}]
                        </template>
                        <template #cell(action)="row">
                            <div class="d-flex">
                                <b-button @click="getFromViewPatientMaster(row.item)" variant="info-400" size="sm"
                                    class="mr-1">Lihat</b-button>
                                <b-button variant="info-600" size="sm" class="mr-1">Ubah</b-button>
                                <router-link :to="{ path: '/prints/card/' + row.item.norm }" target="_blank">
                                    <b-button variant="neutral-100" size="sm" class="mr-1">Kartu</b-button>
                                </router-link>
                                <!-- <b-button variant="danger-600" size="sm" class="mr-1">Deactivation</b-button> -->
                            </div>
                        </template>
                    </b-table>
                    <div class="col px-0 mt-20">
                        <div class="d-flex">
                            <span class="text-regular-14">Menampilkan {{ firsRows }} hingga {{ lastRows }} dari {{
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
import { mapState } from 'vuex'
import vSelect from 'vue-select'

export default {
    name: 'RoomMaster',
    components: {
        add_patient, vSelect
    },
    data() {
        return {
            dataFromViewPatientMaster: '',
            active: {
                modal_add_patient: false,
            },
            FilterForm: {
                rm_number: '',
                idntity_number: '',
                name: '',
                birth_place: '',
                birth_date: '',
                street_name: '',
                rt: '',
                rw: '',
                province: '',
                regency: '',
                district: '',
                village: '',
            },

            params: {
                offset: 0,
                limit: 20,
            },
            currentPage: 1,
            perPage: 20,
            firsRows: 1,
            lastRows: 20,
        }
    },
    mounted() {
        this.$store.state.totalRows = this.items.length
    },
    methods: {
        getFromViewPatientMaster(data) {
            this.$emit('getFromViewPatientMaster', data)
            this.dataFromViewPatientMaster = data
        },
        // selectedFromPatientMaster (value) { },

    },
    watch: {
        currentPage: {
            handler: function (value) {
                console.log(value)
                this.firsRows = this.perPage * (value - 1) + 1
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
        // 'FilterForm.province'(obj) {
        //     if (!obj) {
        //         this.FilterForm.regency = null;
        //         this.FilterForm.district = null;
        //         this.FilterForm.village = null;
        //     } else {
        //         const spread = { ...obj }
        //         if (spread.province && obj.value != spread.province.value) {
        //             this.FilterForm.regency = null;
        //             this.FilterForm.district = null;
        //             this.FilterForm.village = null;
        //         }
        //         this.FilterForm.regency = null;
        //         this.FilterForm.district = null;
        //         this.FilterForm.village = null;
        //         this.$store.dispatch('helper/getParamRegency', obj.value)
        //     }
        // },
        // 'FilterForm.regency' (obj) {
        //     if (!obj) {
        //         this.FilterForm.district = null;
        //         this.FilterForm.village = null;
        //     } else {
        //         const spread = { ...obj }
        //         if (spread.regency && obj.value != spread.regency.value) {
        //             this.FilterForm.district = null;
        //             this.FilterForm.village = null;
        //         }
        //         this.FilterForm.district = null;
        //         this.FilterForm.village = null;
        //         this.$store.dispatch('helper/getParamDistrict', obj.value)
        //     }
        // },
        // 'FilterForm.district' (obj) {
        //     if (!obj) {
        //         this.FilterForm.village = null;
        //     } else {
        //         const spread = { ...obj }
        //         if (spread.district && obj.value != spread.district.value) {
        //             this.FilterForm.village = null;
        //         }
        //         this.FilterForm.village = null;
        //         this.$store.dispatch('helper/getParamVillage', obj.value)
        //     }
        // },
        'FilterForm.province'(obj) {
            return this.$store.dispatch('helper/getParamRegency', obj.value)
        },
        'FilterForm.regency'(obj) {
            return this.$store.dispatch('helper/getParamDistrict', obj.value)
        },
        'FilterForm.district'(obj) {
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
    },
    created() {
        this.$store.dispatch('patient/getAll', this.params)
        this.$store.dispatch('helper/getParamProvince', 'province')
    },
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>