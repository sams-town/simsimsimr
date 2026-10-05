<template>
    <div>
        <TopPharmacyMenu selectedMenu="menu10" />
        <section class="mt-24 d-flex">
            <b-button pill @click="toggleMainTab('list')"
                :variant="active.main_tab === 'list' ? 'primary-500' : 'outline-primary-500'" class="mr-2">List
            </b-button>
            <b-button pill @click="toggleMainTab('add')"
                :variant="active.main_tab === 'add' ? 'primary-500' : 'outline-primary-500'">Tambah Data</b-button>
        </section>
        <section class="mt-24">
            <div v-if="active.main_tab === 'list'">
                <section class="card">
                    <div class="card-header font-weight-bold bg-primary-200"> Filter Data</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <div class="col-lg-4">
                                <div>
                                    <label class="c-form-label">Kode</label>
                                    <b-form-input v-model="params.code" autocomplete="off"/>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Nama</label>
                                    <b-form-input v-model="params.name" autocomplete="off"/>
                                </div>
                                <div class="mt-24">
                                    <b-button type="submit" variant="primary" class="mr-1" :disabled="isDisabledFilter" @click.prevent="filterData"> 
                                        <feather-icon icon="SearchIcon"/> Filter
                                    </b-button>
                                    <b-button type="submit" variant="primary" class="mr-1" @click="exportData()"> 
                                        <feather-icon icon="DownloadIcon"/> Export Master
                                    </b-button>
                                    <b-button variant="outline-primary" @click.prevent="filterReset">
                                        <feather-icon icon="XIcon"/> Clear Filter
                                    </b-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="mt-32">
                    <div class="row mx-0">
                        <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                            <span class="text-regular-14">Tampilkan</span>
                            <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
                            <span class="text-regular-14">Entri</span>
                        </b-form-group>
                    </div>
                    <b-table bordered responsive hover small head-variant="light" :items="items" :fields="fields" :per-page="0" :current-page="currentPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                        <template #cell(name)="row">
                            {{ row.item.name }}
                        </template>
                        <template #cell(is_chronic)="row">
                            <b-badge variant="primary" v-if="row.item.is_chronic === 2">Obat Kronis</b-badge>
                        </template>
                        <template #cell(is_antibiotics)="row">
                            <b-badge variant="primary" v-if="row.item.is_antibiotics === 2">Obat Antibiotik</b-badge>
                        </template>
                        <template #cell(is_executive)="row">
                            <b-badge variant="primary" v-if="row.item.is_executive === 1">Both</b-badge>
                            <b-badge variant="primary" v-if="row.item.is_executive === 2">Eksekutif</b-badge>
                        </template>
                        <template #cell(is_delete)="data">
                            <b-badge :variant="status[1][data.value]">
                            {{ status[0][data.value] }}
                            </b-badge>
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
                </section>
            </div>
            <div v-if="active.main_tab === 'add'">
                <div class="card">
                    <div class="card-header font-weight-bold bg-primary-200">Tambah Data</div>
                    <b-overlay
                        :show="show"
                        spinner-variant="primary"
                        spinner-type="grow"
                        spinner-small
                        rounded="sm"
                        >
                        <div class="card-body">
                            <div class="">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mt-12">
                                                <label class="c-form-label">Nama</label>
                                                <b-form-input v-model="form.name" autocomplete="off"/>
                                            </div>
                                            <!-- <div class="mt-12">
                                                <label class="c-form-label">Brand</label>
                                                <v-select v-model="form.brand_id" :options="BrandOptions" :reduce="BrandOptions => BrandOptions.value" label="title" placeholder="-- Pilih Brand --"></v-select>
                                            </div> -->
                                            <div class="mt-12">
                                                <label class="c-form-label">Jenis Katalog</label>
                                                <v-select v-model="form.category_catalog_id" :options="CategoryCatalogOptions" :reduce="CategoryCatalogOptions => CategoryCatalogOptions.value" label="title" placeholder="-- Pilih Jenis Katalog --"></v-select>
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label">Generik</label>
                                                <v-select v-model="form.generik_id" :options="GenericOptions" :reduce="GenericOptions => GenericOptions.value" label="title" placeholder="-- Pilih Generik --"></v-select>
                                            </div>
                                            <!-- <div class="mt-12">
                                                <label class="c-form-label">Nama Dagang</label>
                                                <b-form-input v-model="form.name_brand" autocomplete="off"/>
                                            </div> -->
                                            <!-- <div class="mt-12">
                                                <label class="c-form-label">Komposisi</label>
                                                <v-select v-model="form.generik_id" :options="GenerikOptions" :reduce="GenerikOptions => GenerikOptions.value" label="title" placeholder="-- Pilih Komposisi --"></v-select>
                                            </div> -->
                                            <!-- <div class="row">
                                                <div class="col-lg-6">
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mt-12">
                                                        <label class="c-form-label">Kode</label>
                                                        <b-form-input v-model="form.code" autocomplete="off"/>
                                                    </div>
                                                </div>
                                            </div> -->
                                            <div class="mt-12">
                                                <label class="c-form-label">Pabrik</label>
                                                <v-select v-model="form.factory_id" :options="FactoryOptions" :reduce="FactoryOptions => FactoryOptions.value" label="title" placeholder="-- Pilih Pabrik --"></v-select>
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label">Harga</label>
                                                <b-form-input type="number" v-model="form.price" aria-placeholder="Harga" autocomplete="off"/>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="mt-12">
                                                        <label class="c-form-label">Min Obat</label>
                                                        <b-form-input type="number" v-model="form.min" autocomplete="off"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mt-12">
                                                        <label class="c-form-label">Max Obat</label>
                                                        <b-form-input type="number" v-model="form.max" autocomplete="off"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mt-12">
                                                <label class="c-form-label">Obat Kronis</label>
                                                <b-form-checkbox v-model="form.is_chronic" value="2" />
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label">Obat Antibiotik</label>
                                                <b-form-checkbox v-model="form.is_antibiotics" value="2" />
                                            </div>
                                            <div class="row mt-12">
                                                <div class="col-lg-6">
                                                    <div class="mt-12">
                                                        <label class="c-form-label">Dosis</label>
                                                        <b-form-input type="number" v-model="form.dose" autocomplete="off"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mt-12">
                                                        <label class="c-form-label">Satuan</label>
                                                        <v-select v-model="form.dose_id" :options="DoseOptions" :reduce="DoseOptions => DoseOptions.value" label="title" placeholder="-- Pilih Satuan Dosis --"></v-select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="mt-12">
                                                <table class="table">
                                                    <tr>
                                                        <td></td>
                                                        <td>Supplier</td>
                                                        <td>Kemasan Kecil</td>
                                                        <td>Isi</td>
                                                        <td>Kemasan Besar</td>
                                                        <td>Isi</td>
                                                    </tr>
                                                    <tr class="item" v-for="(item, index) in form.supplier">
                                                        <td>
                                                            <b-button variant="danger-500" size="sm" @click="deleteSC(index)">
                                                                <feather-icon icon="TrashIcon"  />
                                                                    <b-link v-b-modal.modal-center>
                                                                </b-link>
                                                            </b-button>
                                                        </td>
                                                        <td width="40%">
                                                            <v-select v-model="item.suppliers_id" :options="SupplierOptions" :reduce="SupplierOptions => SupplierOptions.value" label="title" placeholder="-- Pilih Supplier --"></v-select>
                                                        </td>
                                                        <td width="20%">
                                                            <v-select v-model="item.package_id" :options="PackageOptions" :reduce="PackageOptions => PackageOptions.value" label="title" placeholder="Pilih"></v-select>
                                                        </td>
                                                        <td>
                                                            <b-form-input type="number" v-model="item.value_package" aria-placeholder="Isi Kemasan Depo/Kecil" autocomplete="off"/>
                                                        </td>
                                                        <td width="20%">
                                                            <v-select v-model="item.package_big_id" :options="PackageOptions" :reduce="PackageOptions => PackageOptions.value" label="title" placeholder="Pilih"></v-select>
                                                        </td>
                                                        <td>
                                                            <b-form-input type="number" v-model="item.value_package_big" aria-placeholder="Isi Kemasan Besar" autocomplete="off"/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td colspan="2">
                                                            <v-select v-model="suppliers_id" @input="addSupplier()" :options="SupplierOptions" :reduce="SupplierOptions => SupplierOptions" label="title" placeholder="-- Pilih Supplier --"></v-select>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <b-col cols="12" class="px-0 mt-24">
                                        <b-button type="submit" variant="primary" class="mr-1" @click.prevent="validationForm"> Submit </b-button>
                                        <b-button type="reset" variant="outline-secondary"> Reset </b-button>
                                    </b-col>
                                </div>
                            </div>
                        </div>
                    </b-overlay>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import { mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import TopPharmacyMenu from '@/@core/components/top-menu/TopPharmacyMenu.vue';
import vSelect from 'vue-select'
export default {
    components: {
        TopPharmacyMenu, vSelect
    },
    data() {
        return {
            active: {
                main_tab: 'list',
            },
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
            GenericOptions: [{
                title:'',
                value:0
            },{
                title:'Generik',
                value:1
            },{
                title:'Paten',
                value:2
            },{
                title:'Meeto',
                value:3
            },{
                title:'Original',
                value:4
            }],
            suppliers_id:'',
            form: {
                id:null,
                category_catalog_id:'',
                code:'',
                name:'',
                name_brand:'',
                composition:'',
                value_package:'',
                value_package_big:'',
                brand_id:'',
                generik_id:0,
                package_id:'',
                package_big_id:'',
                suppliers_id:'',
                supplier:[],
                factory_id:'',
                dose_id:'',
                dose:'',
                is_chronic:1,
                is_antibiotics:1,
                price:'',
                min:'',
                max:''
            }
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
    computed: {
        ...mapState({ 
            totalRows: state => state.catalog.totalRows, 
            fields: state => state.catalog.fields, 
            items: state => state.catalog.items, 
            pageOptions: state => state.catalog.pageOptions, 
            BrandOptions: state => state.brand.options.brand, 
            GenerikOptions: state => state.generik.options.generik, 
            PackageOptions: state => state.packages.options.packages, 
            DoseOptions: state => state.dose.options.dose, 
            SupplierOptions: state => state.company.options.supplier, 
            CategoryCatalogOptions: state => state.category_catalog.options.category_catalog, 
            FactoryOptions: state => state.company.options.factory, 
        }),
        sortOptions() {
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
        addSupplier () {
            console.log(this.suppliers_id)
            let index = this.form.supplier.findIndex((item) => item.suppliers_id === this.suppliers_id.value);
            // Check if the object with the specified property value exists in the array
            if (index === -1) {
                // If not found, push a new object with the desired properties
                // this.form_soap.form_prescription_parent.push({
                //     id:event.value,
                //     prescription:event.title,
                //     used:'',
                //     value:0
                // }) 
                this.form.supplier.push(
                    { 
                        id:this.suppliers_id.value,
                        date_added:'',
                        is_delete:'',
                        package_big_id:'',
                        package_big_name:'',
                        package_id:'',
                        package_name:'',
                        suppliers_id:this.suppliers_id.value,
                        suppliers_name:this.suppliers_id.title,
                        value_package:'',
                        value_package_big:''
                    }
                )
            } else {
                // If found, log a message indicating that the object already exists
                console.log("Supplier Sudah Terinput");
            }
            console.log(this.form.supplier)
        },
        rowOnDelete (id) {
            this.$bvModal
                .msgBoxConfirm('Please confirm that you want to delete everything.', {
                title: 'Please Confirm', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                this.$store.dispatch('catalog/remove', id)
                .then(response => {
                    if (response.status === 200) {
                    this.$store.dispatch('catalog/getAll')
                    this.notification('Success','CheckIcon','success','You have successfully')
                    } else {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    }
                },
                error => {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                }) 
                }
            })
        },
        deleteSC (index) {
            this.form.supplier.splice(index, 1)
        },
        toggleMainTab(tab) {
            this.active.main_tab = tab
            this.form.id = ''
            this.form.category_catalog_id = ''
            this.form.code = ''
            this.form.name = ''
            this.form.name_brand = ''
            this.form.composition = ''
            this.form.value_package = ''
            this.form.value_package_big = ''
            this.form.brand_id = ''
            this.form.generik_id = 0
            this.form.package_id = ''
            this.form.package_big_id = ''
            this.form.suppliers_id = ''
            this.form.supplier = []
            this.form.factory_id = ''
            this.form.dose_id = ''
            this.form.dose = ''
            this.form.price = ''
            this.form.min = ''
            this.form.max = ''
        },
        validationForm () {
            this.show = true
            this.isDisabled = true
            const formData = this.form 
            this.$store.dispatch('catalog/formPostPut', formData)
            .then(res => {
                this.notification('Success','CheckIcon','success','You have successfully')
                this.$store.dispatch('catalog/getAll',this.params)
                this.active.main_tab = 'list'
                this.form.id = ''
                this.form.category_catalog_id = ''
                this.form.code = ''
                this.form.name = ''
                this.form.name_brand = ''
                this.form.composition = ''
                this.form.value_package = ''
                this.form.value_package_big = ''
                this.form.brand_id = ''
                this.form.generik_id = 0
                this.form.package_id = ''
                this.form.package_big_id = ''
                this.form.suppliers_id = ''
                this.form.supplier = []
                this.form.factory_id = ''
                this.form.dose_id = ''
                this.form.dose = ''
                this.form.price = ''
                this.form.min = ''
                this.form.max = ''
                this.show = false
                this.isDisabled = false
                console.log('sukses');
            }, 
            err => {
                console.log('error');
                this.show = false
                this.isDisabled = false
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
            })
        },
        filterData () {
            this.show = true
            this.isDisabledFilter = true
            this.$store.dispatch('catalog/getAll', this.params)
            .then(res => {
                this.show = false
                this.isDisabledFilter = false
            },
            err => {
                this.show = false
                this.isDisabledFilter = false
            })
        },
        filterReset () {
            this.form.category_catalog_id = ''
            this.form.code = ''
            this.form.name = ''
            this.form.name_brand = ''
            this.form.composition = ''
            this.form.value_package = ''
            this.form.value_package_big = ''
            this.form.brand_id = ''
            this.form.generik_id = 0
            this.form.package_id = ''
            this.form.package_big_id = ''
            this.form.suppliers_id = ''
            this.form.supplier = []
            this.form.factory_id = ''
            this.form.dose_id = ''
            this.form.dose = ''
            this.form.price = ''
            this.form.min = ''
            this.form.max = ''
            this.params.offset = 0
            this.params.limit = this.perPage
            this.$store.dispatch('catalog/getAll',this.params)
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        editData (obj) {
            this.active.main_tab = 'add'
            this.form.id = obj.id
            this.form.category_catalog_id = obj.category_catalog_id
            this.form.code = obj.code
            this.form.name = obj.name
            this.form.name_brand = obj.name_brand
            this.form.composition = obj.composition
            this.form.value_package = obj.value_package
            this.form.value_package_big = obj.value_package_big
            this.form.brand_id = obj.brand_id
            this.form.generik_id = obj.generik_id
            this.form.package_id = obj.package_id
            this.form.package_big_id = obj.package_big_id
            this.form.suppliers_id = obj.suppliers_id
            this.form.supplier = obj.supplier
            this.form.factory_id = obj.factory_id
            this.form.dose_id = obj.dose_id
            this.form.dose = obj.dose
            this.form.is_chronic = obj.is_chronic
            this.form.is_antibiotics = obj.is_antibiotics
            this.form.price = obj.price
            this.form.min = obj.min
            this.form.max = obj.max
            this.show = false
        },
        exportData () {
            window.open(process.env.VUE_APP_ROOT_API+"pharmacy/catalog/export")
        }
    },
    mounted() {
        this.$store.state.totalRows = this.items.length
        this.show = false
    },
    created () {
        this.$store.dispatch('catalog/getAll',this.params)
        this.$store.dispatch('dose/getParam')
        this.$store.dispatch('packages/getParam')
        this.$store.dispatch('category_catalog/getParam')
        this.$store.dispatch('generik/getParam')
        this.$store.dispatch('helper/getParam', 'type_catalog')
        this.$store.dispatch('company/getParamFlag', 3)
        this.$store.dispatch('company/getParamFlag', 4)
        this.show = true
    }
} 
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>