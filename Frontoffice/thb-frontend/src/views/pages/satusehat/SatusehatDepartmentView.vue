<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'SATUSEHAT' },
                { text: 'ORGANISASI' },
                // { text: this.form_transaction.no_order }
            ]"
            >
            <TopSatusehatMenu selectedMenu="menu3" class="mt-2" />
            <section class="d-flex mt-20">
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
                                    <div class="mt-24">
                                        <b-button type="submit" variant="primary" class="mr-1" :disabled="isDisabledFilter" @click.prevent="filterData"> 
                                            <feather-icon icon="SearchIcon"/> Filter
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
                            <b-form-group label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                                <span class="text-regular-14">Tampilkan</span>
                                <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
                                <span class="text-regular-14">Entri</span>
                            </b-form-group>
                        </div>
                        <b-table bordered fixed small head-variant="light" :items="department_list" :fields="departmentFields" :per-page="0" :current-page="currentPage" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                            <!-- <template #cell(is_delete)="data">
                                <b-badge :variant="status[1][data.value]">
                                {{ status[0][data.value] }}
                                </b-badge>
                            </template> -->
                            <template #cell(action)="row">
                                <b-button @click="onEdit(row.item)" variant="warning-500" size="sm" class="mr-1">
                                    <feather-icon icon="EditIcon" />
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
                                                <div class="">
                                                    <label class="c-form-label">Nama</label>
                                                    <b-form-input v-model="form.name" autocomplete="off"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mt-3">
                                                    <b-button type="submit" variant="primary" class="mr-1" @click.prevent="getDepartmentBride"> Get ID Satusehat </b-button>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mt-3">
                                                    <label class="c-form-label">ID Satusehat</label>
                                                    <b-form-input v-model="form.bride_satusehat"/>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mt-3">
                                                    <label class="c-form-label">Nama</label>
                                                    <b-form-input v-model="form.bride_satusehat_name"/>
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
        </Layout>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import vSelect from 'vue-select'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import TopSatusehatMenu from '@/@core/components/top-menu/TopSatusehatMenu.vue';
import axios from 'axios'
export default {
    name: 'departmentMaster',
    components: {
        vSelect, ToastificationContent,TopSatusehatMenu
    },
    data() {
        return {
            active: {
                main_tab: 'list',
            },
            
            departmentFields: [
                { key: 'action', label: 'Aksi', thStyle: { width: '150px' },},
                { key: 'name', label: 'Departemen', sortable: true },
                { key: 'bride_satusehat', label: 'ID Organisasi Satusehat', sortable: true },
            ],

            params: {
                offset: 0,
                limit: 20,
                name: '',
                bride_satusehat: '',
            },

            currentPage: 1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            totalRows: 0,
            pageOptions: [20, 50, 100],

            department_list:[],
            bride: {search:'21e90402-61d3-4411-8de7-2b02c02a7002'},

            form: {
                id:null,
                name:'',
                bride_satusehat:'',
                bride_satusehat_name:'',
            },

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
            handler: function (value) {
                this.firstRows = this.perPage * (value - 1) + 1
                this.lastRows = this.perPage * (value)
                this.params.offset = this.perPage * (value - 1)
                this.params.limit = this.perPage

                this.getList()
            }
        },
        perPage: {
            handler: function (value) {
                this.params.limit = this.perPage
                this.getList()
            }
        },
    },
    computed: {
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
        rowOnDelete (id) {
            this.$bvModal
                .msgBoxConfirm('Please confirm that you want to delete everything.', {
                title: 'Please Confirm', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                this.$store.dispatch('department/remove', id)
                .then(response => {
                    if (response.status === 200) {
                    this.$store.dispatch('department/getAll',this.params)
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
            this.form.name = ''
            this.form.bride_satusehat = ''
            this.form.bride_satusehat_name = ''
        },
        validationForm () {
            this.show = true
            const formData = this.form 
            this.$store.dispatch('department/postPutDepartment', formData)
            .then(res => {
                this.notification('Success','CheckIcon','success','You have successfully')
                this.getList()
                this.active.main_tab = 'list'
                this.form.id = ''
                this.form.name = ''
                this.form.bride_satusehat = ''
                this.form.bride_satusehat_name = ''
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
        onEdit (obj) {
            this.active.main_tab = 'add'
            this.form.id = obj.id
            this.form.name = obj.name
            this.form.bride_satusehat = obj.bride_satusehat
            this.form.bride_satusehat_name = obj.bride_satusehat_name
            this.show = false
        },
        filterData () {
            this.show = true
            this.isDisabledFilter = true
            this.getList()
        },
        filterReset () {
            this.params.name = ''
            this.params.bride_satusehat = ''
            this.params.bride_satusehat_name = ''
            this.params.offset = 0
            this.params.limit = this.perPage
            this.getList()
        },

        getDepartmentBride() {
            // this.doctorFilterReady = false
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            this.searchTimeout = setTimeout(() => {
                if (this.requestCancelToken) {
                    this.requestCancelToken.cancel("Request sebelumnya dibatalkan.")
                }

                this.requestCancelToken = axios.CancelToken.source()

                this.$store.dispatch("satusehat/getDepartmentBride", { params: this.bride, cancelToken: this.requestCancelToken.token })
                .then((res) => {
                    console.log(res)
                    if (res.total >= 1) {
                        let data = res.entry[0].resource
                        this.form.bride_satusehat = data.id
                        this.form.bride_satusehat_name = data.name[0].text
                    } else if (res.id) {
                        let data = res
                        this.form.bride_satusehat = data.id
                        this.form.bride_satusehat_name = data.name[0].text
                    }
                })
                .catch((err) => {
                    if (axios.isCancel(err)) {
                        console.log("Request dibatalkan:", err.message)
                    } else {
                        console.error("Error saat request:", err)
                    }
                })
            }, 300)
        },
        
        getList() {
            // this.doctorFilterReady = false
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            this.searchTimeout = setTimeout(() => {
                if (this.requestCancelToken) {
                    this.requestCancelToken.cancel("Request sebelumnya dibatalkan.")
                }

                this.requestCancelToken = axios.CancelToken.source()

                this.$store.dispatch("satusehat/getDepartment", { params: this.params, cancelToken: this.requestCancelToken.token })
                .then((res) => {
                    this.department_list = res.rows
                    this.totalRows = res.iTotal
                })
                .catch((err) => {
                    if (axios.isCancel(err)) {
                        console.log("Request dibatalkan:", err.message)
                    } else {
                        console.error("Error saat request:", err)
                    }
                })
            }, 300)
        },
    },
    mounted() {
        this.show = false
    },
    created () {
        this.getList()
        this.show = true
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>