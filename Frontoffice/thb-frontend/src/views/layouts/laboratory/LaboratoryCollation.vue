<template>
    <div>
        <TopLaboratoryMenu selectedMenu="menu7" />
        <div class="col mt-32">
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
                                    <div class="">
                                        <label class="c-form-label">Nama</label>
                                        <b-form-input />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Tipe Modul</label>
                                        <b-form-select />
                                    </div>
                                    <div class="mt-24">
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
                            <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                                <span class="text-regular-14">Tampilkan</span>
                                <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
                                <span class="text-regular-14">Entri</span>
                            </b-form-group>
                        </div>
                        <b-table bordered responsive hover small head-variant="light" :items="items" :fields="fields" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
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
                                <span class="text-regular-14">Menampilkan {{ firstRow }} hingga {{ lastRow }} dari {{
                                    totalRows }} entri</span>
                                <div class="ml-auto">
                                    <b-pagination v-model="currentPage" :total-rows="totalRows"
                                        :per-page="perPage" :current-page="currentPage"
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
                                    <div class="col-lg-10">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mt-12">
                                                    <label class="c-form-label">Kode</label>
                                                    <b-form-input v-model="form.code" autocomplete="off"/>
                                                </div>
                                                <div class="mt-12">
                                                    <label class="c-form-label">Kelompok</label>
                                                    <b-form-input v-model="form.name" autocomplete="off"/>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="mt-20">
                                                    <label class="c-form-label">Parameter</label>
                                                    <table class="table">
                                                        <tr>
                                                            <td>
                                                                <v-select v-model="parameter" @input="onAddSC($event)" :options="ParameterOptions" :reduce="ParameterOptions => ParameterOptions" label="title" placeholder="-- Pilih Parameter --"></v-select>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <table class="table">
                                                        <tr>
                                                            <th>Parameter</th>
                                                            <th>Value</th>
                                                            <th>Satuan</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                        <tr class="item" v-for="item in form.parameter">
                                                            <td>
                                                                <b-form-input style="display:none" v-model="item.parameter_id" />
                                                                <b-form-input type="text" v-model="item.parameter" aria-readonly="true" />
                                                            </td>
                                                            <td><b-form-input type="text" v-model="item.value" aria-readonly="true" /></td>
                                                            <td><b-form-input type="text" v-model="item.unit" aria-readonly="true" /></td>
                                                            <td>
                                                                <b-button variant="danger-600" size="sm" @click="deleteSC(form.parameter.items.index+1)" class="mr-2">
                                                                    <feather-icon icon="TrashIcon" />
                                                                </b-button>
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
    </div>
</template>

<script>
import { mapState } from 'vuex'
import TopLaboratoryMenu from '@/@core/components/top-menu/TopLaboratoryMenu.vue'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import vSelect from 'vue-select'
export default {
    components: { TopLaboratoryMenu, vSelect },
    data() {
        return {
            active: {
                main_tab: 'list',
            },
            show: false,
            dir: false,
            currentPage: 1,
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
                id:null,
                code:'',
                name:'',
                parameter:[]
            },
            parameter:''
        }
    },
    computed: {
        ...mapState({ 
            totalRows: state => state.laboratory.collation.totalRows, 
            fields: state => state.laboratory.collation.fields, 
            items: state => state.laboratory.collation.items, 
            perPage: state => state.laboratory.collation.perPage, 
            pageOptions: state => state.laboratory.collation.pageOptions, 
            firstRow: state => state.laboratory.collation.firstRow, 
            lastRow: state => state.laboratory.collation.lastRow, 
            ParameterOptions: state => state.laboratory.options.parameter, 
        }),
        sortOptions() {
            // Create an options list from our fields
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
        onAddSC (event) {
            this.parameter = ''
            console.log(event)
            this.form.parameter.push({ parameter:event.title, parameter_id:event.value, value: event.val, unit: event.unit, action: 'action'})
        },
        deleteSC (index) {
            this.form.parameter.splice(index, 1)
        },
        rowOnDelete (id) {
            this.$bvModal
                .msgBoxConfirm('Please confirm that you want to delete everything.', {
                title: 'Please Confirm', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                this.$store.dispatch('laboratory/removeCollation', id)
                .then(response => {
                    if (response.status === 200) {
                    this.$store.dispatch('laboratory/getCollationAll')
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
        toggleMainTab(tab) {
            this.active.main_tab = tab
            this.form.id = ''
            this.form.code = ''
            this.form.name = ''
            this.form.parameter = []
        },
        validationForm () {
            this.show = true
            const formData = this.form 
            this.$store.dispatch('laboratory/formPostPutCollation', formData)
            .then(res => {
                this.notification('Success','CheckIcon','success','You have successfully')
                this.$store.dispatch('laboratory/getCollationAll')
                this.active.main_tab = 'list'
                this.form.id = ''
                this.form.code = ''
                this.form.name = ''
                this.form.parameter = []
                this.show = false
            }, 
            err => {
                this.show = false
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
            })
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
            this.form.code = obj.code
            this.form.name = obj.name
            let parameter = {}
            for (let i = 0; i < obj.parameter.length; i++) {
                parameter = { 
                    parameter:obj.parameter[i].name, 
                    parameter_id:obj.parameter[i].parameter_id, 
                    value: obj.parameter[i].value, 
                    unit: obj.parameter[i].unit, 
                    action: 'action'
                }
                this.form.parameter.push(parameter)
            }
            this.show = false
        },
    },
    mounted() {
        // Set the initial number of items
        this.$store.state.totalRows = this.items.length
    },
    created () {
        this.$store.dispatch('laboratory/getCollationAll')
        this.$store.dispatch('laboratory/getParameterAll')
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>