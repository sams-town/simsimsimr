<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Setting' },
                { text: 'API Credentials' },
            ]"
            >
            <section class="d-flex mt-20">
                <b-button pill @click="toggleMainTab('list')"
                    :variant="active.main_tab === 'list' ? 'primary-500' : 'outline-primary-500'" class="mr-2">List
                </b-button>
                <!-- <b-button pill @click="toggleMainTab('add')"
                    :variant="active.main_tab === 'add' ? 'primary-500' : 'outline-primary-500'">Tambah Data</b-button> -->
            </section>

            <section class="mt-24">
                <!-- LIST -->
                <div v-if="active.main_tab === 'list'">
                    <section class="mt-32">
                        <div class="row mx-0">
                            <b-form-group label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                                <span class="text-regular-14">Tampilkan</span>
                                <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
                                <span class="text-regular-14">Entri</span>
                            </b-form-group>
                        </div>
                        <b-table bordered fixed small head-variant="light" :items="credential_list" :fields="credentialFields" :per-page="0" :current-page="currentPage" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                            <template #cell(is_active)="data">
                                <b-badge :variant="status[1][data.value]">
                                    {{ status[0][data.value] }}
                                </b-badge>
                            </template>
                            <template #cell(action)="row">
                                <b-button @click="onEdit(row.item)" variant="warning-500" size="sm" class="mr-1">
                                    <feather-icon icon="EditIcon" />
                                </b-button>
                                <b-button @click="rowOnToggleActive(row.item)" :variant="row.item.is_active == 1 ? 'danger-500' : 'success-500'" size="sm">
                                    <feather-icon :icon="row.item.is_active == 1 ? 'XIcon' : 'CheckIcon'" />
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
                                    <template v-else>{{ totalRows }}</template>
                                    dari {{ totalRows }} entri
                                </span>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- ADD / EDIT -->
                <div v-if="active.main_tab === 'add'">
                    <div class="card">
                        <div class="card-header font-weight-bold bg-primary-200">
                            {{ form.id ? 'Edit Data' : 'Tambah Data' }}
                        </div>
                        <b-overlay :show="show" spinner-variant="primary" spinner-type="grow" spinner-small rounded="sm">
                            <div class="card-body">
                                <div class="col-lg-10">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <label class="c-form-label">Provider</label>
                                            <b-form-input v-model="form.provider" placeholder="satusehat / vclaim" autocomplete="off"/>
                                        </div>
                                        <div class="col-lg-4">
                                            <label class="c-form-label">Type</label>
                                            <b-form-input v-model="form.type" placeholder="oauth2 / hmac-signature" autocomplete="off"/>
                                        </div>
                                        <div class="col-lg-4">
                                            <label class="c-form-label">Status</label>
                                            <b-form-checkbox v-model="form.is_active" switch class="mt-1">
                                                {{ form.is_active ? 'Aktif' : 'Nonaktif' }}
                                            </b-form-checkbox>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-lg-6">
                                            <label class="c-form-label">Auth URL</label>
                                            <b-form-input v-model="form.auth_url" autocomplete="off"/>
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="c-form-label">Base URL</label>
                                            <b-form-input v-model="form.base_url" autocomplete="off"/>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-lg-6">
                                            <label class="c-form-label">Client ID</label>
                                            <b-form-input v-model="form.client_id" autocomplete="off"/>
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="c-form-label">Client Secret</label>
                                            <b-form-input v-model="form.client_secret" autocomplete="off"/>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-lg-6">
                                            <label class="c-form-label">Organization ID</label>
                                            <b-form-input v-model="form.organization_id" autocomplete="off"/>
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="c-form-label">Location ID</label>
                                            <b-form-input v-model="form.location_id" autocomplete="off"/>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-lg-4">
                                            <label class="c-form-label">Cons ID</label>
                                            <b-form-input v-model="form.cons_id" autocomplete="off"/>
                                        </div>
                                        <div class="col-lg-4">
                                            <label class="c-form-label">PPK RS</label>
                                            <b-form-input v-model="form.ppkrs" autocomplete="off"/>
                                        </div>
                                        <div class="col-lg-4">
                                            <label class="c-form-label">User Key</label>
                                            <b-form-input v-model="form.user_key" autocomplete="off"/>
                                        </div>
                                    </div>

                                    <b-col cols="12" class="px-0 mt-24">
                                        <b-button type="submit" variant="primary" class="mr-1" @click.prevent="validationForm"> Submit </b-button>
                                        <b-button type="reset" variant="outline-secondary" @click.prevent="resetForm"> Reset </b-button>
                                    </b-col>
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
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import axios from 'axios'

export default {
    name: 'apiCredentialMaster',
    components: { ToastificationContent },
    data() {
        return {
            active: { main_tab: 'list' },

            credentialFields: [
                { key: 'action', label: 'Aksi', thStyle: { width: '150px' } },
                { key: 'provider', label: 'Provider', sortable: true },
                { key: 'type', label: 'Type', sortable: true },
                { key: 'base_url', label: 'Base URL', sortable: false },
                { key: 'is_active', label: 'Status', sortable: true },
            ],

            params: {
                offset: 0,
                limit: 20,
                provider: '',
                type: '',
            },

            currentPage: 1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            totalRows: 0,
            pageOptions: [20, 50, 100],

            credential_list: [],

            form: {
                id: null,
                provider: '',
                type: '',
                auth_url: '',
                base_url: '',
                client_id: '',
                client_secret: '',
                organization_id: '',
                location_id: '',
                cons_id: '',
                ppkrs: '',
                user_key: '',
                is_active: true,
            },

            show: false,

            status: [
                { 0: 'Nonaktif', 1: 'Aktif' },
                { 0: 'danger', 1: 'primary' },
            ],
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
            handler: function () {
                this.params.limit = this.perPage
                this.getList()
            }
        },
    },
    methods: {
        toggleMainTab(tab) {
            this.active.main_tab = tab
            if (tab === 'add' && !this.form.id) {
                this.resetForm()
            }
        },
        resetForm() {
            this.form = {
                id: null,
                provider: '',
                type: '',
                auth_url: '',
                base_url: '',
                client_id: '',
                client_secret: '',
                organization_id: '',
                location_id: '',
                cons_id: '',
                ppkrs: '',
                user_key: '',
                is_active: true,
            }
        },
        validationForm() {
            if (!this.form.provider || !this.form.type) {
                this.notification('Failed', 'AlertTriangleIcon', 'danger', 'Provider dan Type wajib diisi')
                return
            }
            this.show = true
            this.$store.dispatch('setting/postPutCredential', this.form)
                .then(() => {
                    this.notification('Success', 'CheckIcon', 'success', 'Data berhasil disimpan')
                    this.getList()
                    this.active.main_tab = 'list'
                    this.resetForm()
                    this.show = false
                }, () => {
                    this.show = false
                    this.notification('Failed', 'CloseIcon', 'danger', 'Data gagal disimpan')
                })
        },
        rowOnToggleActive(item) {
            const willActivate = item.is_active != 1
            const actionText = willActivate ? 'mengaktifkan' : 'menonaktifkan'
            this.$bvModal
                .msgBoxConfirm(`Konfirmasi untuk ${actionText} kredensial "${item.provider}".`, {
                    title: 'Please Confirm', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
                })
                .then(value => {
                    if (value === true) {
                        this.$store.dispatch('setting/updateStatus', item)
                            .then(() => {
                                this.getList()
                                this.notification('Success', 'CheckIcon', 'success', `Berhasil ${actionText} kredensial`)
                            }, () => {
                                this.notification('Failed', 'CloseIcon', 'danger', `Gagal ${actionText} kredensial`)
                            })
                    }
                })
        },
        notification(a, b, c, d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d }
            })
        },
        onEdit(obj) {
            this.active.main_tab = 'add'
            this.form = {
                id: obj.id,
                provider: obj.provider,
                type: obj.type,
                auth_url: obj.auth_url,
                base_url: obj.base_url,
                client_id: obj.client_id,
                client_secret: obj.client_secret,
                organization_id: obj.organization_id,
                location_id: obj.location_id,
                cons_id: obj.cons_id,
                ppkrs: obj.ppkrs,
                user_key: obj.user_key,
                is_active: obj.is_active == 1,
            }
            this.show = false
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

                this.$store.dispatch("setting/getAll", { params: this.params, cancelToken: this.requestCancelToken.token })
                .then((res) => {
                    this.credential_list = res.rows
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
    created() {
        this.getList()
        this.show = true
    },
    mounted() {
        this.show = false
    }
}
</script>