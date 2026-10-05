<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'FARMASI' },
                { text: 'Kartu Stok' },
                // { text: this.form_transaction.no_order }
            ]">        
            <TopPharmacyMenu selectedMenu="menu4" />
            <b-overlay
                :show="show"
                spinner-variant="primary"
                spinner-type="grow"
                spinner-small
                rounded="sm"
                >
                <div class="col mt-32">
                    <div class="card">
                        <div class="card-header font-weight-bold bg-primary-200">Filter Data</div>
                        <div class="card-body">
                            <div class="d-flex justify-content-center">
                                <div class="col-lg-10">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div>
                                                <label class="c-form-label">Gudang/Apotik/Satelit</label>
                                                <v-select v-model="params.pharmacy_depo_id" :options="DepoOptions" :reduce="DepoOptions => DepoOptions.value" label="title" placeholder="-- Pilih Gudang --"></v-select>
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label">Nama Katalog</label>
                                                <v-select v-model="params.pharmacy_catalog_id" @search="onSearchCatalog" :filterable="false" :options="OptionsCatalog" :reduce="OptionsCatalog => OptionsCatalog.value" label="title" placeholder="-- Pilih Katalog --"></v-select>
                                            </div>
                                            <div v-if="report_format === '2'" class="row">
                                                <div class="col-lg-6 mt-12">
                                                    <label class="c-form-label">Tanggal</label>
                                                    <b-form-input type="date" v-model="params.date"/>
                                                </div>
                                            </div>
                                            <div v-if="report_format === '1'" class="row">
                                                <div class="col-lg-6 mt-12">
                                                    <label class="c-form-label">Tanggal Mulai</label>
                                                    <b-form-input type="date" v-model="params.start" :max="params.end" />
                                                </div>
                                                <div class="col-lg-6 mt-12">
                                                    <label class="c-form-label">Tanggal Akhir</label>
                                                    <b-form-input type="date" v-model="params.end" :min="params.start" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div>
                                                <label class="c-form-label">Format Laporan</label>
                                                <b-form-radio-group @change="changeTable($event)" stacked v-model="report_format">
                                                    <div class="p-2 rounded" :class="report_format === '1'? 'bg-pink-500 text-white' :'bg-pink-100'">
                                                        <b-form-radio value="1">Kartu Stok Obat</b-form-radio>
                                                    </div>
                                                    <div class="p-2 rounded mt-2" :class="report_format === '2'? 'bg-pink-500 text-white' :'bg-pink-100'">
                                                        <b-form-radio value="2">Monitor Persediaan Obat</b-form-radio>
                                                    </div>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-24">
                                        <b-button variant="outline-primary"  @click="searchs()" class="px-3 mr-2">Filter</b-button>
                                        <b-button @click="exportExcel" :disabled="disabled_export" variant="w3green" class="px-3">Export Excel</b-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col mt-32">
                    <div class="card">
                        <div class="card-header font-weight-bold bg-primary-200">List Data</div>
                        <div class="card-body">
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
                                            <b-table bordered responsive hover small head-variant="light" :items="items" :fields="report_format === '1' ? CardFields : MonitorFields" :per-page="0" :current-page="currentPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                                                <template #cell(batch)="row">
                                                    <div>{{ row.item.batch  }} | {{ dateFormat(row.item.expired_date)  }}</div>
                                                </template>
                                                <template #cell(date_created)="row">
                                                    <div>{{ dateFormatTime(row.item.date_created)  }}</div>
                                                </template>
                                                <template #cell(flag)="row">
                                                    <b-badge v-if="row.item.flag === 1" class="text-uppercase p-8" style="font-size: 12px; font-weight: 500;" variant="info-500">Penerimaan</b-badge>
                                                    <b-badge v-if="row.item.flag === 11" class="text-uppercase p-8" style="font-size: 12px; font-weight: 500;" variant="info-500">Batal/Retur Penerimaan</b-badge>
                                                    <b-badge v-if="row.item.flag === 3" class="text-uppercase p-8" style="font-size: 12px; font-weight: 500;" variant="success-500">Pemakaian</b-badge>
                                                    <b-badge v-if="row.item.flag === 4" class="text-uppercase p-8" style="font-size: 12px; font-weight: 500;" variant="primary-500">Mutasi</b-badge>
                                                    <b-badge v-if="row.item.flag === 6" class="text-uppercase p-8" style="font-size: 12px; font-weight: 500;" variant="warning-500">Pemakaian Obat Kronis</b-badge>
                                                    <b-badge v-if="row.item.flag === 7" class="text-uppercase p-8" style="font-size: 12px; font-weight: 500;" variant="warning-500">Opname</b-badge>
                                                    <b-badge v-if="row.item.flag === 8" class="text-uppercase p-8" style="font-size: 12px; font-weight: 500;" variant="danger-500">Pengurangan Stok</b-badge>
                                                </template>
                                                <template #cell(action)="row">
                                                    <div class="row mx-0">
                                                        <b-button variant="warning-500" size="sm" class="mr-1">
                                                            <feather-icon icon="EditIcon" @click="editData(row.item)" />
                                                        </b-button>
                                                        <b-button variant="danger-500" size="sm">
                                                            <feather-icon icon="TrashIcon" @click="rowOnDelete(row.item.id)" />
                                                            <b-link v-b-modal.modal-center>
                                                            </b-link>
                                                        </b-button>
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
                    </div>
                </div>
            </b-overlay>
        </Layout>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import TopPharmacyMenu from '@/@core/components/top-menu/TopPharmacyMenu.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import vSelect from 'vue-select'
import moment from 'moment'

export default {
    name: 'PatientPayment',
    components: {
        TopPharmacyMenu,vSelect
    },
    data() {
        return {
            urlRoot: process.env.VUE_APP_ROOT_API,
            disabled_export: false,
            active: {
                filter: true,
            },
            report_format: '1',
            params:{
                offset: 0,
                limit: 20,
                pharmacy_depo_id: '',
                pharmacy_catalog_id: '',
                date: moment(new Date()).format('YYYY-MM-DD'),
                start: moment(new Date()).format('YYYY-MM-DD'),
                end: moment(new Date()).format('YYYY-MM-DD'),
            },
            OptionsCatalog:[],
            currentPage:1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
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
                this.searchs()
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.searchs()
            }
        }
    },
    methods: {
        dateFormat(input_date) {
            const date = moment(input_date).format('DD-MM-YYYY');
            return date;
        },
        dateFormatTime(input_date) {
            const date = moment(input_date).format('DD-MM-YYYY HH:mm');
            return date;
        },
        onSearchCatalog(search, loading) {
            if(search.length >= 3) {
                loading(true);
                this.$store.dispatch('catalog/getParamSearchNew', search).then(data => {
                    this.OptionsCatalog = []
                    for (let i = 0; i < data.length; i++) {
                        const option = []
                        for (const key in data[i]) {
                            if (key === 'id') { option['value'] = data[i][key] } else if (key === 'is_delete') { option['data_catalog'] = data[i] } else if (key === 'name') { option['title'] = data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
                        }
                        this.OptionsCatalog.push(Object.assign({}, option))
                    }
                    loading(false)
                })
            }
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        searchs () {
            if (this.report_format === '1') {
                this.$store.dispatch('assets/getCard',this.params)
            } else if (this.report_format === '2') {
                this.$store.dispatch('assets/getMonitoring',this.params)
            }
        },
        changeTable (event) {
            if (event === '1') {
                this.$store.dispatch('assets/getCard',this.params)
            } else if (event === '2') {
                this.$store.dispatch('assets/getMonitoring',this.params)
            }
        },
        exportExcel() {
            let stock = []
            let monitor = []
                      
            if (this.params.pharmacy_depo_id) {
                stock.push(`pharmacy_depo_id=${this.params.pharmacy_depo_id}`);
                monitor.push(`pharmacy_depo_id=${this.params.pharmacy_depo_id}`);
            }
                      
            if (this.params.date) {
                stock.push(`date=${this.params.date}`);
                monitor.push(`date=${this.params.date}`);
            }
        
            if (this.params.pharmacy_catalog_id) {
                stock.push(`pharmacy_catalog_id=${this.params.pharmacy_catalog_id}`);
                monitor.push(`pharmacy_catalog_id=${this.params.pharmacy_catalog_id}`);
            }

            if (this.params.start) {
                stock.push(`date_start=${this.params.start}`);
            }

            if (this.params.end) {
                stock.push(`date_end=${this.params.end}`);
            }

            let all_stock = stock.join('&')
            let all_monitor = monitor.join('&')

            this.disabled_export = true
            if (this.report_format === '1') {
                if (this.params.start && this.params.end) {
                    window.open(this.urlRoot+`report/pharmacy/assets/card?${all_stock}`)
                    this.disabled_export = false
                } else {
                    this.notification('Warning','CloseIcon','warning','Tanggal mulai dan tanggal akhir wajib diisi')
                    this.disabled_export = false
                }
            } else if (this.report_format === '2') {
                window.open(this.urlRoot+`report/pharmacy/assets/monitoring?${all_monitor}`)
                this.disabled_export = false
            }
        }
    },
    computed: {
        ...mapState({ 
            totalRows: state => state.assets.totalRows, 
            fields: state => state.assets.fields, 
            CardFields: state => state.assets.CardFields, 
            MonitorFields: state => state.assets.MonitorFields, 
            // CatalogOptions: state => state.catalog.options.catalog, 
            DepoOptions: state => state.depo.options.depo, 
            items: state => state.assets.items, 
            pageOptions: state => state.assets.pageOptions, 
        })
    },
    created () {
        this.$store.dispatch('assets/getCard',this.params)
        // this.$store.dispatch('catalog/getParam')
        this.$store.dispatch('packages/getParam')
        this.$store.dispatch('depo/getParam')
        // this.$store.dispatch('catalog/getParam')
    }
};
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>