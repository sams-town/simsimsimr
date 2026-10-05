<template>
    <div>
        <!-- <section class="d-flex">
            <b-button pill @click="toggleMainTab('list')"
                :variant="active.main_tab === 'list' ? 'primary-500' : 'outline-primary-500'" class="mr-2">COA
            </b-button>
            <b-button pill @click="toggleMainTab('add')"
                :variant="active.main_tab === 'add' ? 'primary-500' : 'outline-primary-500'">Tambah Data</b-button>
        </section> -->
        <TopCoaMenu selectedMenu="menu1" />
        <section class="mt-24">
            <div v-if="active.main_tab === 'list'">
                <section class="mt-32">
                    <div class="row mx-0">
                        <b-form-group label="Per Page" label-cols="6" label-align="left" label-size="sm"
                            label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                            <span class="text-regular-14">Tampilkan</span>
                            <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions" />
                            <span class="text-regular-14">Entri</span>
                        </b-form-group>
                    </div>
                    <b-table bordered responsive hover small head-variant="light" :fields="fields" :items="items"
                        :per-page="0" :current-page="currentPage" thead-class="text-bold-14"
                        tbody-tr-class="text-regular-12" class="mt-12 text-center">
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
                                    <feather-icon icon="TrashIcon" @click="rowOnDelete(row.item.costing_id)" />
                                    <b-link v-b-modal.modal-center>
                                    </b-link>
                                </b-button>
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
                                                <label class="c-form-label">Kode</label>
                                                <b-form-input v-model="form.code" autocomplete="off" />
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label">Jenis Tarif</label>
                                                <v-select v-model="form.type_costing_id" :options="TypeCostingOptions"
                                                    :reduce="TypeCostingOptions => TypeCostingOptions.value" label="title"
                                                    placeholder="-- Pilih Jenis Tarif --"></v-select>
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label">Nama</label>
                                                <b-form-input v-model="form.name" autocomplete="off" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mt-20">
                                                <label class="c-form-label">Poliklinik/Ruangan</label>
                                                <div class="col-lg-12 py-2 bg-neutral-100"
                                                    style="height: 250px; overflow-y: scroll;">
                                                    <b-form-checkbox-group size="sm" stacked>
                                                        <div class="row">
                                                            <div class="mt-12 ml-20">
                                                                <template v-for="r_hfi in healthfacilityitem">
                                                                    <b-form-checkbox v-model="form.health_facility_item_id"
                                                                        :value="r_hfi.id" class="col-lg-12 my-1">{{
                                                                            r_hfi.name }}</b-form-checkbox>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </b-form-checkbox-group>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="mt-20">
                                                <label class="c-form-label">Tarif & Kelas Layanan</label>
                                                <table class="table">
                                                    <tr>
                                                        <td>
                                                            <v-select v-model="service_class_id" @input="onAddSC($event)"
                                                                :options="ServiceClassOptions"
                                                                :reduce="ServiceClassOptions => ServiceClassOptions"
                                                                label="title"
                                                                placeholder="-- Pilih Kelas Layanan --"></v-select>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <table class="table">
                                                    <tr>
                                                        <th>Kelas Layanan</th>
                                                        <th>Tarif</th>
                                                        <th>Fee Dokter</th>
                                                        <th>Fee Perawat</th>
                                                        <th>Fee RS/Klinik</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                    <tr class="item" v-for="(item, index) in form.costing_service_class"
                                                        :key="index">
                                                        <td>
                                                            <b-form-input style="display:none"
                                                                v-model="item.service_class.value" />
                                                            <b-form-input type="text" v-model="item.service_class.title"
                                                                aria-readonly="true" />
                                                        </td>
                                                        <td><b-form-input type="number" v-model="item.var_1"
                                                                autocomplete="off" /></td>
                                                        <td><b-form-input type="number" v-model="item.var_2"
                                                                autocomplete="off" /></td>
                                                        <td><b-form-input type="number" v-model="item.var_3"
                                                                autocomplete="off" /></td>
                                                        <td><b-form-input type="number" v-model="item.var_4"
                                                                autocomplete="off" /></td>
                                                        <td>
                                                            <b-button variant="danger-600" size="sm"
                                                                @click="deleteSC(index)" class="mr-2">
                                                                <feather-icon icon="TrashIcon" />
                                                            </b-button>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
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
import TopCoaMenu from '@/@core/components/top-menu/TopCoaMenu.vue';
import vSelect from 'vue-select'
export default {
    name: 'CoaBackoffice',
    components: { vSelect, TopCoaMenu },
    data() {
        return {
            active: {
                main_tab: 'list',
            },
            params: {
                offset: 0,
                limit: 20,
            },
            currentPage: 1,
            perPage: 20,
            firsRows: 1,
            lastRows: 20,
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
            form: {
                id: null,
                code: '',
                type_costing_id: '',
                name: '',
                costing_service_class: []
            },
            service_class_id: ''
        }
    },
    watch: {
        currentPage: {
            handler: function (value) {
                this.firsRows = this.perPage * (value - 1) + 1
                this.lastRows = this.perPage * (value) + 1
                this.params.offset = this.perPage * (value - 1)
                this.params.limit = this.perPage
                this.$store.dispatch('costing/getAll', this.params)
            }
        },
        perPage: {
            handler: function (value) {
                this.params.limit = this.perPage
                this.$store.dispatch('costing/getAll', this.params)
            }
        }
    },
    computed: {
        ...mapState({
            start: state => state.costing.start,
            totalRows: state => state.costing.totalRows,
            fields: state => state.costing.fields,
            items: state => state.costing.items,
            healthfacilityitem: state => state.healthfacilityitem.items,
            pageOptions: state => state.costing.pageOptions,
            TypeCostingOptions: state => state.helper.options.typecosting,
            ServiceClassOptions: state => state.serviceclass.options.serviceclass,
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
    methods: {
        onAddSC(event) {
            this.service_class_id = ''
            this.form.costing_service_class.push({ service_class: event, service_class_id: event.value, var_1: 0, var_2: 0, var_3: 0, var_4: 0, action: 'action' })
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
                                    this.$store.dispatch('costing/getAll')
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
            this.form.code = ''
            this.form.type_costing_id = ''
            this.form.name = ''
            this.form.costing_service_class = []
        },
        ResetForm() {
            this.form = {
                code: '',
                type_costing_id: '',
                name: '',
                costing_service_class: []
            }
        },
        validationForm() {
            this.show = true
            const formData = this.form
            this.$store.dispatch('costing/formPostPut', formData)
                .then(res => {
                    this.notification('Success', 'CheckIcon', 'success', 'You have successfully')
                    this.$store.dispatch('costing/getAll')
                    this.active.main_tab = 'list'
                    this.form.id = ''
                    this.form.code = ''
                    this.form.costing_service_class = []
                    this.form.name = ''
                    this.show = false
                },
                    err => {
                        this.show = false
                        this.notification('Failed', 'CloseIcon', 'danger', 'You have unsuccessful')
                    })
        },
        notification(a, b, c, d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d }
            })
        },
        editData(obj) {
            console.log(obj)
            this.active.main_tab = 'add'
            this.form.id = obj.id
            this.form.code = obj.code
            this.form.type_costing_id = obj.type_costing_id
            this.form.name = obj.name
            this.show = false
        },
    },
    mounted() {
        // Set the initial number of items
        this.$store.state.totalRows = this.items.length
    },
    created() {
        this.$store.dispatch('costing/getAll', this.params)
        this.$store.dispatch('helper/getParam', 'type_costing')
        this.$store.dispatch('healthfacilityitem/getAllList')
        this.$store.dispatch('serviceclass/getDetail')
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>