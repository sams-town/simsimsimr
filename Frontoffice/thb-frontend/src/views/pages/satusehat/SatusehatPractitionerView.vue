<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'SATUSEHAT' },
                { text: 'PRACTITIONER' },
                // { text: this.form_transaction.no_order }
            ]"
            >
            <TopSatusehatMenu selectedMenu="menu2" class="mt-2" />
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
                                    <div class="mt-12">
                                        <label class="c-form-label">Fasititas Kesehatan</label>
                                        <v-select v-model="params.health_facility_id" :options="OptionsHealthFacility" :reduce="OptionsHealthFacility => OptionsHealthFacility.value" label="title" placeholder="-- Fasilitas Kesehatan --"></v-select>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Jabatan</label>
                                        <v-select v-model="params.position_id" :options="OptionsPosition" :reduce="OptionsPosition => OptionsPosition.value" label="title" placeholder="-- Jabatan --"></v-select>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Nama Pegawai</label>
                                        <b-form-input v-model="params.fullname"></b-form-input>
                                    </div>
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
                        <b-table bordered fixed small head-variant="light" :items="practitioner_list" :fields="practitionerFields" :per-page="0" :current-page="currentPage" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
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
                                <div class="d-flex justify-content-center">
                                    <div class="col-lg-10">
                                        <div class="row">
                                            <div class="col-lg-2">
                                                <div class="">
                                                    <label class="c-form-label">Nama Depan (title)</label>
                                                    <b-form-input v-model="form.f_title" autocomplete="off" placeholder="misal: Ir / Drs"/>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="mt-12 mt-lg-0">
                                                    <label class="c-form-label">Nama</label>
                                                    <b-form-input v-model="form.name" autocomplete="off"/>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="mt-12 mt-lg-0">
                                                    <label class="c-form-label">Nama Belakang (title)</label>
                                                    <b-form-input v-model="form.b_title" autocomplete="off" placeholder="misal: S.kom"/>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="mt-12 mt-lg-0">
                                                    <label class="c-form-label">Nama Lengkap</label>
                                                    <b-form-input v-model="form.fullname" disabled :value="this.fullname_group" />
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="mt-12 mt-lg-0">
                                                    <label class="c-form-label">Nickname</label>
                                                    <b-form-input v-model="form.nickname" :value="this.fullname_group" autocomplete="off"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col">
                                                <div class="mt-12">
                                                    <label class="c-form-label">Alamat</label>
                                                    <b-form-input v-model="form.address" autocomplete="off"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mt-12">
                                                    <label class="c-form-label">Jenis Kelamin</label>
                                                    <v-select v-model="form.sex" aria-readonly="true" :options="OptionsSex" :reduce="OptionsSex => OptionsSex.value" label="title" placeholder="-- Jenis Kelamin --"></v-select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mt-12">
                                                    <label class="c-form-label">Jabatan</label>
                                                    <v-select v-model="form.position_id" aria-readonly="true" :options="OptionsPosition" :reduce="OptionsPosition => OptionsPosition.value" label="title" placeholder="-- Jabatan --"></v-select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mt-12">
                                                    <label class="c-form-label">Tanggal Lahir</label>
                                                    <b-form-input v-model="form.birth_date" type="date" autocomplete="off"/>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mt-12">
                                                    <label class="c-form-label">Masa Berakhir Jabatan</label>
                                                    <b-form-input v-model="form.date_expired" type="date" autocomplete="off"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mt-3">
                                                    <label class="c-form-label">NIK/ID Praktisi</label>
                                                    <b-form-input v-model="bride.search" @input="searchPractitionerBride" autocomplete="off"/>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mt-3">
                                                    <label class="c-form-label">ID Satusehat</label>
                                                    <b-form-input v-model="form.bride_satusehat" readonly="true" autocomplete="off"/>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mt-3">
                                                    <label class="c-form-label">Nama</label>
                                                    <b-form-input v-model="form.bride_satusehat_name" readonly="true" autocomplete="off"/>
                                                </div>
                                            </div>
                                        </div>
                                        <b-col cols="12" class="px-0 mt-24">
                                            <b-button type="submit" variant="primary" class="mr-1" @click.prevent="validationForm"> Submit </b-button>
                                            <b-button type="reset" variant="outline-secondary" @click="ResetFormCreate"> Reset </b-button>
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
import TopSatusehatMenu from '@/@core/components/top-menu/TopSatusehatMenu.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import axios from 'axios'
export default {
    components: { vSelect,TopSatusehatMenu },
    name: 'EmployeeMaster',
    data() {
        return {
            active: {
                main_tab: 'list',
            },

            practitionerFields: [
                { key: 'action', label: 'Aksi', thStyle: { width: '150px' },},
                { key: 'bride_satusehat', label: 'Kode Praktisi', sortable: true },
                { key: 'fullname', label: 'Nama Praktisi', sortable: true },
            ],
            
            params: {
                limit: 20,
                offset: 0,
                name: '',
            },

            currentPage: 1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            totalRows: 0,
            pageOptions: [20, 50, 100],
            
            practitioner_list:[],
            bride: {search:''},
            
            form: {
                id:null,
                f_title:'',
                name: '',
                nickname: '',
                b_title:'',
                fullname:'',
                sex:'',
                address:'',
                position_id:'',
                date_expired:'',
                bride_satusehat:'',
                bride_satusehat_name:'',
                birth_date:''
            },

            requestCancelToken: null,
            searchTimeout: null,

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
        ...mapState({
            OptionsSex: state => state.helper.options.sex,
            OptionsPosition: state => state.position.options.position,
            OptionsHealthFacility: state => state.healthfacility.options.healthfacility,
        }),
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
        },
        fullname_group () {
            let groups = []
            if (this.form.f_title) {
                groups.push(this.form.f_title + '.')
            }

            if (this.form.name && this.form.b_title) {
                groups.push(this.form.name + ',')
            } else if (this.form.name) {
                groups.push(this.form.name)
            }

            if (this.form.b_title) {
                groups.push(this.form.b_title)
            }

            let final_groups = groups.join(' ')
            return this.form.fullname = final_groups
        },
        fullname_group () {
            let groups = []
            if (this.form.f_title) {
                groups.push(this.form.f_title + '.')
            }

            if (this.form.name && this.form.b_title) {
                groups.push(this.form.name + ',')
            } else if (this.form.name) {
                groups.push(this.form.name)
            }

            if (this.form.b_title) {
                groups.push(this.form.b_title)
            }

            let final_groups = groups.join(' ')
            return this.form.nickname = final_groups
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
                this.$store.dispatch('employee/remove', id)
                .then(response => {
                    if (response.status === 200) {
                    this.getList()
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
            this.params.fullname = ''
            this.params.health_facility_id = ''
            this.params.position_id = ''
            this.form.id = ''
            this.form.f_title = ''
            this.form.name = ''
            this.form.b_title = ''
            this.form.fullname = ''
            this.form.nickname = ''
            this.form.sex = ''
            this.form.address = ''
            this.form.position_id = ''
            this.form.date_expired = ''
            this.form.birth_date = ''
            this.form.bride_satusehat = ''
            this.form.bride_satusehat_name = ''
        },
        validationForm () {
            this.show = true
            const formData = this.form
            this.$store.dispatch('employee/formPostPut', formData)
            .then(res => {
                this.notification('Success','CheckIcon','success','You have successfully')
                this.getList()
                this.active.main_tab = 'list'
                this.form.id = ''
                this.f_title = ''
                this.name = ''
                this.b_title = ''
                this.form.fullname = ''
                this.form.nickname = ''
                this.form.sex = ''
                this.form.address = ''
                this.form.position_id = ''
                this.form.date_expired = ''
                this.form.birth_date = ''
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
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        onEdit (obj) {
            this.active.main_tab = 'add'
            this.form.id = obj.id
            this.form.f_title = obj.f_title
            this.form.name = obj.name
            this.form.b_title = obj.b_title
            this.form.fullname = obj.fullname
            this.form.nickname = obj.nickname
            this.form.sex = obj.sex
            this.form.address = obj.address
            this.form.position_id = obj.position_id
            this.form.date_expired = obj.position_date_expired
            this.form.birth_date = obj.birth_date
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
            this.params.fullname = ''
            this.params.health_facility_id = ''
            this.params.position_id = ''
            this.params.offset = 0
            this.params.limit = this.perPage
            this.$store.dispatch('employee/getAll',this.params)
        },
        ResetFormCreate () {
            this.form.f_title = ''
            this.form.name = ''
            this.form.b_title = ''
            this.form.fullname = ''
            this.form.nickname = ''
            this.form.address = ''
            this.form.sex = ''
            this.form.position_id = ''
            this.form.birth_date = ''
            this.form.date_expired = ''
            this.form.bride_satusehat = ''
            this.form.bride_satusehat_name = ''
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

                this.$store.dispatch("satusehat/getPractitioner", { params: this.params, cancelToken: this.requestCancelToken.token })
                .then((res) => {
                    this.practitioner_list = res.rows
                    this.totalRows = res.iTotal
                    // console.log('this.practitioner_list', res);                    
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

        searchPractitionerBride() {
            // this.doctorFilterReady = false
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            this.searchTimeout = setTimeout(() => {
                if (this.requestCancelToken) {
                    this.requestCancelToken.cancel("Request sebelumnya dibatalkan.")
                }

                this.requestCancelToken = axios.CancelToken.source()

                this.$store.dispatch("satusehat/getPractitionerBride", { params: this.bride, cancelToken: this.requestCancelToken.token })
                .then((res) => {
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
        }
    },
    mounted() {
    },
    created () {
        this.getList()
        this.$store.dispatch('position/getParam', 'position')
        this.$store.dispatch('healthfacility/getParam', 'healthfacility')
        this.$store.dispatch('helper/getParam', 'sex')
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>