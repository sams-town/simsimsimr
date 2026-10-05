<template>
    <div>
        <TopPharmacyMenu selectedMenu="menu9" />
        <div class="col mt-32">
            <div class="card">
                <div @click="active.filter = !active.filter" class="card-header pointer bg-primary-200">
                    <div class="d-flex justify-content-between">
                        <span class="font-weight-bold">Review Harga Penerimaan/Harga Jual Farmasi</span>
                        <feather-icon icon="ChevronDownIcon" v-if="active.filter == false" />
                        <feather-icon icon="ChevronUpIcon" v-else />
                    </div>
                </div>
                <div v-if="active.filter == true" class="card-body">
                    <div class="d-flex justify-content-center">
                        <div class="col-lg-10">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="">
                                        <label class="c-form-label">Tanggal Filter</label>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <b-input-group>
                                                    <b-form-input id="example-input" v-model="datePick.start" type="text"
                                                        placeholder="YYYY-MM-DD" autocomplete="off"></b-form-input>
                                                    <b-input-group-append>
                                                        <b-form-datepicker v-model="datePick.start" button-only
                                                            right></b-form-datepicker>
                                                    </b-input-group-append>
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-6">
                                                <b-input-group>
                                                    <b-form-input id="example-input" v-model="datePick.end" type="text"
                                                        placeholder="YYYY-MM-DD" autocomplete="off"></b-form-input>
                                                    <b-input-group-append>
                                                        <b-form-datepicker v-model="datePick.end" button-only
                                                            right></b-form-datepicker>
                                                    </b-input-group-append>
                                                </b-input-group>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <div class="mt-12">
                                        <label class="c-form-label">Gudang/Apotik/Satelit</label>
                                        <b-form-select />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Nama Katalog</label>
                                        <b-form-select />
                                    </div> -->
                                </div>
                                <div class="col-lg-6">
                                    <div>
                                        <label class="c-form-label">Format Laporan</label>
                                        <b-form-radio-group @change="changeTable($event)" stacked v-model="report_format">
                                            <div class="p-2 rounded" :class="report_format === '1'? 'bg-pink-500 text-white' :'bg-pink-100'">
                                                <b-form-radio value="1">LIST HARGA JUAL/PENERIMAAN</b-form-radio>
                                            </div>
                                            <div class="p-2 rounded mt-2" :class="report_format === '2'? 'bg-pink-500 text-white' :'bg-pink-100'">
                                                <b-form-radio value="2">UPDATE HARGA JUAL BERLAKU</b-form-radio>
                                            </div>
                                        </b-form-radio-group>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-24">
                                <b-button pill variant="outline-primary" class="px-3 mr-2">Cancel</b-button>
                                <router-link :to="report_format === '1' ? { name: 'PharmacyStockAvailableView' } : { name: 'PharmacySuppliesView' }" target="_blank">
                                    <b-button pill variant="primary" class="px-3">Export Excel</b-button>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end card-body -->
            </div>
            <!-- end card -->
        </div>
        <div class="col mt-32">
            <div class="card">
                <div @click="active.filter = !active.filter" class="card-header pointer bg-primary-200">
                    <div class="d-flex justify-content-between">
                        <span class="font-weight-bold">List Data</span>
                        <feather-icon icon="ChevronDownIcon" v-if="active.filter == false" />
                        <feather-icon icon="ChevronUpIcon" v-else />
                    </div>
                </div>
                <div v-if="active.filter == true" class="card-body">
                    <div class="d-flex justify-content-center">
                        <div class="col-lg-12">
                            <div class="">
                                <div class="row">
                                    <div class="row mx-0">
                                        <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                                            <span class="text-regular-14">Tampilkan</span>
                                            <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
                                            <span class="text-regular-14">Entri</span>
                                        </b-form-group>
                                    </div>
                                    <b-table bordered responsive hover small head-variant="light" :items="items" :fields="fields" :per-page="0" :current-page="currentPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                                        <template #cell(is_delete)="data">
                                            <b-badge :variant="status[1][data.value]">
                                            {{ status[0][data.value] }}
                                            </b-badge>
                                        </template>
                                        <template #cell(action)="row">
                                            <div class="row mx-0">
                                                <!-- <b-button variant="warning-500" size="sm" class="mr-1">
                                                    <feather-icon icon="EditIcon" @click="editData(row.item)" />
                                                </b-button>
                                                <b-button variant="danger-500" size="sm">
                                                    <feather-icon icon="TrashIcon" @click="rowOnDelete(row.item.id)" />
                                                    <b-link v-b-modal.modal-center>
                                                    </b-link>
                                                </b-button> -->
                                            </div>
                                        </template>
                                    </b-table>
                                    <div class="col px-0 mt-20">
                                        <div class="d-flex">
                                            <span class="text-regular-14">Menampilkan {{ firstRows }} hingga <template v-if="lastRows < totalRows">{{ lastRows }}</template> <template v-else>{{ totalRows }}</template> dari {{ totalRows }} entri</span>
                                            <div class="ml-auto">
                                                <b-pagination 
                                                    v-model="currentPage" 
                                                    :total-rows="totalRows"
                                                    :per-page="perPage" 
                                                    align="fill" size="sm" class="my-0"></b-pagination>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end card-body -->
            </div>
            <!-- end card -->
        </div>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import TopPharmacyMenu from '@/@core/components/top-menu/TopPharmacyMenu.vue';
import vSelect from 'vue-select'
export default {
    name: 'PatientPayment',
    components: {
        TopPharmacyMenu
    },
    data() {
        return {
            active: {
                filter: true,
            },
            datePick: {
                start: null,
                end: null,
            },
            report_format: '1',
            params:{
                offset:0,
                limit:20,
                code:'',
                name:''
            },
            currentPage:1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            isDisabled: false,
            isDisabledFilter: false,
            show: false,
            dir: false,
            sortBy: '',
            sortDesc: false,
            sortDirection: 'asc',
            filter: null,
            filterOn: [],
            status: [{
                0: 'Active', 1: 'Deleted'
            },
            {
                0: 'primary', 1: 'danger'
            }],
        }
    },
    watch: {
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.$store.dispatch('catalog/getAll',this.params)
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.$store.dispatch('catalog/getAll',this.params)
            }
        }
    },
    methods: {
        changeTable (event) {
            console.log(event)
            if (event === '1') {
                this.$store.dispatch('assets/getCardPrice',this.params)
            } else if (event === '2') {
                this.$store.dispatch('assets/getMonitoring',this.params)
            }
        }
    },
    computed: {
        ...mapState({ 
            totalRows: state => state.assets.totalRows, 
            fields: state => state.assets.fieldsPrice, 
            items: state => state.assets.items, 
            pageOptions: state => state.assets.pageOptions, 
        })
    },
    created () {
        this.$store.dispatch('assets/getCardPrice',this.params)
        this.$store.dispatch('packages/getParam')
        this.$store.dispatch('catalog/getParam')
    }
};
</script>
