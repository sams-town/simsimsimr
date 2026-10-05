<template>
    <div>
        <TopCoaMenu selectedMenu="menu2" />
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
                    <!-- <section class="card">
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
                    </section> -->
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
                                                    <b-form-input v-model="form.account" autocomplete="off"/>
                                                </div>
                                                <div class="mt-12">
                                                    <label class="c-form-label">Klasifikasi</label>
                                                    <b-form-input v-model="form.name" autocomplete="off"/>
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
import TopCoaMenu from '@/@core/components/top-menu/TopCoaMenu.vue'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
export default {
    components: { TopCoaMenu },
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
                name:'',
                account:''
            }
        }
    },
    computed: {
        ...mapState({ 
            totalRows: state => state.classification.totalRows, 
            fields: state => state.classification.fields, 
            items: state => state.classification.items, 
            perPage: state => state.classification.perPage, 
            pageOptions: state => state.classification.pageOptions, 
            firstRow: state => state.classification.firstRow, 
            lastRow: state => state.classification.lastRow, 
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
        rowOnDelete (id) {
            this.$bvModal
                .msgBoxConfirm('Please confirm that you want to delete everything.', {
                title: 'Please Confirm', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                this.$store.dispatch('removeclassification', id)
                .then(response => {
                    if (response.status === 200) {
                    this.$store.dispatch('classification/getAll','')
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
            this.form.account = ''
        },
        validationForm () {
            this.show = true
            const formData = this.form 
            this.$store.dispatch('classification/formPostPut', formData)
            .then(res => {
                this.notification('Success','CheckIcon','success','You have successfully')
                this.$store.dispatch('classification/getAll','')
                this.active.main_tab = 'list'
                this.form.id = ''
                this.form.name = ''
                this.form.account = ''
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
            this.form.name = obj.name
            this.form.account = obj.account
            this.show = false
        },
    },
    mounted() {
        // Set the initial number of items
        this.$store.state.totalRows = this.items.length
    },
    created () {
        this.$store.dispatch('classification/getAll','')
    }
}
</script>