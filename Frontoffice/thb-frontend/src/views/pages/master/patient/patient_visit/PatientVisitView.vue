<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Master Data Kunjungan Pasien' },
            ]"
        />
        <section class="content pt-12">
            <section>
                <!-- <add_patient :dataFromViewPatientMaster="dataFromViewPatientMaster" /> -->
                <!-- <update_patient :dataFromUpdatePatientMaster="dataFromUpdatePatientMaster" /> -->
            </section>
            <section>
                <div>
                    <section>
                        <div class="d-flex justify-content-between">
                            <div class="row w-100 align-items-end">
                                <div class="col-lg-4">
                                    <label class="fs-13 mb-0">Cari Pasien</label>
                                    <div class="d-flex align-items-center position-relative">
                                        <b-form-input v-model="searchQuery" @input="filterPatient" class="pl-5" size="sm" autocomplete="off" placeholder="masukkan nama, NRM" />
                                        <b-icon icon="search" class="position-absolute pl-3 text-neutral-400" />
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="fs-13 mb-0">Tanggal Mulai</label>
                                    <b-form-input size="sm" @input="filterPatient" :max="params.date_end" type="date" v-model="params.date_start"></b-form-input>
                                </div>
                                <span class="fs-16">-</span>
                                <div class="col-lg-3">
                                    <label class="fs-13 mb-0">Tanggal Akhir</label>
                                    <b-form-input size="sm" @input="filterPatient" :min="params.date_start" :max="dateNow" type="date" v-model="params.date_end"></b-form-input>
                                </div>
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
                                    <div class="font-weight-bold">{{ row.item.alias }}. {{ row.item.name }}</div>
                                    <div>{{ row.item.birth_date }} ({{ parsedAge(row.item.birth_date) }}) {{ row.item.norm }}</div>
                                </div>
                            </template>
                            <template #cell(health_facility_item_name)="row">
                                <div>
                                    {{ row.item.health_facility_item_name }} <br>
                                    <span class="font-weight-bold">{{ row.item.doctor_in_charge_name }}</span>
                                </div>
                            </template>
                            <template #cell(action)="row">
                                <b-button @click="goToDetailPage(row.item.norm)" size="sm" variant="primary" v-b-popover.hover.right="'Buka Detail Pasien Disini'" class="ml-2">
                                    <feather-icon icon="FileIcon" />
                                </b-button>
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
    </div>
</template>
<script>
import Layout from '@/@core/light/Layout.vue';
import add_patient from '@/views/layouts/_registration/regisPatient/modal/add_patient.vue'
import update_patient from '@/views/layouts/_registration/regisPatient/modal/update_patient.vue'
import { mapState } from 'vuex'
import vSelect from 'vue-select'
import dateMixin from "@/mixins/dateMixin";
import moment from 'moment';
import router from '@/router/index';

export default {
    name: 'RoomMaster',
    components: {
        update_patient, add_patient, vSelect, Layout
    },
    mixins: [dateMixin],
    data() {
        return {
            searchQuery: JSON.parse(localStorage.getItem('masterpatientvisitfiltersession')) ? JSON.parse(localStorage.getItem('masterpatientvisitfiltersession')).norm : "", 
            dataFromViewPatientMaster: '',
            dataFromUpdatePatientMaster: '',
            params: JSON.parse(localStorage.getItem('masterpatientvisitfiltersession')) ? JSON.parse(localStorage.getItem('masterpatientvisitfiltersession')) : {
                offset: 0,
                limit: 20,
                norm: '',
                name: '',
                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date_end: moment(new Date()).format('YYYY-MM-DD'),
            },
            dateNow: moment(new Date()).format('YYYY-MM-DD'),
            currentPage: 1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,

            searchTimeout: null,
        }
    },
    watch: {
        currentPage: {
            handler: function (value) {
                console.log(value)
                this.firstRows = this.perPage * (value - 1) + 1
                this.lastRows = this.perPage * (value)
                this.params.offset = this.perPage * (value - 1)
                this.params.limit = this.perPage

                this.filterPatient()
            }
        },
        perPage: {
            handler: function (value) {
                this.params.limit = this.perPage
                this.lastRows = this.perPage
                this.filterPatient()
            }
        },
    },
    computed: {
        ...mapState({
            items: state => state.registration.master_data.items,
            fields: state => state.registration.patientMasterVisit.fields,
            pageOptions: state => state.registration.pageOptions,
            totalRows: state => state.registration.master_data.totalRows,
        }),
        sortOptions() {
            // Create an options list from our fields
            return this.fields.filter(f => f.sortable).map(f => ({ text: f.label, value: f.key }))
        },
        dateFormat() {
            return create_at => this.$options.filters.formatDate(create_at)
        },
    },

    methods: {
        filterPatient() {
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            this.searchTimeout = setTimeout(() => {

                this.params.norm = ""
                this.params.name = ""
                // this.params.idcard = ""
                // this.params.birth_date = ""

                const input = this.searchQuery.trim();
                // console.log(input, 'thisearchquery')

                // if(/^\d{16}$/.test(input)){
                //     this.params.idcard = input
                // }
                // } else if (/^\d{4}-\d{2}-\d{2}$/.test(input)) {
                //     this.params.birth_date = input
                // } 
                if (/^\d+$/.test(input)) {
                    this.params.norm = input
                }
                else {
                    this.params.name = input
                }
                

                this.$store.dispatch("registration/getListMasterData", this.params)
                .then((res) => {
                    localStorage.setItem("masterpatientvisitfiltersession", JSON.stringify(this.params))
                })
                .catch((err) => {
                    console.error("Error saat request:", err)
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
        getFromUpdatePatientMaster(data) {
            this.$emit('getFromUpdatePatientMaster', data)
            // this.dataFromUpdatePatientMaster = data
        },
    },

    mounted() {
    },

    created() {
        this.filterPatient()
    },
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>