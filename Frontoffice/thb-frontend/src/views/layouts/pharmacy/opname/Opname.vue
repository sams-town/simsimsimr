<template>
    <div>
        <TopPharmacyMenu selectedMenu="menu11" />
        <div class="mt-32 col">
            <p class="text-bold-24">{{ active.tab === 'tab1' ? 'Aktivasi Stok Opname' : 'List Stok Opname' }}</p>
            <div class="row mx-0 mt-8 mb-20">
                <b-button @click="changeTab('tab1')" pill :variant="active.tab === 'tab1' ? 'primary' : 'outline-primary'"
                    class="mr-2">
                    <feather-icon icon="PlusIcon" /> Aktivasi Stok Opname
                </b-button>
                <b-button @click="changeTab('tab2')" pill :variant="active.tab === 'tab2' ? 'primary' : 'outline-primary'">
                    <feather-icon icon="MenuIcon" /> List Stok Opname
                </b-button>
            </div>
            <section v-if="active.tab === 'tab1'">
                <section class="card">
                    <div class="card-header bg-primary-200 font-weight-bold">Filter Data</div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-center">
                            <div class="col-lg-5 px-lg-5">
                                <div>
                                    <label class="c-form-label">Tanggal Stok Opname</label>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <b-input-group>
                                                <b-form-input v-model="datePick.start" type="text"
                                                    placeholder="YYYY-MM-DD" autocomplete="off"></b-form-input>
                                                <b-input-group-append>
                                                    <b-form-datepicker v-model="datePick.start" button-only
                                                        right></b-form-datepicker>
                                                </b-input-group-append>
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-6 mt-2 mt-lg-0">
                                            <b-input-group>
                                                <b-form-input v-model="datePick.end" type="text"
                                                    placeholder="YYYY-MM-DD" autocomplete="off"></b-form-input>
                                                <b-input-group-append>
                                                    <b-form-datepicker v-model="datePick.end" button-only
                                                        right></b-form-datepicker>
                                                </b-input-group-append>
                                            </b-input-group>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Status</label>
                                    <b-form-radio-group>
                                        <b-form-radio value="1">Aktif</b-form-radio>
                                        <b-form-radio value="2">Tidak Aktif</b-form-radio>
                                    </b-form-radio-group>
                                </div>
                            </div>
                            <div class="col-lg-5 px-lg-5">
                                <div class="mt-12 mt-lg-0">
                                    <label class="c-form-label">Keterangan</label>
                                    <b-textarea rows="3" placeholder="Depo/Buffer" />
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center mt-24">
                            <div class="col-lg-10 d-flex">
                                <b-button pill variant="outline-primary" class="px-3 mr-2 ml-auto">
                                    <feather-icon icon="XIcon" /> Reset
                                </b-button>
                                <b-button pill variant="primary" class="px-3">Simpan</b-button>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="mt-24 mt-lg-32">
                    <p class="mb-0 text-bold-24">List Data</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="row mx-0">
                            <span class="text-regular-14">Tampilkan</span>
                            <select id="per-page-select" v-model="ActivationTable.perPage"
                                class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                                <option value="5" selected>5</option>
                                <option value="10" selected>10</option>
                                <option value="15" selected>15</option>
                            </select>
                            <span class="text-regular-14">Entri</span>
                        </div>
                        <div class="col-6 col-lg-3 d-flex align-items-center position-relative">
                            <b-form-input size="sm" autocomplete="off" placeholder="Search" v-model="search.activation"
                                class="rounded-pill pl-5"></b-form-input>
                            <b-icon icon="search" class="position-absolute pl-3 text-neutral-400" />
                        </div>
                    </div>
                    <b-table bordered responsive small head-variant="light" :fields="ActivationTable.fields"
                        :items="filteredActivation" :current-page="ActivationTable.currentPage" :per-page="ActivationTable.perPage"
                        class="mt-12 text-center" thead-class="text-bold-14" tbody-tr-class="text-regular-14">
                        <template #cell(action)>
                            <div>
                                <b-button size="sm" variant="warning-500">
                                    <feather-icon icon="Edit2Icon" />
                                </b-button>
                            </div>
                        </template>
                    </b-table>
                    <div class="col px-0 mt-20">
                        <div class="d-flex">
                            <span class="text-regular-14">Menampilkan {{ firstActivationRow }} hingga {{ lastActivationRow }} dari {{
                                ActivationTable.totalRows }} entri</span>
                            <div class="ml-auto">
                                <b-pagination v-model="ActivationTable.currentPage" :total-rows="ActivationTable.totalRows"
                                    :per-page="ActivationTable.perPage" :current-page="ActivationTable.currentPage" align="fill"
                                    size="sm" class="my-0"></b-pagination>
                            </div>
                        </div>
                    </div>
                </section>
            </section>
            <section v-else-if="active.tab === 'add'">
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
                                <span class="font-weight-bold">DATA OPNAME</span>
                                <div class="d-flex">
                                    <!-- <router-link :to="{ name: 'PharmacyPlanningProcurementView' }" target="_blank">
                                        <b-button variant="primary-600" size="sm" class="mr-1">
                                            <feather-icon icon="PrinterIcon" />
                                        </b-button>
                                    </router-link> -->
                                    <!-- <b-button variant="primary-600" size="sm" @click.prevent="validationForm" class="mr-1">
                                        <feather-icon icon="SaveIcon" /> Simpan
                                    </b-button> -->
                                    <!-- <b-button variant="primary-600" size="sm">
                                        <feather-icon icon="RefreshCcwIcon" />
                                    </b-button> -->
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-center">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="row mt-12">
                                                <div class="col-lg-3 text-lg-right">
                                                    <label>No. Dokumen</label>
                                                </div>
                                                <div class="col-lg">
                                                    <b-form-input v-model="form.no_document"/>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-3 text-lg-right">
                                                    <label>Gudang</label>
                                                </div>
                                                <div class="col-lg">
                                                    <v-select v-model="form.pharmacy_depo_id" :options="DepoOptions" :reduce="DepoOptions => DepoOptions.value" label="title" placeholder="-- Pilih Gudang --"></v-select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="card mt-32">
                        <div class="card-header bg-primary-200 font-weight-bold">Total : . {{this.form.nfinish}}</div>x
                        <div class="card-body">
                            <!-- <table class="table">
                                <tr>
                                    <td>
                                        <v-select v-model="catalog" @input="onAddSC($event)" :options="CatalogOptions" :reduce="CatalogOptions => CatalogOptions" label="title" placeholder="-- Pilih Katalog --"></v-select>
                                    </td>
                                </tr>
                            </table> -->
                            <div class="col-6 col-lg-3 d-flex align-items-center position-relative">
                                <b-form-input size="sm" autocomplete="off" placeholder="Search" v-model="searchtable"
                                    class="rounded-pill pl-5"></b-form-input>
                                <b-icon icon="search" class="position-absolute pl-3 text-neutral-400" />
                            </div>
                            <div class="row mx-0">
                                <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                                    <span class="text-regular-14">Tampilkan</span>
                                    <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
                                    <span class="text-regular-14">Entri</span>
                                </b-form-group>
                            </div>
                            <b-table bordered responsive hover small head-variant="light" :fields="fieldsItem" :items="filteredListOpItem" :per-page="perPage" :current-page="currentPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                            <!-- <table class="table table-responsive table-bordered" responsive small bordered> -->
                                <!-- <thead class="text-center text-bold-14">
                                    <tr>
                                        <th rowspan="2">NO</th>
                                        <th rowspan="2">NAMA BARANG</th>
                                        <th rowspan="2">SAT</th>
                                        <th colspan="9">PERHITUNGAN STOK OPNAME</th>
                                    </tr>
                                    <tr>
                                        <th>BATCH&EXP</th>
                                        <th>SALDO AWAL</th>
                                        <th>JUMLAH FISIK</th>
                                        <th>SELISIH</th>
                                        <th>HARGA</th>
                                        <th>NILAI</th>
                                        <th>OPERATOR</th>
                                        <th>TIMESTAMPT</th>
                                    </tr>
                                </thead> -->
                                <!-- <tr class="item" v-for="(item, index) in filteredListOpItem">
                                    <td>
                                        {{ index+1 }}
                                    </td>
                                    <td>{{ item.catalog_name }}</td>
                                    <td>{{ item.package_name }}</td>
                                    <td><b-form-input type="text" v-model="item.batch" /><b-form-input type="date" v-model="item.expired_date" /></td>
                                    <td>{{ item.sum_adm }}</td>
                                    <td><b-form-input type="text" @keydown.enter="vpChanged(item.index)" v-model="item.accumulation" /></td>
                                    <td>{{ item.difference }}</td>
                                    <td>{{ item.hp | currency }}</td>
                                    <td>{{ item.ntotal | currency }}</td>
                                    <td>{{ item.user }}</td>
                                    <td>{{ item.timestampt }}</td>
                                </tr> -->
                                <template #cell(batch)="row">
                                    <b-form-input type="text" v-model="row.item.batch" /><b-form-input type="date" v-model="row.item.expired_date" />
                                    <v-select v-model="row.item.rack_id" :options="RackOptions" :reduce="RackOptions => RackOptions.value" label="title" placeholder="-- Pilih Nomor Rak --"></v-select>
                                    <v-select v-model="row.item.rackname_id" :options="RacknameOptions" :reduce="RacknameOptions => RacknameOptions.value" label="title" placeholder="-- Pilih Nama Rak --"></v-select>
                                </template>
                                <template #cell(accumulation)="row">
                                    <b-form-input type="text" @keydown.enter="vpChanged(row.item.index)" v-model="row.item.accumulation" />
                                </template>
                                <template #cell(ntotal)="row">
                                    {{ row.item.ntotal | currency }}
                                </template>
                                <template #cell(hp)="row">
                                    {{ row.item.hp | currency }}
                                </template>
                            <!-- </table> -->
                            </b-table>
                            <b-pagination
                            v-model="currentPage"
                            :total-rows="2429"
                            :per-page="perPage"
                            aria-controls="my-table"></b-pagination>
                            <p class="mt-3">Current Page: {{ currentPage }}</p>
                        </div>
                    </section>
                </b-overlay>
            </section>
            <section v-else>
                <section class="card">
                    <div class="card-header bg-primary-200 font-weight-bold">Filter Data</div>
                    <div class="card-body d-flex justify-content-center">
                        <div class="col-lg-5">
                            <div>
                                <div>
                                    <label class="c-form-label">Keterangan Aktifasi SO</label>
                                    <b-form-select />
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Unit Penyimpanan</label>
                                    <b-form-select />
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-24">
                                <b-button pill variant="outline-primary" class="px-3 mr-2">
                                    <feather-icon icon="XIcon" /> Reset
                                </b-button>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="mt-24 mt-lg-32">
                    <p class="mb-0 text-bold-24">List Data</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="row mx-0">
                            <span class="text-regular-14">Tampilkan</span>
                            <select id="per-page-select" v-model="ListTable.perPage"
                                class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                                <option value="5" selected>5</option>
                                <option value="10" selected>10</option>
                                <option value="15" selected>15</option>
                            </select>
                            <span class="text-regular-14">Entri</span>
                        </div>
                        <div class="col-6 col-lg-3 d-flex align-items-center position-relative">
                            <b-form-input size="sm" autocomplete="off" placeholder="Search" v-model="search.list"
                                class="rounded-pill pl-5"></b-form-input>
                            <b-icon icon="search" class="position-absolute pl-3 text-neutral-400" />
                        </div>
                    </div>
                    <b-table bordered responsive hover small head-variant="light" :items="items" :fields="fields" :per-page="0" :current-page="currentPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                        <template #cell(action)="row">
                            <div class="d-flex justify-content-center">
                                <b-button variant="primary-500" size="sm" class="mr-1" v-if="row.item.flag === 0">
                                    <feather-icon icon="EditIcon" @click="editData(row.item)" />
                                </b-button>
                                <b-button variant="primary-500" size="sm" class="mr-1" v-if="row.item.flag === 0">
                                    <feather-icon icon="DownloadIcon" @click="filterDataExcel(row.item.id)" />
                                </b-button>
                                <!-- <router-link :to="{ name: 'PharmacyOpnamePrintView' }" target="_blank">
                                    <b-button size="sm" variant="success-500" class="mr-2">
                                        <feather-icon icon="PrinterIcon" /> Print
                                    </b-button>
                                </router-link>
                                <b-button size="sm" variant="success-500">
                                    <feather-icon icon="FileTextIcon" /> Excel
                                </b-button> -->
                            </div>
                        </template>
                        <template #cell(flag)="row">
                            <div v-if="row.item.flag === 1">
                                <feather-icon icon="CheckCircleIcon" />
                            </div>
                            <div v-if="row.item.flag === 0">
                                <feather-icon icon="XCircleIcon" />
                            </div>
                        </template>
                        <template #cell(so_closed)>
                            <div>
                                <feather-icon icon="AlertTriangleIcon" />
                            </div>
                        </template>
                    </b-table>
                    <div class="col px-0 mt-20">
                        <div class="d-flex">
                            <span class="text-regular-14">Menampilkan {{ firstListRow }} hingga {{ lastListRow }} dari {{
                                ListTable.totalRows }} entri</span>
                            <div class="ml-auto">
                                <b-pagination v-model="ListTable.currentPage" :total-rows="ListTable.totalRows"
                                    :per-page="ListTable.perPage" :current-page="ListTable.currentPage" align="fill"
                                    size="sm" class="my-0"></b-pagination>
                            </div>
                        </div>
                    </div>
                </section>
            </section>
        </div>
    </div>
</template>

<script>
import { mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import TopPharmacyMenu from '@/@core/components/top-menu/TopPharmacyMenu.vue';
import vSelect from 'vue-select'
import moment from 'moment'
export default {
    components: {
        TopPharmacyMenu,vSelect
    },
    data() {
        return {
            urlRoot: process.env.VUE_APP_ROOT_API,
            userData: JSON.parse(localStorage.getItem('userData')), 
            params:{
                offset:0,
                limit:20,
                code:'',
                name:''
            },
            form: {
                id:'',
                pharmacy_depo_id:'',
                no_document:'',
                ntotal:'',
                nfinish:'',
                opname_item:[],
                date:''
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
            fieldsItem: [
                { key: 'no', label: 'NO' }, //thStyle: { minWidth: '140px' }
                { key: 'catalog_name', label: 'NAMA BARANG' }, //thStyle: { minWidth: '140px' }
                { key: 'package_name', label: 'SAT' }, //thStyle: { minWidth: '140px' }
                { key: 'batch', label: 'BATCH&EXP' }, //thStyle: { minWidth: '140px' }
                { key: 'sum_adm', label: 'SALDO AWAL' }, //thStyle: { minWidth: '140px' }
                { key: 'accumulation', label: 'JUMLAH FISIK' }, //thStyle: { minWidth: '140px' }
                { key: 'difference', label: 'SELISIH' }, //thStyle: { minWidth: '140px' }
                { key: 'hp', label: 'HARGA' }, //thStyle: { minWidth: '140px' }
                { key: 'ntotal', label: 'NILAI' }, //thStyle: { minWidth: '140px' }
                { key: 'user', label: 'OPERATOR' }, //thStyle: { minWidth: '140px' }
                { key: 'timestampt', label: 'TIMESTAMPT' }, //thStyle: { minWidth: '140px' }
            ],
            openModal: {
                therapy_class: false,
                therapy_subclass: false,
                generic: false,
                item_group: false,
                factory: false,
                code: false,
                catalog_code: false,
            },
            search: {
                activation: '',
                list: '',
            },
            active: {
                tab: 'tab2',
            },
            datePick: {
                start: '',
                end: '',
            },
            ActivationTable: {
                fields: [
                    { key: 'action', label: 'Aksi' }, //thStyle: { minWidth: '140px' }
                    { key: 'start_date', label: 'Tgl Mulai Stok Opname' },
                    { key: 'end_date', label: 'Tgl Berakhir Stok Opname' },
                    { key: 'notes', label: 'Keterangan' },
                    { key: 'status', label: 'Status' },
                    { key: 'operator', label: 'Operator' },
                    { key: 'update', label: 'Update' },
                ],
                items: [
                    {
                        start_date: '30-10-2021 13:37:35',
                        end_date: '02-11-2021 13:37:35',
                        notes: 'SO BULAN OKTOBER 2021',
                        status: 'Berjalan',
                        operator: 'Rachmat Hidayat',
                        update: '30-10-2021 13:37:57',
                    },
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
            ListTable: {
                fields: [
                    { key: 'action', label: 'Aksi', thStyle: { minWidth: '170px' } },
                    { key: 'storage_unit', label: 'Unit Penyimpanan' },
                    { key: 'notes', label: 'Keterangan' },
                    { key: 'so_activation', label: 'Aktivasi SO' },
                    { key: 'date_activation', label: 'Tgl Aktivasi' },
                    { key: 'operator_activation', label: 'Operator Aktivasi' },
                    { key: 'so_closed', label: 'Tutup SO' },
                    { key: 'so_closed_date', label: 'Tgl Tutup SO' },
                    { key: 'so_closed_operator', label: 'Operator Tutup SO' },
                ],
                items: [
                    {
                        storage_unit: 'ALAMANDA',
                        notes: 'SO BULAN OKTOBER 2021 Unit: ALAMANDA',
                        date_activation: '01-11-2021 11:56:42',
                        operator_activation: 'Vidyanti Permata D, S.Farm, Apt',
                        so_closed_date: '01-11-2021 14:16:33',
                        so_closed_operator: 'Hani Ramadani',
                    },
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
            obj:{},
            searchtable:''
        }
    },
    watch: {
        'form.type_how_to_enter_id' (obj) {
            return this.$store.dispatch('howtoenter/getParam', obj.value)
        },
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.$store.dispatch('opname/getAll',this.params)
            }
        },
        // perPage: {
        //     handler: function(value) {
        //         this.params.limit = this.perPage
        //         this.$store.dispatch('opname/getAll',this.params)
        //     }
        // }
    },
    computed: {
        ...mapState({ 
            RackOptions: state => state.rack.options.rack, 
            RacknameOptions: state => state.rackname.options.rackname, 
            totalRows: state => state.opname.totalRows, 
            fields: state => state.opname.fields, 
            items: state => state.opname.items, 
            pageOptions: state => state.opname.pageOptions, 
            CatalogOptions: state => state.catalog.options.catalog, 
            DepoOptions: state => state.depo.options.depo, 
        }),
        firstRow() {
            return (this.Planningtable.currentPage - 1) * this.Planningtable.perPage + 1
        },
        lastRow() {
            return Math.min(this.Planningtable.currentPage * this.Planningtable.perPage, this.Planningtable.totalRows)
        },
        filteredActivation() {
            const searchTerm = this.search.activation.toLowerCase().trim()
            if (!searchTerm) {
                return this.ActivationTable.items
            }
            return this.ActivationTable.items.filter(item => {
                // Update the condition based on the structure of the items array
                return item.start_date.toLowerCase().includes(searchTerm) ||
                    item.end_date.toLowerCase().includes(searchTerm) ||
                    item.notes.toLowerCase().includes(searchTerm) ||
                    item.status.toLowerCase().includes(searchTerm) ||
                    item.operator.toLowerCase().includes(searchTerm) ||
                    item.update.toLowerCase().includes(searchTerm)
            })
        },
        firstActivationRow() {
            return (this.ActivationTable.currentPage - 1) * this.ActivationTable.perPage + 1
        },
        lastActivationRow() {
            return Math.min(this.ActivationTable.currentPage * this.ActivationTable.perPage, this.ActivationTable.totalRows)
        },
        filteredListOpItem() {
            return this.form.opname_item.filter((rad) => {
                const computedOpItemObj = { ...rad}
                return Object.keys(computedOpItemObj)
                    .some(key => ('' + computedOpItemObj[key]).toLowerCase().includes(this.searchtable.toLowerCase()))
            })
        },
        filteredList() {
            const searchTerm = this.search.list.toLowerCase().trim()
            if (!searchTerm) {
                return this.ListTable.items
            }
            return this.ListTable.items.filter(item => {
                // Update the condition based on the structure of the items array
                return item.storage_unit.toLowerCase().includes(searchTerm) ||
                    item.notes.toLowerCase().includes(searchTerm) ||
                    item.date_activation.toLowerCase().includes(searchTerm) ||
                    item.operator_activation.toLowerCase().includes(searchTerm) ||
                    item.so_closed_date.toLowerCase().includes(searchTerm) ||
                    item.so_closed_operator.toLowerCase().includes(searchTerm)
            })
        },
        firstListRow() {
            return (this.ListTable.currentPage - 1) * this.ListTable.perPage + 1
        },
        lastListRow() {
            return Math.min(this.ListTable.currentPage * this.ListTable.perPage, this.ListTable.totalRows)
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
        filterDataExcel (id) {
            window.open(this.urlRoot+`pharmacy/opname/item_excel/${id}`)
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        changeTab(tab) {
            this.active.tab = tab
        },
        vpChanged(index){
            let text = "Apakah Anda Yakin?";
            if (confirm(text) == true) {
                let formData = {
                    id:this.form.opname_item[index].id,
                    batch:this.form.opname_item[index].batch,
                    expired_date:this.form.opname_item[index].expired_date,
                    accumulation:this.form.opname_item[index].accumulation,
                    rack_id:this.form.opname_item[index].rack_id,
                    rackname_id:this.form.opname_item[index].rackname_id,
                    user:this.userData.employee_name,
                }
                this.$store.dispatch('opname/formPostPutItem', formData)
                .then(res => {
                    // this.form.opname_item[index].difference = this.form.opname_item[index].accumulation-this.form.opname_item[index].sum_adm
                    // this.form.opname_item[index].ntotal = this.form.opname_item[index].difference*this.form.opname_item[index].hp
                    // this.form.opname_item[index].rack_id = this.form.opname_item[index].rack_id
                    // this.form.opname_item[index].rackname_id = this.form.opname_item[index].rackname_id
                    // this.form.opname_item[index].timestampt = moment().format('DD-MM-YYYY h:mm:ss')
                    // this.form.opname_item[index].user = this.userData.employee_name
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.editData (this.obj)
                }, 
                err => {
                    this.show = false
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                })
            }
        },
        editData (obj) {
            console.log(obj)
            this.active.tab = 'add'
            this.obj = obj
            this.form.id = this.obj.id
            this.form.no_document = this.obj.no_document
            this.form.pharmacy_depo_id = this.obj.pharmacy_depo_id
            this.$store.dispatch('rack/getParam',this.obj.pharmacy_depo_id)
            this.$store.dispatch('rackname/getParam','4427ba40-c9e1-4636-aa77-f825b82a9eae')
            this.form.opname_item = []
            this.$store.dispatch('opname/getOpnameItem', this.obj.id)
            .then(res => {
                for (let i = 0; i < res.length; i++) {
                    this.form.opname_item.push(
                        { 
                            no:i+1,
                            index:i,
                            ntotal:res[i].accumulation*res[i].hp,
                            accumulation:res[i].accumulation,
                            difference:res[i].accumulation-res[i].sum_adm,
                            batch:res[i].batch,
                            catalog_name:res[i].catalog_name,
                            expired_date:res[i].expired_date,
                            flag:res[i].flag,
                            hna:res[i].hna,
                            hp:res[i].hp,
                            id:res[i].id,
                            opname_id:res[i].opname_id,
                            package_big_id:res[i].package_big_id,
                            package_big_name:res[i].package_big_name,
                            package_id:res[i].package_id,
                            package_name:res[i].package_name,
                            pharmacy_catalog_id:res[i].pharmacy_catalog_id,
                            sort:res[i].sort,
                            sum_adm:res[i].sum_adm,
                            sum_physical:res[i].sum_physical,
                            user:res[i].user_bak,
                            rack_id:res[i].rack_id,
                            name_rack:res[i].name_rack,
                            rackname_id:res[i].rackname_id,
                            timestampt:res[i].timestampt,
                            action: 'action'
                        }
                    )
                }
            })
            this.show = false
        }
    },
    created () {
        this.$store.dispatch('opname/getAll',this.params)
        this.$store.dispatch('depo/getParam')
        this.$store.dispatch('catalog/getParam')
    }
} 
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>