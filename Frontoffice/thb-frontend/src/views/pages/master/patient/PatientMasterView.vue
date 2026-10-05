<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Master Pasien' },
            ]"
        >
        <section class="">
            <section>
                <!-- <add_patient :dataFromViewPatientMaster="dataFromViewPatientMaster" /> -->
                <update_patient :dataFromUpdatePatientMaster="dataFromUpdatePatientMaster" />
            </section>
            <section>
                <div>
                    <section>
                        <div class="d-flex justify-content-between">
                            <div class="row w-100 align-items-end">
                                <div class="col-lg-4">
                                    <label class="fs-13 mb-0">Cari Pasien</label>
                                    <div class="d-flex align-items-center position-relative">
                                        <b-form-input v-model="searchQuery" @input="filterPatient" class="pl-5" size="sm" autocomplete="off" placeholder="masukkan nama, NRM atau no identitas" />
                                        <b-icon icon="search" class="position-absolute pl-3 text-neutral-400" />
                                    </div>
                                </div>
                                <!-- <div class="col-lg-3">
                                    <label class="fs-13 mb-0">Tanggal Mulai</label>
                                    <b-form-input size="sm" type="date" v-model="params.date_start"></b-form-input>
                                </div>
                                <div class="col-lg-3">
                                    <label class="fs-13 mb-0">Tanggal Akhir</label>
                                    <b-form-input size="sm" type="date" v-model="params.date_end"></b-form-input>
                                </div> -->
                            </div>
                            <div class="d-flex align-items-end">
                                <div class="d-flex align-items-center">
                                    <span class="fs-12 mr-1">Tampilkan</span>
                                    <b-form-select id="per-page-select" style="width:55px;" v-model="perPage" size="sm" :options="pageOptions" />
                                </div>
                            </div>
                        </div>
                        <b-table bordered responsive hover small head-variant="light" :fields="fields" :items="items" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                            <template #cell(name)="row">
                                <div class="text-left">
                                    <div class="font-weight-bold">{{ row.item.alias_name }}. {{ row.item.name }}</div>
                                    <div>{{ row.item.birth_date }} ({{ parsedAge(row.item.birth_date) }}) {{ row.item.norm }}</div>
                                </div>
                                <!-- {{ row.item.name }} [{{ row.item.alias_name }}] -->
                            </template>
                            <template #cell(action)="row">
                                <b-button @click="getFromUpdatePatientMaster(row.item)" size="sm" variant="info-400" v-b-popover.hover.right="'Lihat Pasien Disini'" class="mr-1">
                                    <feather-icon icon="FileIcon" />
                                </b-button>

                                <b-button @click="goToDetailPage(row.item.norm)" size="sm" variant="primary" v-b-popover.hover.right="'Buka Detail Pasien Disini'" class="ml-2">
                                    <feather-icon icon="FileIcon" />
                                </b-button>

                                <b-button @click="gotToRMPage(row.item.norm)" size="sm" variant="success-400" v-b-popover.hover.right="'Lihat Rekam Medis'" class="ml-2">
                                    <feather-icon icon="FileIcon" />
                                </b-button>

                                <!-- <b-button @click="getFromUpdatePatientMaster(row.item)" variant="info-400" size="sm" class="mr-1">Lihat</b-button>

                                <b-button @click="goToDetailPage(row.item.norm)" variant="info-600" size="sm" class="ml-2">Detail</b-button> -->

                                <!-- <router-link :to="{ path: '/master/patient/detail/'+ row.item.norm}">
                                    <b-button variant="info-600" size="sm" class="ml-2">Detail</b-button>
                                </router-link> -->
                                <!-- <b-button variant="info-600" size="sm" class="mr-1">Ubah</b-button> -->
                                <!-- <b-button variant="danger-600" size="sm" class="mr-1">Deactivation</b-button> -->
                            </template>
                        </b-table>
                        <div class="col px-0 mt-20">
                            <div class="d-flex">
                                <div class="mr-auto">
                                    <b-pagination v-model="currentPage" :total-rows="totalRows" :per-page="perPage" align="fill" size="sm" class="my-0"></b-pagination>
                                </div>
                                <span class="fs-12">Menampilkan {{ firstRows }} hingga
                                    <template v-if="lastRows < totalRows">{{ lastRows }}</template>
                                    <template v-else>{{ totalRows}}</template>
                                    dari {{ totalRows }} entri
                                </span>
                            </div>
                        </div>
                    </section>
                </div>
            </section>
        </section>
        </Layout>
    </div>
</template>
<script>
import Layout from '@/@core/light/Layout.vue';
import dateMixin from "@/mixins/dateMixin";
import router from '@/router/index';
import add_patient from '@/views/layouts/_registration/regisPatient/modal/add_patient.vue';
import update_patient from '@/views/layouts/_registration/regisPatient/modal/update_patient.vue';
import moment from 'moment';
import vSelect from 'vue-select';
import { mapState } from 'vuex';

export default {
    name: 'RoomMaster',
    components: {
        update_patient, add_patient, vSelect, Layout
    },
    mixins: [dateMixin],
    data() {
        return {
            searchQuery: JSON.parse(localStorage.getItem('masterpatientfiltersession')) ? JSON.parse(localStorage.getItem('masterpatientfiltersession')).norm : "",
            dataFromViewPatientMaster: '',
            dataFromUpdatePatientMaster: '',
            active: {
                modal_add_patient: false,
            },
            params: JSON.parse(localStorage.getItem('masterpatientfiltersession')) ? JSON.parse(localStorage.getItem('masterpatientfiltersession')) : {
                offset: 0,
                limit: 20,
                norm: '',
                idcard: '',
                name: '',
            },
            currentPage: 1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
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
            optionSex:[
                { label: 'Laki-Laki', value: 1 },
                { label: 'Perempuan', value: 0 },
                { label: 'Semua', value: '' },
            ],

            searchTimeout: null,
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
                this.lastRows = this.perPage * (value)
                this.params.offset = this.perPage * (value - 1)
                this.params.limit = this.perPage

                this.filterPatient()
                // this.$store.dispatch('patient/getAll', this.params)
            }
        },
        perPage: {
            handler: function (value) {
                this.params.limit = this.perPage
                this.lastRows = this.perPage
                this.filterPatient()

                // this.$store.dispatch('patient/getAll', this.params)
            }
        },
    },
    computed: {
        ...mapState({
            items: state => state.patient.items,
            fields: state => state.patient.patientMaster.fields,
            pageOptions: state => state.patient.pageOptions,
            totalRows: state => state.patient.totalRows,
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
        filterPatient() {
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            this.searchTimeout = setTimeout(() => {
                // this.isDisabledFilter = true;

                this.params.norm = ""
                this.params.name = ""
                this.params.idcard = ""
                // this.params.birth_date = ""

                const input = this.searchQuery.trim();
                // console.log(input, 'thisearchquery')

                if(/^\d{16}$/.test(input)){
                    this.params.idcard = input
                }
                // } else if (/^\d{4}-\d{2}-\d{2}$/.test(input)) {
                //     this.params.birth_date = input
                // }
                else {
                    if (/^\d+$/.test(input)) {
                        this.params.norm = input
                    }
                    else {
                        this.params.name = input
                    }
                }

                this.$store.dispatch("patient/getAll", this.params)
                .then((res) => {
                    localStorage.setItem("masterpatientfiltersession", JSON.stringify(this.params))
                    // this.isDisabledFilter = false
                })
                .catch((err) => {
                    console.error("Error saat request:", err)
                    // this.isDisabledFilter = false
                })
            }, 300)
        },
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
        gotToRMPage(norm){
            const obj = {
                norm: norm
            }

            if (obj && obj.norm) {
                localStorage.setItem('obj_norm', JSON.stringify(obj))
                this.$router.push({ path: `/master/patient/medical-record/` })
            }
        },
        getFromUpdatePatientMaster(data) {
            this.$emit('getFromUpdatePatientMaster', data)
            this.dataFromUpdatePatientMaster = data
        },
        // resetForm() {
        //     this.params.norm = ''
        //     this.params.idcard = ''
        //     this.params.name = ''
        //     this.params.birth_date = ''
        //     this.params.sex = ''
        // },
    },

    mounted() {
        // this.$store.state.totalRows = this.items.length
        // this.show = true
    },

    created() {
        this.filterPatient()
        // this.$store.dispatch('patient/getAll', this.params)
        // this.$store.dispatch('helper/getParamProvince', 'province')
        // this.show = true
    },
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>