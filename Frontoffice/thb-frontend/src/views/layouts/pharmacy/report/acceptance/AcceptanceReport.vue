<template>
    <div>
        <TopPharmacyMenu selectedMenu="menu12" />
        <div class="col mt-32">
            <div class="card">
                <div class="card-header bg-primary fw-bold text-center text-white">Laporan Penerimaan Farmasi</div>
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div class="col-lg-9">
                            <div class="row">
                                <div class="col-lg-4">
                                    <label class="c-form-label">Tanggal Mulai</label>
                                    <b-form-input type="date" v-model="params.date_start" :max="params.date_end" />
                                </div>
                                <div class="col-lg-4 mt-12 mt-lg-0">
                                    <label class="c-form-label">Tanggal Akhir</label>
                                    <b-form-input type="date" v-model="params.date_end" :min="params.date_start" />
                                </div>
                                <div class="col-lg-4 mt-12 mt-lg-0">
                                    <label class="c-form-label">Supplier</label>
                                    <v-select v-model="params.supplier_id" :options="SupplierOptions" :reduce="SupplierOptions => SupplierOptions.value" label="title" placeholder="-- Pilih Supplier --"></v-select>
                                </div>
                                <div class="col-lg-4 mt-12">
                                    <label class="c-form-label">Nama Katalog</label>
                                    <v-select v-model="params.pharmacy_catalog_id" :options="CatalogOptions" :reduce="CatalogOptions => CatalogOptions.value" label="title" placeholder="-- Pilih Katalog --"></v-select>
                                </div>
                                <div class="col-lg-4 mt-12">
                                    <label class="c-form-label">No Dokumen</label>
                                    <b-form-input v-model="params.no_document" />
                                </div>
                                <div class="col-lg-4 mt-12">
                                    <label class="c-form-label">Tipe</label>
                                    <v-select v-model="params.type" aria-readonly="true" :options="OptionsType" :reduce="OptionsType => OptionsType.value" label="title" placeholder="-- Ringkas/Detail --"></v-select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        <b-button @click="exportExcel" :disabled="disabled_export" variant="w3green" class="px-16">
                            <feather-icon icon="FileTextIcon" /> Export Excel
                        </b-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapState } from 'vuex'
import TopPharmacyMenu from '@/@core/components/top-menu/TopPharmacyMenu.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import vSelect from 'vue-select'
import moment from 'moment'

export default {
    components: {
        TopPharmacyMenu,vSelect
    },
    data() {
        return {
            urlRoot: process.env.VUE_APP_ROOT_API,
            disabled_export: false,
            params: {
                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date_end: moment(new Date()).format('YYYY-MM-DD'),
                supplier_id: '',
                pharmacy_catalog_id: '',
                no_document: '',
                type:1
            },
            OptionsType:[
                { value: 1, title: 'Ringkas' },
                { value: 2, title: 'Detail' }
            ]
        }
    },
    computed: {
        ...mapState({ 
            SupplierOptions: state => state.company.options.supplier, 
            CatalogOptions: state => state.catalog.options.catalog, 

        }),
    },
    methods: {
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        exportExcel() {
            this.disabled_export = true
            if (this.params.date_start && this.params.date_end) {
                let queryParams = []
                          
                if (this.params.date_start) {
                    queryParams.push(`date_start=${this.params.date_start}`);
                }
    
                if (this.params.date_end) {
                    queryParams.push(`date_end=${this.params.date_end}`);
                }
    
                if (this.params.supplier_id) {
                    queryParams.push(`supplier_id=${this.params.supplier_id}`);
                }
            
                if (this.params.pharmacy_catalog_id) {
                    queryParams.push(`pharmacy_catalog_id=${this.params.pharmacy_catalog_id}`);
                }
    
                if (this.params.no_document) {
                    queryParams.push(`no_document=${this.params.no_document}`);
                }
    
                let paramsall = queryParams.join('&')
                if (this.params.type === 1) {
                    window.open(this.urlRoot+`report/pharmacy/receipt/get?${paramsall}`)
                } else {
                    window.open(this.urlRoot+`report/pharmacy/receipt/get_item?${paramsall}`)
                }
                
                this.disabled_export = false
            } else {
                this.notification('Warning','CloseIcon','warning','Tanggal mulai dan tanggal akhir wajib diisi')
                this.disabled_export = false
            }
        }
    },
    created () {
        this.$store.dispatch('company/getParamFlag', 3)
        this.$store.dispatch('catalog/getParam')
    }
} 
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>