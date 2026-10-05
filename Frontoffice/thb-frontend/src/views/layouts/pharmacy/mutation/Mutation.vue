<template>
    <div>
        <TopPharmacyMenu v-if="checkrouter != 'PharmacyMutationUnitView'" selectedMenu="menu5" />
        <section class="mt-24 d-flex">
            <b-button pill @click="toggleMainTab('list')"
                :variant="active.main_tab === 'list' ? 'primary-500' : 'outline-primary-500'" class="mr-2">List
            </b-button>
            <b-button pill @click="toggleMainTab('add')"
                :variant="active.main_tab === 'add' ? 'primary-500' : 'outline-primary-500'">Tambah Data</b-button>
        </section>
        <section class="mt-24">
            <div v-if="active.main_tab === 'list'">
                <section class="mt-32">
                    <div class="card">
                        <div class="card-header bg-primary-200 fw-bold">Filter Data</div>
                        <div class="card-body">
                            <div class="d-flex justify-content-center">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-4 mt-1">
                                            <div>
                                                <label class="c-form-label">No Dokumen</label>
                                                <b-form-input v-model="params.no_document" />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mt-1">
                                            <div>
                                                <label class="c-form-label">Unit Pengirim</label>
                                                <v-select v-model="params.pharmacy_depo_send_id" :options="DepoOptions" :reduce="DepoOptions => DepoOptions.value" label="title" placeholder="-- Pilih Unit Pengirim --"></v-select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mt-1">
                                            <div>
                                                <label class="c-form-label">Unit Penerima/Permintaan</label>
                                                <v-select v-model="params.pharmacy_depo_recive_id" :options="DepoOptions" :reduce="DepoOptions => DepoOptions.value" label="title" placeholder="-- Pilih Unit Penerima/Permintaan --"></v-select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mt-1">
                                            <label class="c-form-label">Tanggal Mutasi</label>
                                            <b-input-group>
                                                <b-form-input id="example-input" v-model="params.date_start"
                                                    type="text" placeholder="YYYY-MM-DD"
                                                    autocomplete="off"></b-form-input>
                                                <b-input-group-append>
                                                    <b-form-datepicker v-model="params.date_start" button-only
                                                        right locale="en-US"
                                                        aria-controls="example-input"></b-form-datepicker>
                                                </b-input-group-append>
                                                <b-form-input id="example-inputx" v-model="params.date_end"
                                                    type="text" placeholder="YYYY-MM-DD"
                                                    autocomplete="off"></b-form-input>
                                                <b-input-group-append>
                                                    <b-form-datepicker v-model="params.date_end" button-only
                                                        right locale="en-US"
                                                        aria-controls="example-inputx"></b-form-datepicker>
                                                </b-input-group-append>
                                            </b-input-group>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-20">
                                        <!-- <b-button pill variant="outline-primary" @click="filterReset" :disabled="isDisabledFilter" class="px-3 mr-2">Cancel</b-button> -->
                                        <b-button pill variant="primary" @click="filterData" :disabled="isDisabledFilter" class="px-3">Filter</b-button>
                                    </div>
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
                        <template #cell(no_document)="row">
                            <b>{{ row.item.no_document }}</b><br>{{ row.item.date_added }}
                        </template>
                        <template #cell(ntotal)="data">
                            {{ data.value | currency }}
                        </template>
                        <template #cell(ndiscount)="data">
                            {{ data.value | currency }}
                        </template>
                        <template #cell(nfinish)="data">
                            {{ data.value | currency }}
                        </template>
                        <template #cell(validation_key_1)="row">
                            <template v-if="row.item.validation_key_1">
                                <b-badge variant="primary">Tervalidasi</b-badge>
                            </template>
                            <template v-else>
                                <b-badge variant="danger">Belum Tervalidasi</b-badge>
                            </template>
                        </template>
                        <template #cell(validation_key_2)="row">
                            <template v-if="row.item.validation_key_2">
                                <b-badge variant="primary">Tervalidasi</b-badge>
                            </template>
                            <template v-else>
                                <b-badge variant="danger">Belum Tervalidasi</b-badge>
                            </template>
                        </template>
                        <template #cell(validation_key_3)="row">
                            <template v-if="row.item.validation_key_3">
                                <b-badge variant="primary">Tervalidasi</b-badge>
                            </template>
                            <template v-else>
                                <b-badge variant="danger">Belum Tervalidasi</b-badge>
                            </template>
                        </template>
                        <template #cell(action)="row">
                            <div class="row mx-0">
                                <template v-if="!row.item.validation_key_1">
                                    <b-button variant="primary-500" size="sm" class="mr-1">
                                        <feather-icon icon="EyeIcon" @click="editData(row.item)" />
                                    </b-button>
                                    <b-button variant="danger-500" size="sm">
                                        <feather-icon icon="TrashIcon" @click="rowOnDelete(row.item.id)" />
                                        <b-link v-b-modal.modal-center>
                                        </b-link>
                                    </b-button>
                                </template>
                                <template v-else>
                                    <b-button variant="primary-500" size="sm" class="mr-1">
                                        <feather-icon icon="EyeIcon" @click="editData(row.item)" />
                                    </b-button>
                                    <router-link :to="{ path: '/pharmacy/mutation/print/'+ row.item.id}" v-if="row.item.validation_key_1">
                                        <b-button size="sm" variant="info-700">
                                            <feather-icon icon="PrinterIcon" />
                                        </b-button>
                                    </router-link>
                                </template>
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
                <b-overlay
                    :show="show"
                    spinner-variant="primary"
                    spinner-type="grow"
                    spinner-small
                    rounded="sm"
                    >
                    <section class="card">
                        <div class="card-header bg-primary-200 font-weight-bold">
                            <div class="d-flex justify-content-lg-between">
                                <span class="font-weight-bold">DATA MUTASI</span>
                                <div class="d-flex">
                                    <!-- <router-link :to="{ name: 'PharmacyPlanningProcurementView' }" target="_blank">
                                        <b-button variant="primary-600" size="sm" class="mr-1">
                                            <feather-icon icon="PrinterIcon" />
                                        </b-button>
                                    </router-link> -->
                                    <!-- <template v-if="this.form.validation_key_1 === null"> -->
                                        <b-button variant="primary-600" size="sm" :disabled="isDisabled" @click.prevent="validationForm" class="mr-1">
                                            <feather-icon icon="SaveIcon" /> Simpan
                                        </b-button>
                                    <!-- </template> -->
                                    <!-- <b-button variant="primary-600" size="sm">
                                        <feather-icon icon="RefreshCcwIcon" />
                                    </b-button> -->
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-center">
                                <div class="col-lg-11">
                                    <div class="row">
                                        <div class="col-lg-5">
                                            <div class="row mt-12" v-if="this.form.no_document">
                                                <div class="col-lg-3 text-lg-right">
                                                    <label>No. Dokumen</label>
                                                </div>
                                                <div class="col-lg">
                                                    {{ this.form.no_document }}
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-lg-3 text-lg-right">
                                                    <label>Jenis Transaksi</label>
                                                </div>
                                                <div class="col-lg">
                                                    <b-form-select value=1>
                                                        <b-form-select-option value="1">PERMINTAAN</b-form-select-option>
                                                        <b-form-select-option value="2">RETUR</b-form-select-option>
                                                    </b-form-select>
                                                </div>
                                            </div>
                                            <div class="row mt-12">
                                                <div class="col-lg-3 text-lg-right">
                                                    <label>Tanggal Mutasi</label>
                                                </div>
                                                <div class="col-lg">
                                                    <b-input-group>
                                                        <b-form-input type="date" v-model="form.date" autocomplete="off"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-lg-3 text-lg-right">
                                                    <label>Keterangan</label>
                                                </div>
                                                <div class="col-lg">
                                                    <b-textarea v-model="form.notes" rows="2" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="row mt-12">
                                                <div class="col-lg-3 text-lg-right">
                                                    <label>Gudang/Buffer Pengirim</label>
                                                </div>
                                                <div class="col-lg">
                                                    <v-select v-model="form.pharmacy_depo_send_id" :options="DepoOptions" :reduce="DepoOptions => DepoOptions.value" label="title" placeholder="-- Pilih Gudang --"></v-select>
                                                </div>
                                            </div>
                                            <div class="row mt-12">
                                                <div class="col-lg-3 text-lg-right">
                                                    <label>Gudang/Buffer Tujuan</label>
                                                </div>
                                                <div class="col-lg">
                                                    <v-select v-model="form.pharmacy_depo_recive_id" :options="DepoOptions" :reduce="DepoOptions => DepoOptions.value" label="title" placeholder="-- Pilih Gudang --"></v-select>
                                                </div>
                                            </div>
                                            <!-- <div class="row mt-12">
                                                <div class="col-lg-3 text-lg-right">
                                                    <label>Total</label>
                                                </div>
                                                <div class="col-lg">
                                                    <b-input-group prepend="Nilai">
                                                        <b-form-input readonly v-model="form.ntotal" class="text-right" />
                                                    </b-input-group>
                                                    <b-input-group prepend="Diskon">
                                                        <b-form-input readonly v-model="form.ndiscount" class="text-right" />
                                                    </b-input-group>
                                                    <b-input-group prepend="Akhir">
                                                        <b-form-input readonly v-model="form.nfinish" class="text-right" />
                                                    </b-input-group>
                                                </div>
                                            </div> -->
                                            <div class="row mt-12">
                                                <div class="col-lg-3 text-lg-right">
                                                    <label>Validasi</label>
                                                </div>
                                                <div class="col-lg">
                                                    <table class="table table-responsive table-bordered" responsive small bordered width="100%">
                                                        <thead class="text-center text-bold-14">
                                                            <tr>
                                                                <th>Level</th>
                                                                <th>Validasi</th>
                                                                <th>Nama</th>
                                                                <th>Tanggal</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr class="item">
                                                                <td>Permintaan/Retur</td>
                                                                <td><b-form-checkbox @change="changeSign" v-model="validation_key_1 === form.validation_key_1" v-if="form.flag_verification === 0 || !form.validation_key_1" /></td>
                                                                <td><template v-if="form.validation_key_1">{{ form.user_validation_1 ? form.user_validation_1 : userData.employee_name  }}</template></td> 
                                                                <td><template v-if="form.validation_key_1">{{ form.user_validation_1 ? form.validation_key_1_date : datenow }}</template></td>
                                                                <td><template v-if="form.validation_key_1">Signed</template></td>
                                                            </tr>
                                                            <tr class="item" v-if="form.flag_verification === 1 || form.flag_verification === 2 || form.flag_verification === 3 ">
                                                                <td>Pengirim</td>
                                                                <td><b-form-checkbox @change="changeSign2" v-model="validation_key_2 === form.validation_key_2" v-if="form.flag_verification === 1 || !form.validation_key_2" /></td>
                                                                <td><template v-if="form.validation_key_2">{{ form.user_validation_2 ? form.user_validation_2 : userData.employee_name  }}</template></td>
                                                                <td><template v-if="form.validation_key_2">{{ form.user_validation_2 ? form.validation_key_2_date : datenow }}</template></td>
                                                                <td><template v-if="form.validation_key_2">Signed</template></td>
                                                            </tr>
                                                            <tr class="item" v-if="form.flag_verification === 1 || form.flag_verification === 2 || form.flag_verification === 3">
                                                                <td>Penerima</td>
                                                                <td><b-form-checkbox @change="changeSign3" v-model="validation_key_3 === form.validation_key_3" v-if="form.flag_verification === 2 || !form.validation_key_3"/></td>
                                                                <td><template v-if="form.validation_key_3">{{ form.user_validation_3 ? form.user_validation_3 : userData.employee_name  }}</template></td>
                                                                <td><template v-if="form.validation_key_3">{{ form.user_validation_3 ? form.validation_key_3_date : datenow }}</template></td>
                                                                <td><template v-if="form.validation_key_3">Signed</template></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="card mt-32">
                        <div class="card-header bg-primary-200 font-weight-bold">
                            <div class="d-flex justify-content-lg-between">
                                <span class="font-weight-bold" style="display:none;">Total : Rp. {{this.form.nfinish}}</span>
                                <div class="d-flex">
                                    <!-- <b-button variant="danger-600" size="sm" @click="deleteSC()" class="mr-2">
                                        <feather-icon icon="TrashIcon" /> Hapus Item
                                    </b-button> -->
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <td v-if="form.flag_verification === 0">
                                        <v-select v-model="catalog" @input="onAddSC($event)" :options="CatalogOptions" :reduce="CatalogOptions => CatalogOptions" label="title" placeholder="-- Pilih Katalog --"></v-select>
                                    </td>
                                </tr>
                            </table>
                            <table class="table table-responsive table-bordered" responsive small bordered width="100%">
                                <thead class="text-center text-bold-14">
                                    <tr>
                                        <th>
                                            <!-- <b-form-checkbox v-model="checkall" @input="selectAll()" /> -->
                                        </th>
                                        <th>NO</th>
                                        <th>KODE</th>
                                        <th>EXP&BATCH</th>
                                        <th>NAMA BARANG</th>
                                        <th>STOK</th>
                                        <th>QTY PERMINTAAN/RETUR</th>
                                        <th>QTY KIRIM</th>
                                    </tr>
                                </thead>
                                <tr class="item" v-for="(item, index) in form.mutation_items">
                                    <td>
                                        <!-- <b-form-checkbox v-model="item.check" /> -->
                                        <b-button variant="danger-600" size="sm" @click="deleteSC(index)" class="mr-2" v-if="form.flag_verification === 0">
                                            <feather-icon icon="TrashIcon" />
                                        </b-button>
                                    </td>
                                    <td>
                                        {{ index+1 }}
                                    </td>
                                    <td>
                                        {{ item.code }}
                                    </td>
                                    <td>
                                        {{ item.expired_date }} - 
                                        {{ item.batch }}
                                    </td>
                                    <td>
                                        {{ item.name }}
                                    </td>
                                    <td class="text-center">
                                        {{ item.stock }}
                                    </td>
                                    <td>
                                        <b-form-input type="text" @change="vpChanged_send($event, index, item.admin_amount)" :disabled="form.validation_key_1 ? true : false" v-model="item.value_send" />
                                    </td>
                                    <td>
                                        <b-form-input type="text" @change="vpChanged_recive($event, index)" :disabled="form.flag_verification === 1 ? false : true" v-model="item.value_recive" />
                                    </td>
                                    <!-- <td>
                                        <b-button variant="danger-600" size="sm" @click="deleteSC(item.index+1)" class="mr-2">
                                            <feather-icon icon="TrashIcon" />
                                        </b-button>
                                    </td> -->
                                </tr>
                            </table>
                        </div>
                    </section>
                </b-overlay>
            </div>
        </section>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import TopPharmacyMenu from '@/@core/components/top-menu/TopPharmacyMenu.vue';
import vSelect from 'vue-select'
import moment from 'moment'
export default {
    name: 'PatientPayment',
    components: {
        TopPharmacyMenu,vSelect
    },
    data() {
        return {
            checkrouter:this.$route.name,
            checkall:false,
            active: {
                main_tab: 'list',
            },
            userData: JSON.parse(localStorage.getItem('userData')),
            params:{
                no_document:'',
                pharmacy_depo_send_id:'8da5d967-18da-4f87-86dc-0a9416611c11',
                pharmacy_depo_recive_id:'',
                offset:0,
                limit:20,
                code:'',
                name:''
            },
            currentPage:1,
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
            datePick: '',
            datenow:moment().format('DD-MM-YYYY hh:mm:ss'),
            form: {
                id:'',
                user_validation_1:'',
                user_validation_2:'',
                user_validation_3:'',
                validation_key_1_date:moment().format('DD-MM-YYYY hh:mm:ss'),
                validation_key_2_date:moment().format('DD-MM-YYYY hh:mm:ss'),
                validation_key_3_date:moment().format('DD-MM-YYYY hh:mm:ss'),
                pharmacy_depo_send_id:'',
                pharmacy_depo_recive_id:'',
                type_doc_id:1,
                no_document:'',
                ntotal:'',
                ndiscount:'',
                nmargins:'',
                flag_verification:0,
                nfinish:'',
                date:'',
                notes:'',
                mutation_items:[],
                validation_key_1:'',
                validation_key_2:'',
                validation_key_3:'',
                transaction_signature_val:[]
            },
            validation_key_1:JSON.parse(localStorage.getItem('userData')).id,
            validation_key_2:JSON.parse(localStorage.getItem('userData')).id,
            validation_key_3:JSON.parse(localStorage.getItem('userData')).id,
            fill_ttd:false,
            validation_key_1_show:true,
            validation_key_2_show:true,
            validation_key_3_show:true,
            sign:false,
            catalog:''
        }
    },
    watch: {
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.$store.dispatch('mutation/getAll',this.params)
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.$store.dispatch('mutation/getAll',this.params)
            }
        }
    },
    computed: {
        ...mapState({ 
            totalRows: state => state.mutation.totalRows, 
            fields: state => state.mutation.fields, 
            items: state => state.mutation.items, 
            pageOptions: state => state.mutation.pageOptions, 
            CatalogOptions: state => state.catalog.options.catalog, 
            DepoOptions: state => state.depo.options.depo, 
            PackageOptions: state => state.packages.options.packages,
            SignatureItems: state => state.signaturevalidation.items
        }),
        firstRow() {
            return (this.Mutationtable.currentPage - 1) * this.Mutationtable.perPage + 1
        },
        lastRow() {
            return Math.min(this.Mutationtable.currentPage * this.Mutationtable.perPage, this.Mutationtable.totalRows)
        },
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat("en-US", { style: "currency", currency: "IDR" }).format(value)
        },
        numberToDecimal(value) {
            let val = (value/1).toFixed(0).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },
        numberToDecimalCurr(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },
        decimalToNumber(value) {
            const val = value
                .replace(/(kr|\$|£|€)/g, '') // getting rid of currency
                .trim()
                .replace(/(.+)[.,](\d+)$/g, "$1x$2") // stripping number into $1: integer and $2: decimal part and putting it together with x as decimal point
                .replace(/[.,]/g, '') // getting rid of . AND ,
                .replace('x', '.'); // replacing x with .
            return parseFloat(val);
        }
    },
    methods: {
        changeSign() {
            if (this.form.validation_key_1) {
                this.sign = false
                this.form.validation_key_1 = ''
            } else {
                this.sign = true
                this.form.validation_key_1 = this.userData.user_id
            }
        },
        changeSign2() {
            if (this.form.validation_key_2) {
                this.sign = false
                this.form.validation_key_2 = ''
            } else {
                this.sign = true
                this.form.validation_key_2 = this.userData.user_id
            }
        },
        changeSign3() {
            if (this.form.validation_key_3) {
                this.sign = false
                this.form.validation_key_3 = ''
            } else {
                this.sign = true
                this.form.validation_key_3 = this.userData.user_id
            }
        },
        undo() {
            this.$refs.signaturePad.undoSignature();
        },
        save() {
            const { isEmpty, data } = this.$refs.signaturePad.saveSignature();
            this.form.validation_key_1 = data.replace("data:image/png;base64,", '')
            this.validation_key_1_show = true
            this.sign = false
        },
        deleteSC (index) {
            this.form.mutation_items.splice(index, 1)
        },
        // selectAll: function() {
        //     if (this.checkall) {
        //         for (let i = 0; i < this.form.mutation_items.length; i++) {
        //             this.form.mutation_items.push({'this.itemsRad[i]'})
        //         }
        //     }
        // },
        onAddSC (event) {
            this.catalog = ''
            this.collation = ''
            this.$store.dispatch('prescription/geCatalogItemMutasi', {pharmacy_depo_id:this.form.pharmacy_depo_send_id,id:event.data_catalog.id})
            .then(res => {
                console.log(res)
                let index = this.form.mutation_items.findIndex((item) => item.pharmacy_catalog_id === event.data_catalog.id);
                // console.log(item.pharmacy_catalog_id)
                // Check if the object with the specified property value exists in the array
                if (index === -1) {
                    // If not found, push a new object with the desired properties
                    // this.form_soap.form_prescription_parent.push({
                    //     id:event.value,
                    //     prescription:event.title,
                    //     used:'',
                    //     value:0
                    // }) 
                    // for (let i = 0; i < res.length; i++) {
                        this.form.mutation_items.push(
                            { 
                                id:'',
                                index:0,
                                on_prc:res.on_prc,
                                on_po:res.on_po,
                                code:res.code,
                                expired_date:res.expired_date,
                                batch:res.batch,
                                stock:res.stock,
                                name:res.name,
                                min:res.min,
                                admin_amount:res.admin_amount,
                                pharmacy_catalog_id:event.data_catalog.id, 
                                package_id:res.package_big_id,
                                package_name_big:res.package_name_big,
                                package_name:res.package_name,
                                value_send:0,
                                value_recive:0,
                                sum_package:res.value_package+' '+res.package_name, 
                                sum_item:'0 '+res.package_name, 
                                price_package:'0', 
                                price_item:'0', 
                                discount_item:'0', 
                                discount_price:'0', 
                                nt:'0', 
                                nf:'0', 
                                sort:this.form.mutation_items.length, 
                                data_catalog:event,
                                action: 'action'
                            }
                        )
                    // }
                } else {
                    // If found, log a message indicating that the object already exists
                    console.log("Katalog Sudah Terinput");
                }
            })
        },
        validationForm () {
            this.isDisabled = true
            this.show = true
            const formData = this.form 
            this.$store.dispatch('mutation/formPostPut', formData)
            .then(res => {
                this.notification('Success','CheckIcon','success','You have successfully')
                this.$store.dispatch('mutation/getAll', this.params)
                this.active.main_tab = 'list'
                this.form.id = ''
                this.form.type_doc_id = 1
                this.form.ntotal = ''
                this.form.ndiscount = ''
                this.form.nmargins = ''
                this.form.nfinish = ''
                this.form.notes = ''
                this.form.date = ''
                this.form.validation_key_1 = NULL
                this.form.validation_key_2 = NULL
                this.form.validation_key_3 = NULL
                this.form.mutation_items = []
                this.isDisabled = false
                this.show = false
            }, 
            err => {
                this.isDisabled = false
                this.show = false
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
            })
        },
        filterData () {
            this.show = true
            this.isDisabledFilter = true
            this.$store.dispatch('mutation/getAll',this.params)
            .then(res => {
                this.show = false
                this.isDisabledFilter = false
            },
            err => {
                this.show = false
                this.isDisabledFilter = false
            })
        },
        filterReset () {
            this.form.id = ''
            this.form.type_doc_id = 1
            this.form.ntotal = ''
            this.form.ndiscount = ''
            this.form.nmargins = ''
            this.form.nfinish = ''
            this.form.notes = ''
            this.form.validation_key_1 = null
            this.form.validation_key_2 = null
            this.form.validation_key_3 = null
            this.form.date = ''
            this.form.mutation_items = []
            this.params.offset = 0
            this.params.limit = this.perPage
            this.$store.dispatch('catalog/getAll',this.params)
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        vpChanged_send($event, index, value){
            let value_package = $event
            if (value_package > value) {
                alert('Tidak bisa melebihi stock');
                this.form.mutation_items[index].value_send = 0
            }
            // let sum_package = this.$options.filters.decimalToNumber(this.form.mutation_items[index].sum_package)
            // this.form.mutation_items[index].value_package = this.$options.filters.numberToDecimal(value_package)
            // this.form.mutation_items[index].sum_item = this.$options.filters.numberToDecimal(value_package * sum_package) + ' ' + this.form.mutation_items[index].package_name
            // let price_item = this.$options.filters.decimalToNumber(this.form.mutation_items[index].price_item)
            // let sum_item = this.$options.filters.decimalToNumber(this.form.mutation_items[index].sum_item)
            // let discount_item = this.$options.filters.decimalToNumber(this.form.mutation_items[index].discount_item)
            // this.form.mutation_items[index].price_item = this.$options.filters.numberToDecimalCurr(price_item)
            // this.form.mutation_items[index].price_package = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            // this.form.mutation_items[index].nt = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            // if (discount_item > 0.00) {
            //     this.form.mutation_items[index].discount_price = this.$options.filters.numberToDecimalCurr(((price_item * sum_item)*discount_item)/100)
            //     this.form.mutation_items[index].nf = this.$options.filters.numberToDecimalCurr((price_item * sum_item)-((price_item * sum_item)*discount_item)/100)
            // } else {
            //     this.form.mutation_items[index].nf = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            // }
            // this.sumAll()
        },
        spChanged($event, index){
            let sum_package = this.$options.filters.decimalToNumber($event)
            let value_package = this.$options.filters.decimalToNumber(this.form.mutation_items[index].value_package)
            let discount_item = this.$options.filters.decimalToNumber(this.form.mutation_items[index].discount_item)
            this.form.mutation_items[index].sum_package = this.$options.filters.numberToDecimal(sum_package) + ' ' + this.form.mutation_items[index].package_name
            this.form.mutation_items[index].sum_item = this.$options.filters.numberToDecimal(sum_package * value_package) + ' ' + this.form.mutation_items[index].package_name
            let price_item = this.$options.filters.decimalToNumber(this.form.mutation_items[index].price_item)
            let sum_item = this.$options.filters.decimalToNumber(this.form.mutation_items[index].sum_item)
            this.form.mutation_items[index].price_item = this.$options.filters.numberToDecimalCurr(price_item)
            this.form.mutation_items[index].price_package = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            this.form.mutation_items[index].nt = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            if (discount_item > 0.00) {
                this.form.mutation_items[index].discount_price = this.$options.filters.numberToDecimalCurr(((price_item * sum_item)*discount_item)/100)
                this.form.mutation_items[index].nf = this.$options.filters.numberToDecimalCurr((price_item * sum_item)-((price_item * sum_item)*discount_item)/100)
            } else {
                this.form.mutation_items[index].nf = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            }
            this.sumAll()
        },
        piChanged($event, index){
            let price_item = this.$options.filters.decimalToNumber($event)
            let discount_item = this.$options.filters.decimalToNumber(this.form.mutation_items[index].discount_item)
            let sum_item = this.$options.filters.decimalToNumber(this.form.mutation_items[index].sum_item)
            this.form.mutation_items[index].price_item = this.$options.filters.numberToDecimalCurr(price_item)
            this.form.mutation_items[index].price_package = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            this.form.mutation_items[index].nt = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            if (discount_item > 0.00) {
                this.form.mutation_items[index].discount_price = this.$options.filters.numberToDecimalCurr(((price_item * sum_item)*discount_item)/100)
                this.form.mutation_items[index].nf = this.$options.filters.numberToDecimalCurr((price_item * sum_item)-((price_item * sum_item)*discount_item)/100)
            } else {
                this.form.mutation_items[index].nf = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            }
            this.sumAll()
        },
        diChanged($event, index){
            let discount_item = this.$options.filters.decimalToNumber($event)
            let price_item = this.$options.filters.decimalToNumber(this.form.mutation_items[index].price_item)
            let sum_item = this.$options.filters.decimalToNumber(this.form.mutation_items[index].sum_item)
            this.form.mutation_items[index].discount_item = this.$options.filters.numberToDecimal(discount_item)
            this.form.mutation_items[index].price_package = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            this.form.mutation_items[index].discount_price = this.$options.filters.numberToDecimalCurr(((price_item * sum_item)*discount_item)/100)
            this.form.mutation_items[index].nf = this.$options.filters.numberToDecimalCurr((price_item * sum_item)-((price_item * sum_item)*discount_item)/100)
            this.sumAll()
        },
        sumAll() {
            let ntotal = 0
            let ndiscount = 0
            let nfinish = 0
            this.form.mutation_items.forEach(element => {
                ntotal += this.$options.filters.decimalToNumber(element.price_package)
                ndiscount += this.$options.filters.decimalToNumber(element.discount_price)
                nfinish += this.$options.filters.decimalToNumber(element.nf)
            })
            this.form.ntotal = this.$options.filters.numberToDecimalCurr(ntotal)
            this.form.ndiscount = this.$options.filters.numberToDecimalCurr(ndiscount)
            this.form.nfinish = this.$options.filters.numberToDecimalCurr(nfinish)
        },
        patientFiltered(patientItems) {
            this.Mutationtable.totalRows = patientItems.length
            this.Mutationtable.currentPage = 1
        },
        toggleMainTab(tab) {
            this.active.main_tab = tab
            this.form.id = ''
            this.form.type_doc_id = 1
            this.form.validation_key_1 = null
            this.form.validation_key_2 = null
            this.form.validation_key_3 = null
            this.form.ntotal = ''
            this.form.ndiscount = ''
            this.form.nmargins = ''
            this.form.nfinish = ''
            this.form.date = ''
            this.form.mutation_items = []
        },
        editData (obj) {
            this.isDisabled = false
            this.active.main_tab = 'add'
            this.form.user_validation_1 = obj.user_validation_1
            this.form.user_validation_2 = obj.user_validation_2
            this.form.user_validation_3 = obj.user_validation_3
            this.form.validation_key_1_date = obj.validation_key_1_date
            this.form.validation_key_2_date = obj.validation_key_2_date
            this.form.validation_key_3_date = obj.validation_key_3_date
            this.form.id = obj.id
            this.form.type_doc_id = obj.type_doc_id
            this.form.flag_verification = obj.flag_verification
            this.form.notes = obj.notes
            this.form.no_document = obj.no_document
            this.form.pharmacy_depo_send_id = obj.pharmacy_depo_send_id
            this.form.pharmacy_depo_recive_id = obj.pharmacy_depo_recive_id
            if (obj.validation_key_1) {
                this.sign = true
                this.form.validation_key_1 = obj.validation_key_1 
            } else {
                this.sign = false
                this.form.validation_key_1 = '' 
            }
            if (obj.validation_key_2) {
                this.sign = true
                this.form.validation_key_2 = obj.validation_key_2 
            } else {
                this.sign = false
                this.form.validation_key_2 = '' 
            }
            if (obj.validation_key_3) {
                this.sign = true
                this.form.validation_key_3 = obj.validation_key_3 
            } else {
                this.sign = false
                this.form.validation_key_3 = '' 
            }
            this.form.ntotal = this.$options.filters.numberToDecimalCurr(obj.ntotal)
            this.form.ndiscount = this.$options.filters.numberToDecimalCurr(obj.ndiscount)
            this.form.nmargins = this.$options.filters.numberToDecimalCurr(obj.nmargins)
            this.form.nfinish = this.$options.filters.numberToDecimalCurr(obj.nfinish)
            this.form.date = obj.date
            this.form.mutation_items = []
            let pi = {}
            for (let i = 0; i < obj.mutation_items.length; i++) {
                pi = {
                    signature_validation_id:obj.mutation_items[i].id,
                    pharmacy_mutation_id:obj.mutation_items[i].pharmacy_mutation_id,
                    index:obj.mutation_items[i].sort,
                    on_prc:obj.mutation_items[i].on_prc,
                    on_po:obj.mutation_items[i].on_po,
                    on_pm:obj.mutation_items[i].on_pm,
                    code:obj.mutation_items[i].code,
                    expired_date:obj.mutation_items[i].expired_date,
                    batch:obj.mutation_items[i].batch,
                    stock:obj.mutation_items[i].stock,
                    name:obj.mutation_items[i].catalog_name,
                    min:obj.mutation_items[i].min, 
                    admin_amount:obj.mutation_items[i].admin_amount, 
                    pharmacy_catalog_id:obj.mutation_items[i].pharmacy_catalog_id, 
                    package_id:obj.mutation_items[i].package_id,
                    package_name_big:obj.mutation_items[i].package_name_big,
                    package_name:obj.mutation_items[i].package_name,
                    value_send:obj.mutation_items[i].value_send,
                    value_recive:obj.mutation_items[i].value_recive,
                    value_package:this.$options.filters.numberToDecimal(obj.mutation_items[i].value_package),
                    sum_package:this.$options.filters.numberToDecimal(obj.mutation_items[i].sum_package)+' '+obj.mutation_items[i].package_name, 
                    sum_item:this.$options.filters.numberToDecimal(obj.mutation_items[i].sum_item)+' '+obj.mutation_items[i].package_name, 
                    price_package:this.$options.filters.numberToDecimalCurr(obj.mutation_items[i].price_package), 
                    price_item:this.$options.filters.numberToDecimalCurr(obj.mutation_items[i].price_item), 
                    discount_item:this.$options.filters.numberToDecimal(obj.mutation_items[i].discount_item), 
                    discount_price:this.$options.filters.numberToDecimalCurr(obj.mutation_items[i].discount_price), 
                    nt:this.$options.filters.numberToDecimalCurr(obj.mutation_items[i].price_item*obj.mutation_items[i].sum_item), 
                    nf:this.$options.filters.numberToDecimalCurr((obj.mutation_items[i].price_item*obj.mutation_items[i].sum_item)-((obj.mutation_items[i].price_item*obj.mutation_items[i].sum_item)*obj.mutation_items[i].discount_item/100)), 
                    sort:obj.mutation_items[i].sort, 
                    action: 'action'
                }
                this.form.mutation_items.push(pi)
            }
            this.form.transaction_signature_val = obj.transaction_signature_validation
            this.show = false
        },
        rowOnDelete(id) {
            this.$bvModal
            .msgBoxConfirm('Please confirm that you want to delete everything.', {
                title: 'Please Confirm', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true) {
                    this.$store.dispatch('mutation/remove', id)
                        .then(response => {
                            if (response.status === 200) {
                                this.$store.dispatch('mutation/getAll',this.params)
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
        }
    },
    mounted() {
        // if (this.form.id === '') {
        //     this.form.transaction_signature_val = []
        //     this.$store.dispatch('signaturevalidation/getParamModule','a4abf06d-70ab-494c-b14a-9bffe4b59587').then(res => {
        //         let sg = {}
        //         for (let i = 0; i < res.signature_validation_items.length; i++) {
        //             sg = {
        //                 signature_validation_id:res.signature_validation_items[i].signature_validation_id,
        //                 group_name:res.signature_validation_items[i].group_name,
        //                 group_id:res.signature_validation_items[i].group_id,
        //                 level_validation:res.signature_validation_items[i].level_validation,
        //                 status:res.signature_validation_items[i].status
        //             }
        //             this.form.transaction_signature_val.push(sg)
        //         }
        //     })
        // }
    },
    created () {
        this.$store.dispatch('mutation/getAll',this.params)
        this.$store.dispatch('packages/getParam')
        this.$store.dispatch('catalog/getParam')
        this.$store.dispatch('depo/getParam')
    }
};
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>