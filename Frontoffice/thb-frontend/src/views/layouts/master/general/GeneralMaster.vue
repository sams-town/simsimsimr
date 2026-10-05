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
                                <div class="col-lg-4">
                                    <div class="mt-12">
                                        <label class="c-form-label">Kode</label>
                                        <b-form-input v-model="params.code" autocomplete="off" />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Nama General</label>
                                        <b-form-input v-model="params.name" autocomplete="off" />
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
                            <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect"
                                class="text-nowrap mb-md-0 mr-1">
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
                                    <b-pagination v-model="currentPage" :total-rows="totalRows" :per-page="perPage"
                                        align="fill" size="sm" class="my-0"></b-pagination>
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
                        <div class="">
                            <div class="col-lg-10">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mt-12">
                                            <label class="c-form-label">Nama General</label>
                                            <b-form-input v-model="form.name" autocomplete="off" />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Kode</label>
                                            <b-form-input v-model="form.code" autocomplete="off" />
                                        </div>
                                    </div>
                                </div>
                                <b-col cols="12" class="px-0 mt-24">
                                    <b-button type="submit" variant="primary" class="mr-1" :disabled="isDisabled"
                                        @click.prevent="validationForm"> Submit </b-button>
                                    <b-button @click="ResetForm" type="reset" variant="outline-secondary"> Reset </b-button>
                                </b-col>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
export default {
    name: 'GeneralMaster',
    data() {
        return {
            active: {
                main_tab: 'list',
            },
            params: {
                offset: 0,
                limit: 20,
                name: '',
                code: ''
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
            form: {
                id: null,
                name: '',
                code: ''
            }
        }
    },
    watch: {
        currentPage: {
            handler: function (value) {
                this.firstRows = this.perPage * (value - 1) + 1
                this.lastRows = this.perPage * (value) + 1
                this.params.offset = this.perPage * (value - 1)
                this.params.limit = this.perPage
                this.$store.dispatch('general/getAll', this.params)
            }
        },
        perPage: {
            handler: function (value) {
                this.params.limit = this.perPage
                this.$store.dispatch('general/getAll', this.params)
            }
        },
    },
    computed: {
        ...mapState({
            start: state => state.general.start,
            totalRows: state => state.general.totalRows,
            fields: state => state.general.fields,
            items: state => state.general.items,
            pageOptions: state => state.general.pageOptions,
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
        rowOnDelete(id) {
            this.$bvModal
                .msgBoxConfirm('Please confirm that you want to delete everything.', {
                    title: 'Please Confirm', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
                })
                .then(value => {
                    if (value === true) {
                        this.$store.dispatch('general/remove', id)
                            .then(response => {
                                if (response.status === 200) {
                                    this.$store.dispatch('general/getAll', this.params)
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
            this.form.name = ''
            this.form.code = ''
            this.params.code = ''
            this.params.name = ''
        },
        validationForm() {
            this.show = true
            this.isDisabled = true
            const formData = this.form
            this.$store.dispatch('general/formPostPut', formData)
                .then(res => {
                    this.notification('Success', 'CheckIcon', 'success', 'You have successfully')
                    this.$store.dispatch('general/getAll', this.params)
                    this.active.main_tab = 'list'
                    this.form.id = ''
                    this.form.name = ''
                    this.form.code = ''
                    this.show = false
                    this.isDisabled = false
                },
                    err => {
                        this.show = false
                        this.isDisabled = false
                        this.notification('Failed', 'CloseIcon', 'danger', 'You have unsuccessful')
                    })
        },

        ResetForm() {
            this.form.name = ''
            this.form.code = ''
        },
        filterData() {
            this.show = true
            this.isDisabledFilter = true
            this.$store.dispatch('general/getAll', this.params)
                .then(res => {
                    this.show = false
                    this.isDisabledFilter = false
                },
                    err => {
                        this.show = false
                        this.isDisabledFilter = false
                    })
        },
        filterReset() {
            this.params.name = ''
            this.params.code = ''
            this.params.offset = 0
            this.params.limit = this.perPage
            this.$store.dispatch('general/getAll', this.params)
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
            this.form.name = obj.name
            this.form.code = obj.code
            this.show = false
        },
    },
    mounted() {
        this.$store.state.totalRows = this.items.length
        this.show = false
    },
    created() {
        this.$store.dispatch('general/getAll', this.params)
        this.show = true
    },
}

</script>