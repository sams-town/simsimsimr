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
                <b-overlay :show="show" spinner-variant="primary" spinner-type="grow" spinner-small rounded="sm">
                    <section class="card">
                        <div class="card-header font-weight-bold bg-primary-200"> Filter Data</div>
                        <div class="card-body">
                            <div class="d-flex justify-content-center">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mt-12">
                                                <label class="c-form-label">Nama</label>
                                                <b-form-input v-model="params.name" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-24">
                                        <b-button type="submit" variant="primary" class="mr-1" :disabled="isDisabledFilter"
                                            @click.prevent="filterData">
                                            <feather-icon icon="SearchIcon" /> Filter
                                        </b-button>
                                        <b-button variant="outline-primary" @click.prevent="filterReset">
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
                            <template #cell(is_delete)="data">
                                <b-badge :variant="status[1][data.value]">
                                    {{ status[0][data.value] }}
                                </b-badge>
                            </template>
                            <template #cell(action)="row">
                                <div class="row mx-0">
                                    <template v-if="row.item.name === 'pharmacy' || row.item.name === 'laboratory' || row.item.name === 'radiology'">
                                        <router-link :to="{ path: '/queue/'+ row.item.name +'/'+ row.item.id}" target="_blank">
                                            <b-button variant="warning-500" size="sm" class="mr-1">
                                                <feather-icon icon="MonitorIcon" />
                                            </b-button>
                                        </router-link>
                                    </template>
                                    <template v-else>
                                        <router-link :to="{ path: '/queue/poli/'+ row.item.id}" target="_blank">
                                            <b-button variant="warning-500" size="sm" class="mr-1">
                                                <feather-icon icon="MonitorIcon" />
                                            </b-button>
                                        </router-link>
                                    </template>
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
                                <span class="text-regular-14">Menampilkan {{ firstRows }} hingga {{ lastRows }} dari {{
                                    totalRows }} entri</span>
                                <div class="ml-auto">
                                    <b-pagination v-model="currentPage" :total-rows="totalRows" :per-page="perPage" align="fill"
                                        size="sm" class="my-0"></b-pagination>
                                </div>
                            </div>
                        </div>
                    </section>
                </b-overlay>
            </div>
            <div v-if="active.main_tab === 'add'">
                <div class="card">
                    <div class="card-header font-weight-bold bg-primary-200">Tambah Data</div>
                    <div class="card-body">
                        <div class="col-lg-10">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mt-12">
                                        <label class="c-form-label">Nama Display</label>
                                        <b-form-input v-model="form.name" autocomplete="off" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mt-20">
                                <label class="c-form-label"></label>
                                <table class="table">
                                    <tr>
                                        <th>Poli</th>
                                        <th>Dokter</th>
                                        <th>Aksi</th>
                                    </tr>
                                    <tr class="item" v-for="(item, index) in form.display">
                                        <td>{{ item.health_facility_item_name }}</td>
                                        <td>{{ item.doctor_name }}</td>
                                        <td colspan="2">
                                            <b-button variant="danger-600" size="sm" @click="deleteSC(index)" class="mr-2">
                                                <feather-icon icon="TrashIcon" />
                                            </b-button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <v-select v-model="health_facility_item" :options="OptionsHealthFacilityItem" :reduce="OptionsHealthFacilityItem => OptionsHealthFacilityItem" label="title" placeholder="-- Poli --"></v-select>
                                            <v-select v-model="doctor" @input="onAdd($event)" :options="OptionsDoctor" :reduce="OptionsDoctor => OptionsDoctor" label="title" placeholder="-- Dokter --"></v-select>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <b-col cols="12" class="px-0 mt-24">
                                <b-button type="submit" variant="primary" class="mr-1" :disabled="isDisabled"
                                    @click.prevent="validationForm"> Submit </b-button>
                                <b-button type="reset" variant="outline-secondary"> Reset </b-button>
                            </b-col>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import vSelect from 'vue-select'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
export default {
    name: 'DisplayMaster',
    components: {
        vSelect
    },
    data() {
        return {
            active: {
                main_tab: 'list',
            },
            params: {
                offset: 0,
                limit: 20,
                name: '',
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
            health_facility_item: '',
            doctor: '',
            status: [{
                0: 'Active', 1: 'Deleted'
            },
            {
                0: 'primary', 1: 'danger'
            }],
            form: {
                id: null,
                display: [],
                delete: [],
                name: ''
            }
        }
    },
    watch: {
        health_facility_item (value) {
            if (value) {
                return this.$store.dispatch('employee/getParam', value.value)
            }
        },
        currentPage: {
            handler: function (value) {
                console.log(value)
                this.firstRows = this.perPage * (value - 1) + 1
                this.lastRows = this.perPage * (value) + 1
                this.params.offset = this.perPage * (value - 1)
                this.params.limit = this.perPage
                this.$store.dispatch('display/getAll', this.params)
            }
        },
        perPage: {
            handler: function (value) {
                this.params.limit = this.perPage
                this.$store.dispatch('display/getAll', this.params)
            }
        }
    },
    computed: {
        ...mapState({
            items: state => state.display.items,
            fields: state => state.display.fields,
            pageOptions: state => state.display.pageOptions,
            totalRows: state => state.display.totalRows,
            OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
            OptionsDoctor: state => state.employee.options.employee
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
        onAdd () {
            this.form.display.push({ 
                doctor_id:this.doctor.value, 
                doctor_name:this.doctor.title, 
                health_facility_item_name: this.health_facility_item.title,
                health_facility_item_id: this.health_facility_item.value, 
                action: 'action'
            })
            this.doctor = ''
            this.health_facility_item = ''
        },
        deleteSC (index) {
            this.form.delete.push(this.form.display[index])
            this.form.display.splice(index, 1)
        },
        resetForm() {
            this.params.name = ''
        },
        toggleMainTab(tab) {
            this.active.main_tab = tab
            this.form.id = ''
            this.form.name = ''
            this.form.display = []
            this.form.delete = []
        },
        validationForm() {
            this.show = true
            this.isDisabled = true
            const formData = this.form
            this.$store.dispatch('display/formPostPut', formData)
            .then(res => {
                this.notification('Success', 'CheckIcon', 'success', 'You have successfully')
                this.$store.dispatch('display/getAll', this.params)
                this.active.main_tab = 'list'
                this.form.id = ''
                this.form.name = ''
                this.form.display = []
                this.form.delete = []
                this.show = false
                this.isDisabled = false
            },
            err => {
                this.show = false
                this.isDisabled = false
                this.notification('Failed', 'CloseIcon', 'danger', 'You have unsuccessful')
            })

        },
        filterData() {
            this.show = true;
            this.isDisabledFilter = true;
            this.$store.dispatch('display/getAll', this.params)
            .then(() => {
                this.show = false;
                this.isDisabledFilter = false;
            })
            .catch(error => {
                console.error(error);
                this.show = false;
                this.isDisabledFilter = false;
            });
        },
        filterReset() {
            this.params.name = ''
            this.params.offset = 0
            this.params.limit = this.perPage
            this.$store.dispatch('display/getAll', this.params)
        },
        notification(a, b, c, d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d }
            })
        },
        editData(obj) {
            this.active.main_tab = 'add'
            this.form.id = obj.id
            if (obj.display) {
                this.form.display = obj.display
            } else {
                this.form.display = []
            }
            this.form.name = obj.name
            this.show = false
        },
        rowOnDelete (id) {
            this.$bvModal
                .msgBoxConfirm('Please confirm that you want to delete everything.', {
                title: 'Please Confirm', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                this.$store.dispatch('display/remove', id)
                .then(response => {
                    if (response.status === 200) {
                        this.$store.dispatch('display/getAll', this.params)
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
        }
    },
    mounted() {
        this.$store.state.totalRows = this.items.length
        this.show = false
    },
    created() {
        this.$store.dispatch('healthfacilityitem/getParam', '3cb626f5-9148-4fb1-8663-3d7f028b01a6')
        this.$store.dispatch('display/getAll', this.params)
        this.show = false
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>