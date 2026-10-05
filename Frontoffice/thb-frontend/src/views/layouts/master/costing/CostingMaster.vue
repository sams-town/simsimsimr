<template>
    <div>
        <section class="d-flex">
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
                                <div class="mt-12">
                                    <label class="c-form-label">Nama</label>
                                    <b-form-input v-model="params.name" autocomplete="off" />
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
                        <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect"
                            class="text-nowrap mb-md-0 mr-1">
                            <span class="text-regular-14">Tampilkan</span>
                            <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions" />
                            <span class="text-regular-14">Entri</span>
                        </b-form-group>
                    </div>
                    <b-table bordered responsive hover small head-variant="light" :items="items" :fields="fields" :per-page="0" :current-page="currentPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                        <template #cell(is_delete)="data">
                            <b-badge :variant="status[1][data.value]">
                                {{ status[0][data.value] }}
                            </b-badge>
                        </template>
                        <!-- <template #cell(name)="row">
                            <p>{{row.item.name}}</p>
                            <p>{{row.item.desc}}</p>
                        </template> -->
                        <template #cell(var_1)="data">
                            {{data.value | currency}}
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
                            <span class="text-regular-14">Menampilkan {{ firstRows }} hingga <template
                                    v-if="lastRows < totalRows">{{ lastRows }}</template> <template v-else>{{ totalRows
                                    }}</template> dari {{ totalRows }} entri</span>
                            <div class="ml-auto">
                                <b-pagination v-model="currentPage" :total-rows="totalRows" :per-page="perPage" align="fill"
                                    size="sm" class="my-0"></b-pagination>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div v-if="active.main_tab === 'add'">
                <div class="card">
                    <div class="card-header font-weight-bold bg-primary-200">Tambah Data</div>
                    <b-overlay :show="show" spinner-variant="primary" spinner-type="grow" spinner-small rounded="sm">
                        <div class="card-body">
                            <div class="">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mt-12">
                                                <label class="c-form-label">Jenis Tarif</label>
                                                <v-select v-model="form.classification_one_type_id" :options="TypeCostingCOptions"
                                                    :reduce="TypeCostingCOptions => TypeCostingCOptions.value" label="title"
                                                    placeholder="-- Pilih Jenis Tarif --"></v-select>
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label">Sub Jenis Tarif</label>
                                                <v-select v-model="form.type_costing_id" :options="TypeCostingOptions"
                                                    :reduce="TypeCostingOptions => TypeCostingOptions.value" label="title"
                                                    placeholder="-- Pilih Sub Jenis Tarif --"></v-select>
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label">Nama</label>
                                                <b-form-input v-model="form.name" autocomplete="off" />
                                            </div>
                                            <div class="mt-12">
                                                <b-form-checkbox
                                                    v-model="form.is_single"
                                                    :value="1"
                                                    :unchecked-value="0"
                                                >
                                                    Single Tarif
                                                </b-form-checkbox>
                                            </div>
                                            <!-- <div class="mt-12">
                                                <label class="c-form-label">Desc</label>
                                                <b-form-input v-model="form.desc" autocomplete="off" />
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label">Accurate Satuan</label>
                                                <b-form-input v-model="form.costing_service_class[0].bride_satuan" autocomplete="off" />
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label">Accurate Kode</label>
                                                <b-form-input v-model="form.costing_service_class[0].bride_code" autocomplete="off" />
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label">Accurate Department</label>
                                                <b-form-input v-model="form.costing_service_class[0].bride_department" autocomplete="off" />
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label">Accurate Department Sub</label>
                                                <b-form-input v-model="form.costing_service_class[0].bride_department_sub" autocomplete="off" />
                                            </div> -->
                                            <div class="mt-12">
                                                <label class="c-form-label">Tarif Dasar</label>
                                                <b-form-input v-model="form.var_1" autocomplete="off" />
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label">Jasa Dokter</label>
                                                <b-form-input v-model="form.var_2" autocomplete="off" />
                                            </div>
                                            <template v-if="form.is_single">
                                                <div class="mt-12">
                                                    <label class="c-form-label">Poli</label>
                                                    <v-select v-model="form.health_facility_item_id"
                                                        :options="healthfacilityitem"
                                                        :reduce="healthfacilityitem => healthfacilityitem.value"
                                                        label="title"
                                                        placeholder="-- Pilih Poli --"></v-select>
                                                </div>
                                                <div class="mt-12">
                                                    <label class="c-form-label">Asuransi/Penjamin</label>
                                                    <v-select v-model="form.company_id"
                                                        :options="CompanyOptions"
                                                        :reduce="CompanyOptions => CompanyOptions.value"
                                                        label="title"
                                                        placeholder="-- Pilih Asuransi/Penjamin --"></v-select>
                                                </div>
                                                <div class="mt-12">
                                                    <label class="c-form-label">Kelas Layanan</label>
                                                    <v-select v-model="form.service_class_id"
                                                        :options="ServiceClassOptions"
                                                        :reduce="ServiceClassOptions => ServiceClassOptions.value"
                                                        label="title"
                                                        placeholder="-- Pilih Kelas Layanan --"></v-select>
                                                </div>
                                            </template>

                                            <!-- <div class="mt-12">
                                                <label class="c-form-label">Harga</label>
                                                <b-form-input v-model="form.costing_service_class[0].var_1" autocomplete="off" />
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label">Departement</label>
                                                <v-select v-model="form.costing_service_class[0].health_facility_item_id"
                                                    :options="healthfacilityitem"
                                                    :reduce="healthfacilityitem => healthfacilityitem.value"
                                                    label="title"
                                                    placeholder="-- Pilih Departement --"></v-select>
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label">Asuransi/Penjamin</label>
                                                <v-select v-model="form.costing_service_class[0].company_insurance_id"
                                                    :options="CompanyOptions"
                                                    :reduce="CompanyOptions => CompanyOptions.value"
                                                    label="title"
                                                    placeholder="-- Pilih Asuransi/Penjamin --"></v-select>
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label">Kelas Layanan</label>
                                                <v-select v-model="form.costing_service_class[0].service_class_id"
                                                    :options="ServiceClassOptions"
                                                    :reduce="ServiceClassOptions => ServiceClassOptions.value"
                                                    label="title"
                                                    placeholder="-- Pilih Kelas Layanan --"></v-select>
                                            </div> -->
                                        </div>
                                    </div>
                                    <b-col cols="12" class="px-0 mt-24">
                                        <b-button type="submit" variant="primary" class="mr-1"
                                            @click.prevent="validationForm"> Submit </b-button>
                                        <b-button type="reset" variant="outline-secondary" @click="ResetForm"> Reset
                                        </b-button>
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
import vSelect from 'vue-select'
export default {
    name: 'CostingMaster',
    components: { vSelect },
    data() {
        return {
            active: {
                main_tab: 'list',
            },
            params: {
                offset: 0,
                limit: 20,
                name: ''
            },
            currentPage: 1,
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
            form:{   
                id: null,

                is_single: 0,
                service_class_id: '',
                company_id: '',
                type_costing_id: '',
                classification_one_type_id: '',
                health_facility_item_id: '',
                name: '',
                var_1: 0,
                var_2: 0,
            },
            form2: {
                id: null,
                type_costing_id: '',
                is_ref: 0,
                name: '',
                desc: '',
                costing_service_class: [
                    {
                        company_insurance_id: '',
                        service_class_id: '',
                        health_facility_item_id: '',
                        bride_code: '',
                        bride_satuan: '',
                        bride_department: '',
                        bride_department_sub: '',
                        var_1: 0
                    }
                ]
            },
            service_class_id: '',
            company_insurance_id: ''
        }
    },
    watch: {
        currentPage: {
            handler: function (value) {
                this.firstRows = this.perPage * (value - 1) + 1
                this.lastRows = this.perPage * (value) + 1
                this.params.offset = this.perPage * (value - 1)
                this.params.limit = this.perPage
                this.$store.dispatch('costing/getAllParams', this.params)
            }
        },
        perPage: {
            handler: function (value) {
                this.params.limit = this.perPage
                this.$store.dispatch('costing/getAllParams', this.params)
            }
        },
    },
    computed: {
        ...mapState({
            totalRows: state => state.costing.totalRows,
            fields: state => state.costing.fields,
            items: state => state.costing.items,
            healthfacilityitem: state => state.healthfacilityitem.options.healthfacilityitem,
            pageOptions: state => state.costing.pageOptions,
            TypeCostingOptions: state => state.helper.options.typecosting,
            TypeCostingCOptions: state => state.helper.options.typecostingc,
            ServiceClassOptions: state => state.serviceclass.options.serviceclass,
            CompanyOptions: state => state.company.options.company,
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
        }
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat().format(value)
        },
    },
    methods: {
        onAddIC(event) {
            this.company_insurance_id = event
        },
        onAddSC(event) {
            this.service_class_id = ''
            let index = this.form.costing_service_class.findIndex((item) => item.service_class_id === event.value && this.company_insurance_id === item.company_insurance);
            // Check if the object with the specified property value exists in the array
            if (index === -1) {
                this.form.costing_service_class.push({ company_insurance: this.company_insurance_id, company_insurance_id:this.company_insurance_id.value, service_class: event, service_class_id: event.value, var_1: 0, action: 'action' })
            } else {
                alert('Penjamin & Kelas Sudah Ada!');
            }
            // this.onAddC(this.company_insurance_id.value,event.value)
        },
        onAddC(company_insurance_id,service_class_id) {
            // this.service_class_id = ''
            this.form.costing_value.push({company_insurance_id:company_insurance_id, service_class_id:service_class_id, coa: '', name: '', var_1: 0, action: 'action' })
        },
        deleteSC(index) {
            this.form.costing_service_class.splice(index, 1)
        },
        rowOnDelete(id) {
            this.$bvModal
                .msgBoxConfirm('Please confirm that you want to delete everything.', {
                    title: 'Please Confirm', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
                })
                .then(value => {
                    if (value === true) {
                        this.$store.dispatch('costing/remove', id)
                            .then(response => {
                                if (response.status === 200) {
                                    this.$store.dispatch('costing/getAllParams', this.params)
                                    this.notification('Success', 'CheckIcon', 'success', 'You have successfully')
                                } else {
                                    this.notification('Failed', 'CloseIcon', 'danger', 'You have unsuccessful')
                                }
                            },
                                error => {
                                    this.notification('Failed', 'CloseIcon', 'danger', 'You have unsuccessful')
                                })
                    }
                })
        },
        toggleMainTab(tab) {
            this.active.main_tab = tab
            this.form.id = ''
            // this.form.is_ref = 0
            // this.form.desc = ''
            // this.form.type_costing_id = ''
            // this.form.name = ''
            // this.form.costing_service_class = [
            //     {
            //         company_insurance_id: '',
            //         service_class_id: '',
            //         health_facility_item_id: '',
            //         bride_code: '',
            //         bride_satuan: '',
            //         bride_department: '',
            //         bride_department_sub: '',
            //         var_1: 0
            //     }
            // ]
        },
        ResetForm() {
            this.form = {
                is_single: 0,
                service_class_id: '',
                company_id: '',
                type_costing_id: '',
                classification_one_type_id: '',
                health_facility_item_id: '',
                name: '',
                var_1: 0,
                var_2: 0,
            }
        },
        validationForm() {
            this.show = true
            const formData = this.form
            this.$store.dispatch('costing/formPostPutV2', formData)
                .then(res => {
                    this.notification('Success', 'CheckIcon', 'success', 'Sukses Menyimpan Tarif')
                    this.$store.dispatch('costing/getAllParams', this.params)
                    this.active.main_tab = 'list'
                    this.form.id = ''
                    // this.form.code = ''
                    // this.form.costing_service_class = []
                    // this.form.costing_value = []
                    // this.form.name = ''
                    this.show = false
                },
                    err => {
                        this.show = false
                        this.notification('Failed', 'CloseIcon', 'danger', 'Gagal Menyimpan Tarif')
                    })
        },
        notification(a, b, c, d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d }
            })
        },
        filterData() {
            this.show = true
            this.isDisabledFilter = true
            this.$store.dispatch('costing/getAllParams', this.params)
                .then(res => {
                    this.show = false
                    this.isDisabledFilter = false
                },
                    err => {
                        this.show = false
                        this.isDisabledFilter = false
                    })
        },
        editData(obj) {
            this.active.main_tab = 'add'
            this.form.id = obj.id
            this.$store.dispatch('costing/getById', obj.id).then(data => {
                this.form.code = data[0].code
                this.form.type_costing_id = data[0].type_costing_id
                this.form.name = data[0].name
                for (let index = 0; index < data[0].costing_csc.length; index++) {
                    this.form.costing_service_class.push({ company_insurance: data[0].costing_csc[index].company_insurance, company_insurance_id:data[0].costing_csc[index].company_insurance_id, service_class: data[0].costing_csc[index].service_class, service_class_id: data[0].costing_csc[index].service_class_id, var_1: data[0].costing_csc[index].var_1, action: 'action' })
                    for (let index_v = 0; index_v < data[0].costing_csc[index].costing_value.length; index_v++) {
                        this.form.costing_value.push({company_insurance_id:data[0].costing_csc[index].company_insurance_id, service_class_id:data[0].costing_csc[index].service_class_id, coa: data[0].costing_csc[index].costing_value[index_v].coa, name: data[0].costing_csc[index].costing_value[index_v].name, var_1: data[0].costing_csc[index].costing_value[index_v].var_1, action: 'action' })
                    }
                }
            })
            this.show = false
        },
    },
    mounted() {
        // Set the initial number of items
        this.$store.state.totalRows = this.items.length
    },
    created() {
        this.$store.dispatch('costing/getAllParams', this.params)
        this.$store.dispatch('helper/getParam', 'type_costing')
        this.$store.dispatch('helper/getParam', 'costing_c_one')
        this.$store.dispatch('healthfacilityitem/getParamAlls')
        this.$store.dispatch('serviceclass/getDetail')
        this.$store.dispatch('company/getParamFlag', 1)
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>