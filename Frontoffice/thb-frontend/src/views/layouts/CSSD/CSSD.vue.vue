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
                            <div class="col-lg-7">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label class="c-form-label">Nomor Transaksi</label>
                                        <b-form-input v-model="params.transaction_number" autocomplete="off"/>
                                    </div>
                                    <div class="col-lg-6 mt-12 mt-lg-0">
                                        <label class="c-form-label">Unit</label>
                                        <b-form-input v-model="params.unit" autocomplete="off"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 mt-12">
                                        <label class="c-form-label">Jenis Transaksi</label>
                                        <v-select v-model="params.transaction_type" aria-readonly="true" :options="options_transaction_type" :reduce="options_transaction_type => options_transaction_type.value" label="title" placeholder="-- Jenis transaksi --"></v-select>
                                    </div>
                                    <div class="col-lg-6 mt-12">
                                        <label class="c-form-label">Status</label>
                                        <v-select v-model="params.status" aria-readonly="true" :options="options_status" :reduce="options_status => options_status.value" label="title" placeholder="-- Status --"></v-select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <div class="col-lg mt-12">
                                                <label class="c-form-label">Tanggal</label>
                                                <b-form-input v-model="params.date_start" type="date"/>
                                            </div>
                                            <div class="col-lg mt-12">
                                                <label class="c-form-label">S.d</label>
                                                <b-form-input v-model="params.date_end" type="date"/>
                                            </div>
                                        </div>
                                    </div>
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
                        <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                            <span class="text-regular-14">Tampilkan</span>
                            <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
                            <span class="text-regular-14">Entri</span>
                        </b-form-group>
                    </div>
                    <b-table bordered responsive hover small head-variant="light" :items="items" :fields="fields" :per-page="0" :current-page="currentPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                        <template #cell(action)="row">
                            <div class="d-flex">
                                <b-button variant="success-500" size="sm" class="mr-1">
                                    <feather-icon icon="PrinterIcon" />
                                </b-button>
                                <b-button variant="info-500" size="sm">
                                    <feather-icon icon="ListIcon" />
                                    <b-link v-b-modal.modal-center>
                                    </b-link>
                                </b-button>
                            </div>
                        </template>
                        <template #cell(send_verification)="data">
                            <div class="text-regular-16">
                                <b-badge class="p-2 font-weight-normal" :variant="data.value == 'Terkirim' ? 'success-500' : 'danger-600'">{{ data.value }}</b-badge>
                            </div>
                        </template>
                        <template #cell(receiver_verification)="data">
                            <div class="text-regular-16">
                                <b-badge class="p-2 font-weight-normal" :variant="data.value == 'Diterima' ? 'success-500' : 'danger-600'">{{ data.value }}</b-badge>
                            </div>
                        </template>
                        <template #cell(transaction_status)="data">
                            <div class="text-regular-16">
                                <b-badge class="p-2 font-weight-normal" v-if="data.value == 'Tidak Terpenuhi / Dikembalikan'" variant="danger-500">{{ data.value }}</b-badge>
                                <b-badge class="p-2 font-weight-normal" v-if="data.value == 'Pending'" variant="warning-500">{{ data.value }}</b-badge>
                                <b-badge class="p-2 font-weight-normal" v-if="data.value == 'Terpenuhi / Diproses'" variant="success-500">{{ data.value }}</b-badge>
                                <b-badge class="p-2 font-weight-normal" v-if="data.value == 'Selesai'" variant="info-500">{{ data.value }}</b-badge>
                            </div>
                        </template>
                        <template #cell(transaction_type)="data">
                            <div class="text-regular-16">
                                <b-badge class="p-2 font-weight-normal" :variant="data.value == 'Dikirimkan / Dikembalikan' ? 'warning-500' : 'success-600'">{{ data.value == 'Dikirimkan / Dikembalikan' ? 'Retur' : data.value }}</b-badge>
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
                    <div class="card-header font-weight-bold bg-primary-200">Data Transaksi</div>
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
                                        <div class="col-lg-6">
                                            <div class="mt-12">
                                                <label class="c-form-label">No Transaksi</label>
                                                <b-form-input v-model="form.transaction_number" autocomplete="off"/>
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label">Jenis transaksi</label>
                                                <v-select v-model="form.transaction_type" aria-readonly="true" :options="options_transaction_type" :reduce="options_transaction_type => options_transaction_type.value" label="title" placeholder="-- Jenis transaksi --"></v-select>
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label">Tanggal</label>
                                                <b-form-input v-model="form.date" type="datetime-local" autocomplete="off"/>
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label">Keterangan</label>
                                                <b-textarea v-model="form.notes" rows="3" autocomplete="off"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mt-12">
                                                <label class="c-form-label">Unit Pengirim</label>
                                                <v-select v-model="form.sender_unit" aria-readonly="true" :options="options_transaction_type" :reduce="options_transaction_type => options_transaction_type.value" label="title" placeholder="-- Unit pengirim --"></v-select>
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label">Unit Tujuan</label>
                                                <v-select v-model="form.destination_unit" aria-readonly="true" :options="options_transaction_type" :reduce="options_transaction_type => options_transaction_type.value" label="title" placeholder="-- Unit tujuan --"></v-select>
                                                <b-input-group class="mt-10">
                                                    <b-input-group-prepend is-text>
                                                        <input type="checkbox" value="true" v-model="form.trigger_check">
                                                    </b-input-group-prepend>
                                                    <div class="col px-0">
                                                        <b-form-input v-model="form.date_time_now" :disabled="form.trigger_check == false" type="datetime-local" />
                                                        <b-form-input v-model="form.user_act" :disabled="form.trigger_check == false"/>
                                                    </div>
                                                </b-input-group>
                                            </div>
                                        </div>
                                    </div>
                                    <b-col cols="12" class="px-0 mt-24">
                                        <b-button type="submit" variant="primary" class="mr-1" @click.prevent="validationForm"> Simpan </b-button>
                                        <b-button type="reset" variant="outline-secondary"> Reset </b-button>
                                    </b-col>
                                </div>
                            </div>
                        </div>
                    </b-overlay>
                </div>
                <div class="card mt-32">
                    <div class="card-header font-weight-bold bg-primary-200">
                        <div class="w-100 d-flex justify-content-between">
                            <span>Jumlah Item Barang = 0</span>
                            <b-button variant="primary-500" size="sm"><feather-icon icon="MinusIcon" /></b-button>
                        </div>
                    </div>
                    <div class="card-body">
                        <b-table-simple>
                            <b-thead class="text-bold-14">
                                <b-tr>
                                    <b-th>
                                        <input type="checkbox" value="true"> No  
                                    </b-th>
                                    <b-th>Kode</b-th>
                                    <b-th>Nama barang</b-th>
                                    <b-th>Qty / Jml</b-th>
                                </b-tr>
                            </b-thead>
                            <b-tbody class="text-regular-12">
                                <b-tr>
                                    <b-td>
                                        <input type="checkbox" value="true"> 1 
                                    </b-td>
                                    <b-td>-</b-td>
                                    <b-td>
                                        <v-select v-model="goods_item.goods_name" aria-readonly="true" :options="options_dummy" :reduce="options_dummy => options_dummy.value" label="title" placeholder="-- Nama barang --"></v-select>
                                    </b-td>
                                    <b-td>
                                        <b-form-input v-model="goods_item.goods_number"/>
                                    </b-td>
                                </b-tr>
                            </b-tbody>
                        </b-table-simple>
                    </div>
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
    name: 'CatalogMaster',
    components: { ToastificationContent, vSelect, },
    data() {
        return {
            currentPage:1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            isDisabled: false,
            isDisabledFilter: false,
            show: false,
            options_transaction_type:[
                { value: 1, title: 'Permintaan' },
                { value: 2, title: 'Dikirim / Dikembalikan' },
            ],
            options_status:[
                { value: 1, title: 'Tidak Terpenuhi / Dikembalikan' },
                { value: 2, title: 'Pending' },
                { value: 3, title: 'Terpenuhi / Diproses' },
                { value: 4, title: 'Selesai' },
            ],
            options_dummy:[
                { value: 1, title: 'Dummy 1' },
                { value: 2, title: 'Dummy 2' },
                { value: 3, title: 'Dummy 3' },
            ],
            active: {
                main_tab: 'list',
            },
            params:{
                offset: 0,
                limit: 20,
                transaction_number: '',
                unit: '',
                transaction_type:'',
                date_start: '',
                date_end: '',
            },
            form: {
                id: null,
                transaction_number: '',
                transaction_type:'',
                date: '',
                notes: '',
                sender_unit: '',
                destination_unit: '',
                trigger_check: false,
                date_time_now: '',
                user_act: '',
            },
            goods_item: {
                goods_name: '',
                goods_number: 0,
            }

        }
    },
    computed: {
        ...mapState({ 
            totalRows: state => state.cssd.totalRows, 
            fields: state => state.cssd.fields, 
            items: state => state.cssd.items, 
            pageOptions: state => state.cssd.pageOptions, 
        }),
    },
    watch: {
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.$store.dispatch('cssd/getAll',this.params)
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.$store.dispatch('cssd/getAll',this.params)
            }
        },
        'form.trigger_check' (value) {
            if (value == true) {
                this.form.date_time_now = new Date().toISOString().slice(0, 19).replace('T', ' ')
                this.form.user_act = 'Admin'
            } else {
                this.form.date_time_now = ''
                this.form.user_act = ''
            }
        }
    },
    methods: {
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        toggleMainTab(tab) {
            this.active.main_tab = tab
        },
        filterData() {
            console.log('Filter clicked')
            // this.show = true
            // this.isDisabledFilter = true
            // this.$store.dispatch('cssd/getAll', this.params)
            // .then(res => {
            //     this.show = false
            //     this.isDisabledFilter = false
            // },
            // err => {
            //     this.show = false
            //     this.isDisabledFilter = false
            // })
        },
        filterReset() {
            console.log('Reset clicked')
            this.params.offset = 0
            this.params.limit = this.perPage
            this.params.transaction_number = ''
            this.params.unit = ''
            this.params.transaction_type = ''
            this.params.date_start = ''
            this.params.date_end = ''
            // this.$store.dispatch('cssd/getAll',this.params)
        },
        validationForm(){
            console.log('Validation clicked')
        }
    },
    mounted() {
        this.$store.state.totalRows = this.items.length
        this.show = false
    },
    created () {
        this.$store.dispatch('catalog/getAll',this.params)
        this.show = true
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>