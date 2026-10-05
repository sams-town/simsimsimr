<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'FARMASI' },
                { text: 'PR' },
                // { text: this.form_transaction.no_order }
            ]"
        >
        <!-- <Layout selected_menu="menu10"/> -->
        <TopPharmacyMenu selectedMenu="menu1" />
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
                    <div class="row mx-0">
                        <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                            <span class="text-regular-14">Tampilkan</span>
                            <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
                            <span class="text-regular-14">Entri</span>
                        </b-form-group>
                    </div>
                    <b-table bordered responsive hover small head-variant="light" :items="items" :fields="fields" :per-page="0" :current-page="currentPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
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
                            <template v-if="row.item.validation_key_1 !== null">
                                <b-badge variant="primary">Tervalidasi</b-badge>
                            </template>
                            <template v-else>
                                <b-badge variant="danger">Belum Tervalidasi</b-badge>
                            </template>
                        </template>
                        <template #cell(action)="row">
                            <div class="row mx-0">
                                <template v-if="row.item.validation_key_1 === null">
                                    <b-button variant="warning-500" size="sm" class="mr-1">
                                        <feather-icon icon="EditIcon" @click="editData(row.item)" />
                                    </b-button>
                                    <b-button variant="danger-500" size="sm">
                                        <feather-icon icon="TrashIcon" @click="rowOnDelete(row.item.id)" />
                                        <b-link v-b-modal.modal-center>
                                        </b-link>
                                    </b-button>
                                </template>
                                <template v-else>
                                    <b-button variant="primary-500" size="sm" class="mr-1">
                                        <feather-icon icon="EditIcon" @click="editData(row.item)" />
                                    </b-button>
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
                                <span class="font-weight-bold">DATA PERENCANAAN</span>
                                <div class="d-flex">
                                    <!-- <router-link :to="{ name: 'PharmacyPlanningProcurementView' }" target="_blank">
                                        <b-button variant="primary-600" size="sm" class="mr-1">
                                            <feather-icon icon="PrinterIcon" />
                                        </b-button>
                                    </router-link> -->
                                    <!-- <template v-if="this.form.validation_key_1 === null"> -->
                                        <b-button variant="primary-600" size="sm" @click.prevent="validationForm" class="mr-1">
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
                                        <div class="col-lg-6">
                                            <div class="row mt-12" v-if="this.form.no_document">
                                                <div class="col-lg-3 text-lg-right">
                                                    <label>No. Dokumen</label>
                                                </div>
                                                <div class="col-lg">
                                                    {{ this.form.no_document }}
                                                </div>
                                            </div>
                                            <div class="row mt-12">
                                                <div class="col-lg-3 text-lg-right">
                                                    <label>Gudang</label>
                                                </div>
                                                <div class="col-lg">
                                                        <v-select v-model="form.pharmacy_depo_id" @input="onAddLowStock($event)" :options="DepoOptions" :reduce="DepoOptions => DepoOptions.value" label="title" placeholder="-- Pilih Gudang --"></v-select>
                                                        <!-- <b-input-group>
                                                        <b-input-group-prepend is-text checked>
                                                            <b-form-radio type="radio" v-model="form.pharmacy_depo_id" value="8da5d967-18da-4f87-86dc-0a9416611c11" :checked="true"/>
                                                        </b-input-group-prepend>
                                                        <b-form-input value="INSTALASI FARMASI" disabled></b-form-input>
                                                    </b-input-group> -->
                                                </div>
                                            </div>
                                            <div class="row mt-12">
                                                <div class="col-lg-3 text-lg-right">
                                                    <label>Tanggal</label>
                                                </div>
                                                <div class="col-lg">
                                                    <b-input-group>
                                                        <b-form-input type="date" v-model="form.date" autocomplete="off"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="row mt-12">
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
                                            </div>
                                            <div class="row mt-12">
                                                <div class="col-lg-3 text-lg-right">
                                                    <label>Validasi</label>
                                                </div>
                                                <div class="col-lg">
                                                    <template>
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
                                                                    <td>1</td>
                                                                    <td><b-form-checkbox @change="changeSign" v-model="validation_key_1 === form.validation_key_1" /></td>
                                                                    <td><template v-if="form.validation_key_1">{{ userData.employee_name }}</template></td>
                                                                    <td><template v-if="form.validation_key_1">{{ datenow }}</template></td>
                                                                    <td><template v-if="form.validation_key_1">Signed</template></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </template>
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
                                <span class="font-weight-bold">Total : Rp. {{this.form.nfinish}}</span>
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
                                    <td>
                                        <v-select v-model="catalog" @input="onAddSC($event)" :options="CatalogOptions" :reduce="CatalogOptions => CatalogOptions" label="title" placeholder="-- Pilih Katalog --"></v-select>
                                    </td>
                                </tr>
                            </table>
                            <b-table bordered responsive hover small head-variant="light" :fields="fieldsItem" :items="form.planning_items" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                                <!-- <template #cell(value_package)="row">
                                    <b-form-input type="text" style="width:90px" @change="vpChanged($event, row.item.index)" v-model="row.item.value_package" />
                                </template> -->
                                <template #thead-top>
                                    <b-tr>
                                        <b-th
                                        v-for="field in fieldsItem"
                                        :key="field.key"
                                        class="text-center small"
                                        >
                                        {{ field.subLabel }}
                                        </b-th>
                                    </b-tr>
                                </template>
                                <template #cell(suppliers_name)="row">
                                    {{ row.item.suppliers_name }}
                                    <router-link :to="{ path: '/pharmacy/receive/print/'+ row.item.receipt_id}">
                                        <b-button size="sm" class="ml-1" variant="info-700">
                                            <feather-icon icon="PrinterIcon" />
                                        </b-button>
                                    </router-link>
                                </template>
                                <!-- <template #cell(sum_package)="row">
                                    <b-form-input type="text" @change="spChanged($event, row.item.index)" v-model="row.item.sum_package" />
                                </template> -->
                                <!-- <template #cell(sum_item)="row">
                                    <b-form-input type="text" v-model="row.item.sum_item" readonly/>
                                </template> -->
                                <!-- <template #cell(price_package)="row">
                                    <b-form-input type="text" v-model="row.item.price_package" readonly/>
                                </template> -->
                                <!-- <template #cell(price_item)="row">
                                    <b-form-input type="text" @change="piChanged($event, row.item.index)" v-model="row.item.price_item" />
                                </template> -->
                                <!-- <template #cell(discount_item)="row">
                                    <b-form-input type="text" @change="diChanged($event, row.item.index)" v-model="row.item.discount_item" />
                                </template> -->
                                <!-- <template #cell(nt)="row">
                                    <b-form-input type="text" v-model="row.item.nt" readonly/>
                                </template>
                                <template #cell(discount_price)="row">
                                    <b-form-input type="text" v-model="row.item.discount_price" readonly/>
                                </template>
                                <template #cell(nf)="row">
                                    <b-form-input type="text" v-model="row.item.nf" readonly/>
                                </template> -->
                            </b-table>
                            <!-- <table class="table table-responsive table-bordered" responsive small bordered>
                                <thead class="text-center text-bold-14">
                                    <tr>
                                        <th rowspan="2">
                                        </th>
                                        <th rowspan="2">NO</th>
                                        <th rowspan="2">KODE</th>
                                        <th rowspan="2" style="min-width: 200px;">NAMA BARANG</th>
                                        <th rowspan="2">STOK</th>
                                        <th rowspan="2">MINUMUM STOK</th>
                                        <th colspan="2">ESTIMASI ORDER</th>
                                        <th colspan="2">JUMLAH</th>
                                        <th colspan="2">HARGA</th>
                                        <th rowspan="2" style="min-width: 100px;">DISKON %</th>
                                        <th colspan="3">TOTAL</th>
                                    </tr>
                                    <tr>
                                        <th style="min-width: 140px;">KEMASAN</th>
                                        <th style="min-width: 140px;">SATUAN</th>
                                        <th style="min-width: 140px;">KEMASAN</th>
                                        <th style="min-width: 140px;">SATUAN</th>
                                        <th style="min-width: 140px;">KEMASAN</th>
                                        <th style="min-width: 140px;">SATUAN</th>
                                        <th style="min-width: 140px;">SEBELUM DISC</th>
                                        <th style="min-width: 140px;">DISC</th>
                                        <th style="min-width: 140px;">NOMINAL</th>
                                    </tr>
                                </thead>
                                <tr class="item" v-for="(item, index) in form.planning_items" :style="(item.on_prc === 1 && item.on_po === 1) ? 'background-color:#F8C43E' : (item.on_prc === 1 || item.on_pm === 1 ? 'background-color:#db6969' : '')">
                                    <td>
                                        <b-button variant="danger-600" size="sm" @click="deleteSC(index)" class="mr-2">
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
                                        {{ item.name }}
                                    </td>
                                    <td class="text-center">
                                        {{ item.admin_amount }}
                                    </td>
                                    <td class="text-center">
                                        {{ item.min }}
                                    </td>
                                    <td>
                                        <v-select v-model="item.package_id" :options="PackageOptions" :reduce="PackageOptions => PackageOptions.value" label="title" placeholder="Kemasan"></v-select>
                                    </td>
                                    <td>
                                        <b-form-input type="text" @change="vpChanged($event, index)" v-model="item.value_package" />
                                    </td>
                                    <td>
                                        <b-form-input type="text" @change="spChanged($event, index)" v-model="item.sum_package" />
                                    </td>
                                    <td>
                                        <b-form-input type="text" v-model="item.sum_item" readonly/>
                                    </td>
                                    <td>
                                        <b-form-input type="text" v-model="item.price_package" readonly/>
                                    </td>
                                    <td>
                                        <b-form-input type="text" @change="piChanged($event, index)" v-model="item.price_item" />
                                    </td>
                                    <td>
                                        <b-form-input type="text" @change="diChanged($event, index)" v-model="item.discount_item" />
                                    </td>
                                    <td>
                                        <b-form-input type="text" v-model="item.nt" readonly/>
                                    </td>
                                    <td>
                                        <b-form-input type="text" v-model="item.discount_price" readonly/>
                                    </td>
                                    <td>
                                        <b-form-input type="text" v-model="item.nf" readonly/>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="13">&nbsp;</td>
                                    <td><b-form-input type="text" v-model="form.ntotal" readonly/></td>
                                    <td><b-form-input type="text" v-model="form.ndiscount" readonly/></td>
                                    <td><b-form-input type="text" v-model="form.nfinish" readonly/></td>
                                </tr>
                                <tr>
                                    <td colspan="17">&nbsp;</td>
                                </tr>
                            </table> -->
                        </div>
                    </section>
                    <!-- <section class="card mt-32">
                        <div class="card-header bg-primary-200">
                            <div class="d-flex justify-content-lg-between">
                                <span class="font-weight-bold">HISTORI VALIDASI PERENCANAAN</span>
                                <div class="d-flex">
                                    <router-link :to="{ name: 'PharmacyPlanningProcurementView' }" target="_blank">
                                        <b-button variant="primary-600" size="sm" class="mr-1">
                                            <feather-icon icon="PrinterIcon" />
                                        </b-button>
                                    </router-link>
                                    <b-button variant="primary-600" size="sm" @click.prevent="validationForm" class="mr-1">
                                        <feather-icon icon="SaveIcon" />
                                    </b-button>
                                    <b-button variant="primary-600" size="sm">
                                        <feather-icon icon="RefreshCcwIcon" />
                                    </b-button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive table-bordered" responsive small bordered width="100%">
                                <thead class="text-center text-bold-14">
                                    <tr>
                                        <th>Level</th>
                                        <th>Ver</th>
                                        <th>Otorisasi</th>
                                        <th>User</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item" v-for="(item, index) in form.transaction_signature_val">
                                        <td>
                                            {{ item.level_validation }}
                                        </td>
                                        <td>
                                            <b-form-checkbox v-model="item.status" :value="item.status" />
                                        </td>
                                        <td>
                                            {{ item.group_name }}
                                        </td>
                                        <td>
                                            {{ item.group_name }}
                                        </td>
                                        <td>
                                            {{ datenow }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section> -->
                </b-overlay>
            </div>
        </section>
        </Layout>
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
            percent:false,
            is_percent:false,
            userData: JSON.parse(localStorage.getItem('userData')),
            checkall:false,
            active: {
                main_tab: 'list',
            },
            params:{
                offset:0,
                limit:20,
                code:'',
                name:''
            },
            fieldsItem: [
                { key: 'no', label: 'NO' }, //thStyle: { minWidth: '140px' }
                { key: 'name', label: 'NAMA BARANG' }, //thStyle: { minWidth: '140px' }
                { key: 'satuan', label: 'SATUAN', subLabel: '' },
                { key: 'sisa_stok_31_des_2025', label: 'SISA STOK PER 31 DESEMBER 2025', subLabel: '(a)' },
                { key: 'prediksi_pengadaan_2026', label: 'PREDIKSI PENGADAAN TAHUN 2026', subLabel: '(b)' },
                { key: 'pemakaian_rata_rata', label: 'PEMAKAIAN RATA-RATA PER BULAN SELAMA 2025', subLabel: '(c)' },
                { key: 'kategori_abc', label: 'KATEGORI A/B/C', subLabel: '(d)' },
                { key: 'kategori_ven', label: 'KATEGORI V/E/N', subLabel: '(e)' },
                { key: 'kategori_obat_abc_ven', label: 'KATEGORI OBAT SESUAI ABC-VEN', subLabel: '(f)' },
                { key: 'kategori_put', label: 'KATEGORI PUT', subLabel: '(g)' },
                { key: 'persentase_buffer_stok', label: 'PERSENTASE BUFFER STOK', subLabel: '(h)' },
                { key: 'prediksi_sisa_stok_31_des_2026', label: 'PREDIKSI SISA STOK 31 DESEMBER 2026', subLabel: '(i) = (a + b) - (12 x c)' },
                { key: 'jumlah_kebutuhan_2027', label: 'JUMLAH KEBUTUHAN TAHUN 2027', subLabel: '(j) = (c x 12) + (h x c x 1)' },
                { key: 'rencana_kebutuhan_2027', label: 'RENCANA KEBUTUHAN TAHUN 2027', subLabel: '(k) = j - i' },
                { key: 'rencana_pengadaan_2027', label: 'RENCANA PENGADAAN TAHUN 2027', subLabel: '(l)' },
                { key: 'rencana_pengadaan_2026', label: 'RENCANA PENGADAAN TAHUN 2026', subLabel: '(m)' },
                { key: 'realisasi_pengadaan_2025', label: 'REALISASI PENGADAAN TAHUN 2025', subLabel: '(n)' },
                { key: 'keterangan', label: 'KETERANGAN', subLabel: '(o)' },
            ],
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
                pharmacy_depo_id:'',
                type_doc_id:1,
                no_document:'',
                ntotal:'',
                ntotalb:'',
                ndiscount:'',
                ndiscountotal:'',
                nmargins:'',
                nfinish:'',
                nfinishitem:'',
                nntax:'',
                date:'',
                planning_items:[],
                validation_1:false,
                validation_key_1:'',
                transaction_signature_val:[]
            },
            sign:false,
            validation_key_1:'eba5ed94-e863-43c7-bb57-197c33678b4d',
            validation_key_1_show:true,
            fill_ttd:false,
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
                this.$store.dispatch('catalog/getAll',this.params)
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.$store.dispatch('catalog/getAll',this.params)
            }
        }
    },
    computed: {
        ...mapState({ 
            totalRows: state => state.planning.totalRows, 
            fields: state => state.planning.fields, 
            items: state => state.planning.items, 
            pageOptions: state => state.planning.pageOptions, 
            CatalogOptions: state => state.catalog.options.catalog, 
            DepoOptions: state => state.depo.options.depo, 
            PackageOptions: state => state.packages.options.packages,
            SignatureItems: state => state.signaturevalidation.items
        }),
        firstRow() {
            return (this.Planningtable.currentPage - 1) * this.Planningtable.perPage + 1
        },
        lastRow() {
            return Math.min(this.Planningtable.currentPage * this.Planningtable.perPage, this.Planningtable.totalRows)
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
        changePercent() {
            if (this.is_percent) {
                this.is_percent = false
                this.percent = false
            } else {
                this.is_percent = true
                this.percent = true
            }
            this.form.ndiscountotal = 0
            this.form.ndiscounttotal = 0
            this.sumAll()
        },
        changeSign() {
            if (this.form.validation_key_1) {
                this.sign = false
                this.form.validation_key_1 = ''
            } else {
                this.sign = true
                this.form.validation_key_1 = this.userData.user_id
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
            this.form.planning_items.splice(index, 1)
        },
        // selectAll: function() {
        //     if (this.checkall) {
        //         for (let i = 0; i < this.form.planning_items.length; i++) {
        //             this.form.planning_items.push({'this.itemsRad[i]'})
        //         }
        //     }
        // },
        onAddSC (event) {
            this.catalog = ''
            this.collation = ''
            this.$store.dispatch('planning/getItemDepo', {pharmacy_depo_id:this.form.pharmacy_depo_id,pharmacy_catalog_id:event.data_catalog.id})
            .then(res => {
                let index = this.form.planning_items.findIndex((item) => item.pharmacy_catalog_id === event.data_catalog.id);
                // Check if the object with the specified property value exists in the array
                if (index === -1) {
                    // If not found, push a new object with the desired properties
                    // this.form_soap.form_prescription_parent.push({
                    //     id:event.value,
                    //     prescription:event.title,
                    //     used:'',
                    //     value:0
                    // }) 
                    for (let i = 0; i < res.length; i++) {
                        this.form.planning_items.push(
                            { 
                                id:'',
                                index:0,
                                on_prc:res[i].on_prc,
                                on_po:res[i].on_po,
                                code:res[i].code,
                                name:res[i].name,
                                min:res[i].min,
                                admin_amount:res[i].admin_amount,
                                pharmacy_catalog_id:res[i].id, 
                                package_id:event.data_catalog.package_id,
                                package_big_id:event.data_catalog.package_big_id,
                                package_name_big:res[i].package_name_big,
                                package_name:res[i].package_name,
                                value_package:0,
                                sum_package:res[i].value_package+' '+res[i].package_name, 
                                sum_item:'0 '+res[i].package_name, 
                                price_package:'0', 
                                price_item:'0', 
                                discount_item:'0', 
                                discount_price:'0', 
                                nt:'0', 
                                nf:'0', 
                                sort:this.form.planning_items.length, 
                                data_catalog:event,
                                action: 'action'
                            }
                        )
                    }
                } else {
                    // If found, log a message indicating that the object already exists
                    console.log("Katalog Sudah Terinput");
                }
            })
            console.log(this.form.planning_items)
        },
        onAddLowStock (event) {
            this.catalog = ''
            this.collation = ''
            this.$store.dispatch('planning/getLowDepo', this.form.pharmacy_depo_id)
            .then(res => {
                let no = 1
                this.form.planning_items = []
                for (let i = 0; i < res.length; i++) {
                    this.form.planning_items.push(
                        { 
                            id:'',
                            no: no++,
                            index:i,
                            code:res[i].code,
                            on_prc:res[i].on_prc,
                            on_po:res[i].on_po,
                            name:res[i].catalog_name,
                            min:res[i].min,
                            max:res[i].max,
                            admin_amount:res[i].admin_amount,
                            pharmacy_catalog_id:res[i].id, 
                            package_id:res[i].package_id,
                            package_big_id:res[i].package_big_id,
                            package_name_big:res[i].package_big_name,
                            suppliers_name:res[i].suppliers_name,
                            account_alias:res[i].account_alias,
                            price_point:res[i].price_point,
                            receipt_id:res[i].receipt_id,
                            package_name:res[i].package_name,
                            value_package:(res[i].max-res[i].admin_amount),
                            sum_package:this.$options.filters.numberToDecimalCurr(res[i].value_package), 
                            sum_item:(res[i].max-res[i].admin_amount), 
                            price_package:this.$options.filters.numberToDecimalCurr(res[i].price), 
                            price_item:this.$options.filters.numberToDecimalCurr(res[i].price), 
                            discount_item:res[i].discount_item, 
                            discount_price:this.$options.filters.numberToDecimalCurr((res[i].price*(res[i].max-res[i].admin_amount))*res[i].discount_item/100), 
                            nt:this.$options.filters.numberToDecimalCurr(res[i].price*(res[i].max-res[i].admin_amount)), 
                            nf:this.$options.filters.numberToDecimalCurr((res[i].price*(res[i].max-res[i].admin_amount))-((res[i].price*(res[i].max-res[i].admin_amount))*res[i].discount_item/100)), 
                            sort:this.form.planning_items.length, 
                            data_catalog:event,
                            action: 'action'
                        }
                    )
                    this.sumAll()
                }
            })
            console.log(this.form.planning_items)
        },
        validationForm () {
            this.show = true
            const formData = this.form 
            this.$store.dispatch('planning/formPostPut', formData)
            .then(res => {
                this.notification('Success','CheckIcon','success','You have successfully')
                this.$store.dispatch('planning/getAll', this.params)
                this.active.main_tab = 'list'
                this.form.id = ''
                this.form.type_doc_id = 1
                this.form.ntotal = ''
                this.form.ntotalb = ''
                this.form.ndiscount = ''
                this.form.nmargins = ''
                this.form.nfinish = ''
                this.form.nfinishitem = ''
                this.form.date = ''
                this.form.validation_key_1 = null
                this.form.planning_items = []
                this.show = false
            }, 
            err => {
                this.show = false
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
            })
        },
        filterData () {
            this.show = true
            this.isDisabledFilter = true
            this.$store.dispatch('catalog/getAll', this.params)
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
            this.form.ntotalb = ''
            this.form.ndiscount = ''
            this.form.nmargins = ''
            this.form.nfinish = ''
            this.form.nfinishitem = ''
            this.form.validation_key_1 = null
            this.form.date = ''
            this.form.planning_items = []
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
        vpChanged($event, index){
            let value_package = $event
            let sum_package = this.$options.filters.decimalToNumber(this.form.planning_items[index].sum_package)
            this.form.planning_items[index].value_package = this.$options.filters.numberToDecimal(value_package)
            this.form.planning_items[index].sum_item = this.$options.filters.numberToDecimal(value_package * sum_package) + ' ' + this.form.planning_items[index].package_name
            let price_item = this.$options.filters.decimalToNumber(this.form.planning_items[index].price_item)
            let sum_item = this.$options.filters.decimalToNumber(this.form.planning_items[index].sum_item)
            let discount_item = this.form.planning_items[index].discount_item
            this.form.planning_items[index].price_item = this.$options.filters.numberToDecimalCurr(price_item)
            console.log(price_item)
            console.log(sum_item)
            let total = Math.floor(price_item * sum_item)
            this.form.planning_items[index].nt = this.$options.filters.numberToDecimalCurr(total)
            this.form.planning_items[index].price_package = this.$options.filters.numberToDecimalCurr(total)
            console.log('price_package'+ this.form.planning_items[index].price_package)
            if (discount_item > 0.00) {
                this.form.planning_items[index].discount_price = this.$options.filters.numberToDecimalCurr(((total)*discount_item)/100)
                this.form.planning_items[index].nf = this.$options.filters.numberToDecimalCurr((total)-((total)*discount_item)/100)
            } else {
                this.form.planning_items[index].nf = this.$options.filters.numberToDecimalCurr(total)
                console.log('nf'+ this.form.planning_items[index].nf)
            }
            this.sumAll()
        },
        spChanged($event, index){
            let sum_package = this.$options.filters.decimalToNumber($event)
            let value_package = this.$options.filters.decimalToNumber(this.form.planning_items[index].value_package)
            let discount_item = this.$options.filters.decimalToNumber(this.form.planning_items[index].discount_item)
            this.form.planning_items[index].sum_package = this.$options.filters.numberToDecimal(sum_package) + ' ' + this.form.planning_items[index].package_name
            this.form.planning_items[index].sum_item = this.$options.filters.numberToDecimal(sum_package * value_package) + ' ' + this.form.planning_items[index].package_name
            let price_item = this.$options.filters.decimalToNumber(this.form.planning_items[index].price_item)
            let sum_item = this.$options.filters.decimalToNumber(this.form.planning_items[index].sum_item)
            this.form.planning_items[index].price_item = this.$options.filters.numberToDecimalCurr(price_item)
            this.form.planning_items[index].price_package = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            this.form.planning_items[index].nt = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            if (discount_item > 0.00) {
                this.form.planning_items[index].discount_price = this.$options.filters.numberToDecimalCurr(((price_item * sum_item)*discount_item)/100)
                this.form.planning_items[index].nf = this.$options.filters.numberToDecimalCurr((price_item * sum_item)-((price_item * sum_item)*discount_item)/100)
            } else {
                this.form.planning_items[index].nf = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            }
            this.sumAll()
        },
        piChanged($event, index){
            let price_item = this.$options.filters.decimalToNumber($event)
            let discount_item = this.$options.filters.decimalToNumber(this.form.planning_items[index].discount_item)
            let sum_item = this.$options.filters.decimalToNumber(this.form.planning_items[index].sum_item)
            this.form.planning_items[index].price_item = this.$options.filters.numberToDecimalCurr(price_item)
            this.form.planning_items[index].price_package = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            this.form.planning_items[index].nt = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            if (discount_item > 0.00) {
                this.form.planning_items[index].discount_price = this.$options.filters.numberToDecimalCurr(((price_item * sum_item)*discount_item)/100)
                this.form.planning_items[index].nf = this.$options.filters.numberToDecimalCurr((price_item * sum_item)-((price_item * sum_item)*discount_item)/100)
            } else {
                this.form.planning_items[index].nf = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            }
            this.sumAll()
        },
        diChanged($event, index){
            let discount_item = this.$options.filters.decimalToNumber($event)
            let price_item = this.$options.filters.decimalToNumber(this.form.planning_items[index].price_item)
            let sum_item = this.$options.filters.decimalToNumber(this.form.planning_items[index].sum_item)
            this.form.planning_items[index].discount_item = this.$options.filters.numberToDecimal(discount_item)
            this.form.planning_items[index].price_package = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            this.form.planning_items[index].discount_price = this.$options.filters.numberToDecimalCurr(((price_item * sum_item)*discount_item)/100)
            this.form.planning_items[index].nf = this.$options.filters.numberToDecimalCurr((price_item * sum_item)-((price_item * sum_item)*discount_item)/100)
            this.sumAll()
        },
        sumAll() {
            let ntotal = 0
            let ntotalb = 0
            let ndiscount = 0
            let nntax = 0
            let nfinish = 0
            let nfinishitem = 0
            let ndiscountotal = 0
            let ndiscounttotal = 0
            this.form.planning_items.forEach(element => {
                ntotal += this.$options.filters.decimalToNumber(element.nt)
                ntotalb += this.$options.filters.decimalToNumber(element.nt)
                ndiscount += this.$options.filters.decimalToNumber(element.discount_price)
                nfinishitem += this.$options.filters.decimalToNumber(element.nf)
            })
            if (this.form.ndiscountotal) {
                ndiscountotal = this.$options.filters.decimalToNumber(this.form.ndiscountotal)
                ndiscounttotal = Math.floor((ntotal * ndiscountotal) / 100)
                ntotal = Math.floor(ntotal - ndiscounttotal)
            }
            nfinish = Math.floor(nfinishitem)
            this.form.ntotalb = this.$options.filters.numberToDecimalCurr(ntotalb)
            this.form.ntotal = this.$options.filters.numberToDecimalCurr(ntotal)
            this.form.ndiscount = this.$options.filters.numberToDecimalCurr(ndiscount)
            this.form.ndiscounttotal = this.$options.filters.numberToDecimalCurr(ndiscounttotal)
            this.form.nfinish = this.$options.filters.numberToDecimalCurr(nfinish)
            this.form.nntax = this.$options.filters.numberToDecimalCurr(nntax)
            this.form.nfinishitem = this.$options.filters.numberToDecimalCurr(nfinishitem)
        },
        patientFiltered(patientItems) {
            this.Planningtable.totalRows = patientItems.length
            this.Planningtable.currentPage = 1
        },
        toggleMainTab(tab) {
            this.active.main_tab = tab
            this.form.id = ''
            this.form.type_doc_id = 1
            this.form.validation_key_1 = null
            this.form.ntotal = ''
            this.form.ndiscount = ''
            this.form.nmargins = ''
            this.form.nfinish = ''
            this.form.date = ''
            this.form.planning_items = []
        },
        editData (obj) {
            this.active.main_tab = 'add'
            this.form.id = obj.id
            this.form.type_doc_id = obj.type_doc_id
            this.form.no_document = obj.no_document
            this.form.pharmacy_depo_id = obj.pharmacy_depo_id
            console.log(`http://192.168.100.3/${obj.validation_key_1}`)
            if (obj.validation_key_1) {
                this.fill_ttd = true
                this.sign = true
                this.validation_key_1_show = true
                // this.$refs.signaturePad.fromDataURL(`http://192.168.100.3/${obj.validation_key_1}`);
                this.form.validation_key_1 = `http://192.168.100.3/${obj.validation_key_1}`
            } else {
                this.fill_ttd = false
                this.sign = false
                this.validation_key_1_show = true
            }
            console.log(this.fill_ttd)
            console.log(this.sign)
            console.log(this.validation_key_1_show)
            this.form.ntotal = this.$options.filters.numberToDecimalCurr(obj.ntotal)
            this.form.ndiscount = this.$options.filters.numberToDecimalCurr(obj.ndiscount)
            this.form.nmargins = this.$options.filters.numberToDecimalCurr(obj.nmargins)
            this.form.nfinish = this.$options.filters.numberToDecimalCurr(obj.nfinish)
            this.form.date = obj.date
            let pi = {}
            for (let i = 0; i < obj.planning_items.length; i++) {
                pi = {
                    signature_validation_id:obj.planning_items[i].id,
                    pharmacy_planning_id:obj.planning_items[i].pharmacy_planning_id,
                    index:obj.planning_items[i].sort,
                    on_prc:obj.planning_items[i].on_prc,
                    on_po:obj.planning_items[i].on_po,
                    on_pm:obj.planning_items[i].on_pm,
                    code:obj.planning_items[i].code,
                    name:obj.planning_items[i].pharmacy_catalog_name,
                    min:obj.planning_items[i].min, 
                    admin_amount:obj.planning_items[i].admin_amount, 
                    pharmacy_catalog_id:obj.planning_items[i].pharmacy_catalog_id, 
                    package_id:obj.planning_items[i].package_id,
                    package_name_big:obj.planning_items[i].package_name_big,
                    package_name:obj.planning_items[i].package_name,
                    value_package:this.$options.filters.numberToDecimal(obj.planning_items[i].value_package),
                    sum_package:this.$options.filters.numberToDecimal(obj.planning_items[i].sum_package)+' '+obj.planning_items[i].package_name, 
                    sum_item:this.$options.filters.numberToDecimal(obj.planning_items[i].sum_item)+' '+obj.planning_items[i].package_name, 
                    price_package:this.$options.filters.numberToDecimalCurr(obj.planning_items[i].price_package), 
                    price_item:this.$options.filters.numberToDecimalCurr(obj.planning_items[i].price_item), 
                    discount_item:this.$options.filters.numberToDecimal(obj.planning_items[i].discount_item), 
                    discount_price:this.$options.filters.numberToDecimalCurr(obj.planning_items[i].discount_price), 
                    nt:this.$options.filters.numberToDecimalCurr(obj.planning_items[i].price_item*obj.planning_items[i].sum_item), 
                    nf:this.$options.filters.numberToDecimalCurr((obj.planning_items[i].price_item*obj.planning_items[i].sum_item)-((obj.planning_items[i].price_item*obj.planning_items[i].sum_item)*obj.planning_items[i].discount_item/100)), 
                    sort:obj.planning_items[i].sort, 
                    action: 'action'
                }
                this.form.planning_items.push(pi)
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
                    this.$store.dispatch('planning/remove', id)
                        .then(response => {
                            if (response.status === 200) {
                                this.$store.dispatch('planning/getAll',this.params)
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
        this.$store.dispatch('planning/getAll',this.params)
        this.$store.dispatch('packages/getParam')
        this.$store.dispatch('catalog/getParam')
        this.$store.dispatch('depo/getParamProc')
    }
};
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>