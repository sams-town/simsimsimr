<template>
    <div>
        <TopPharmacyMenu selectedMenu="menu3" />
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
                                                <label class="c-form-label">No Dokumen PO</label>
                                                <b-form-input v-model="params.no_document_po" />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mt-1">
                                            <div>
                                                <label class="c-form-label">Nama PBF</label>
                                                <v-select v-model="params.supplier_id" :options="SupplierOptions" :reduce="SupplierOptions => SupplierOptions.value" label="title" placeholder="-- Pilih Supplier --"></v-select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mt-1">
                                            <label class="c-form-label">Tanggal PO</label>
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
                                        <div class="col-lg-4 mt-1">
                                            <div>
                                                <label class="c-form-label">Katalog</label>
                                                <v-select v-model="params.pharmacy_catalog_id" :options="CatalogOptions" :reduce="CatalogOptions => CatalogOptions.value" label="title" placeholder="-- Pilih Katalog --"></v-select>
                                            </div>
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
                        <template #cell(is_delete)="data">
                            <b-badge :variant="status[1][data.value]">
                            {{ status[0][data.value] }}
                            </b-badge>
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
                            <template v-if="row.item.validation_key_1 !== null">
                                <b-badge variant="primary">Tervalidasi</b-badge>
                            </template>
                            <template v-else>
                                <b-badge variant="danger">Belum Tervalidasi</b-badge>
                            </template>
                        </template>
                        <template #cell(action)="row">
                            <div class="row mx-0">
                                <template v-if="row.item.validation_key_1 === ''">
                                    <b-button variant="warning-500" size="sm" class="mr-1" @click="editData(row.item)">
                                        <feather-icon icon="EditIcon"  />
                                    </b-button>
                                    <b-button variant="danger-500" size="sm" @click="rowOnDelete(row.item.id)">
                                        <feather-icon icon="TrashIcon"  />
                                        <b-link v-b-modal.modal-center>
                                        </b-link>
                                    </b-button>
                                </template>
                                <template v-else>
                                    <b-button variant="primary-500" size="sm" class="mr-1">
                                        <feather-icon icon="EditIcon" @click="editData(row.item)" />
                                    </b-button>
                                </template>
                                <b-button size="sm" variant="info-700" @click="sendData(row.item)" v-if="row.item.is_accurate === 0 && row.item.validation_key_1">
                                    <feather-icon icon="SendIcon" />
                                </b-button>
                                <router-link :to="{ path: '/pharmacy/receive/print/'+ row.item.id}" v-if="row.item.validation_key_1">
                                    <b-button size="sm" class="ml-1" variant="info-700">
                                        <feather-icon icon="PrinterIcon" />
                                    </b-button>
                                </router-link>
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
                                <span class="font-weight-bold">DATA PENERIMAAN</span>
                                <div class="d-flex">
                                    <!-- <router-link :to="{ name: 'PharmacyPlanningProcurementView' }" target="_blank">
                                        <b-button variant="primary-600" size="sm" class="mr-1">
                                            <feather-icon icon="PrinterIcon" />
                                        </b-button>
                                    </router-link> -->
                                    <b-button variant="primary-600" size="sm" @click.prevent="isRefPrice()" class="mr-1">
                                        <feather-icon icon="SaveIcon" /> Simpan
                                    </b-button>
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
                                        <div class="col-lg-7">
                                            <!-- <div class="row mt-12">
                                                <div class="col-lg-3 text-lg-right">
                                                    <label>No. Dokumen</label>
                                                </div>
                                                <div class="col-lg">
                                                    <b-form-input v-model="form.no_document"/>
                                                </div>
                                            </div> -->
                                            <div class="row mt-1">
                                                <div class="col-lg-3 text-lg-right">
                                                    <label>Reff Pembelian</label>
                                                </div>
                                                <div class="col-lg">
                                                    <v-select v-model="form.no_purchase_select" @input="onAddPurchase($event)" :options="PurchaseOptions" :reduce="PurchaseOptions => PurchaseOptions" label="title" placeholder="-- Pilih Referensi Pembelian --"></v-select>
                                                </div>
                                            </div>
                                            <div class="row mt-1">
                                                <div class="col-lg-3 text-lg-right">
                                                    <label>Reff Perencanaan</label>
                                                </div>
                                                <div class="col-lg">
                                                    <v-select v-model="form.no_planning_select" :options="PlanningOptions" :reduce="PlanningOptions => PlanningOptions" label="title" placeholder="-- Pilih Referensi Perencanaan --"></v-select>
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
                                            <div class="row mt-12">
                                                <div class="col-lg-3 text-lg-right">
                                                    <label>No. Faktur</label>
                                                </div>
                                                <div class="col-lg">
                                                    <b-form-input v-model="form.no_invoice"/>
                                                </div>
                                            </div>
                                            <div class="row mt-1">
                                                <div class="col-lg-3 text-lg-right">
                                                    <label>Cara Bayar</label>
                                                </div>
                                                <div class="col-lg">
                                                    <v-select v-model="form.payment_method_id" :options="PaymentMethodOptions" :reduce="PaymentMethodOptions => PaymentMethodOptions.value" label="title" placeholder="-- Pilih Cara Bayar --"></v-select>
                                                </div>
                                            </div>
                                            <div class="row mt-1">
                                                <div class="col-lg-3 text-lg-right">
                                                    <label>Supplier</label>
                                                </div>
                                                <div class="col-lg">
                                                    <v-select v-model="form.supplier_id" :options="SupplierOptions" :reduce="SupplierOptions => SupplierOptions.value" label="title" placeholder="-- Pilih Supplier --"></v-select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <div class="col-lg-3 text-lg-right">
                                                    <label>Gudang</label>
                                                </div>
                                                <div class="col-lg">
                                                    <b-input-group>
                                                        <b-input-group-prepend is-text checked>
                                                            <b-form-radio type="radio" v-model="form.pharmacy_depo_id" value="8da5d967-18da-4f87-86dc-0a9416611c11" :checked="true"/>
                                                        </b-input-group-prepend>
                                                        <b-form-input value="INSTALASI FARMASI" disabled></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div class="row mt-12">
                                                <div class="col-lg-3 text-lg-right">
                                                    <label>Total</label>
                                                </div>
                                                <div class="col-lg">
                                                    <b-input-group prepend="Total">
                                                        <b-form-input readonly v-model="form.ntotal" class="text-right" />
                                                    </b-input-group>
                                                    <b-input-group prepend="Diskon Item">
                                                        <b-form-input readonly v-model="form.ndiscount" class="text-right" />
                                                    </b-input-group>
                                                    <b-input-group prepend="Ongkir">
                                                        <b-form-input readonly @change="vpChangedDeliveryFee($event)" v-model="form.delivery_fee" class="text-right" />
                                                    </b-input-group>
                                                    <b-input-group prepend="Diskon Global %">
                                                        <b-form-input readonly @change="vpChangedDiscTotal($event)" v-model="form.ndiscountotal" class="text-right" />
                                                    </b-input-group>
                                                    <b-input-group prepend="Nilai Total">
                                                        <b-form-input readonly v-model="form.nfinishitem" class="text-right" />
                                                    </b-input-group>
                                                    <!-- <b-input-group prepend="PPN">
                                                        <b-form-input readonly v-model="form.ntax" class="text-right" />
                                                    </b-input-group>
                                                    <b-input-group prepend="Nilai Total + PPN">
                                                        <b-form-input readonly v-model="form.nntax" class="text-right" />
                                                    </b-input-group> -->
                                                    <b-input-group prepend="Nilai Akhir">
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
                        <div class="card-header bg-primary-200 font-weight-bold">Total : Rp. {{this.form.nfinish}}</div>
                        <div class="card-body">
                            <!-- <table class="table">
                                <tr>
                                    <td>
                                        <v-select v-model="catalog" @input="onAddSC($event)" :options="CatalogOptions" :reduce="CatalogOptions => CatalogOptions" label="title" placeholder="-- Pilih Katalog --"></v-select>
                                    </td>
                                </tr>
                            </table> -->
                            <table class="table table-responsive table-bordered" responsive small bordered>
                                <thead class="text-center text-bold-14">
                                    <tr>
                                        <th rowspan="2">
                                            <!-- <b-form-checkbox /> -->
                                        </th>
                                        <th rowspan="2">NO</th>
                                        <th rowspan="2">KODE</th>
                                        <th rowspan="2" style="min-width: 200px;">NAMA BARANG</th>
                                        <th rowspan="2">EXPIRED&BATCH</th>
                                        <th colspan="2">PO</th>
                                        <th colspan="2">PENERIMAAN</th>
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
                                        <th style="min-width: 140px;">KEMASAN</th>
                                        <th style="min-width: 140px;">SATUAN</th>
                                        <th style="min-width: 140px;">SEBELUM DISC</th>
                                        <th style="min-width: 140px;">DISC</th>
                                        <th style="min-width: 140px;">NOMINAL</th>
                                    </tr>
                                </thead>
                                <tr class="item" v-for="(item, index) in form.receipt_items">
                                    <td>
                                        <!-- <b-form-checkbox v-model="item.index" /> -->
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
                                    <td>
                                        <b-form-input type="date" v-model="item.expired_date" />
                                        <b-form-input type="text" v-model="item.batch" placeholder="BATCH"/>
                                    </td>
                                    <td>
                                        <v-select v-model="item.package_id_po" disabled :options="PackageOptions" :reduce="PackageOptions => PackageOptions.value" label="title" placeholder="Kemasan"></v-select>
                                    </td>
                                    <td>
                                        <b-form-input type="text" disabled v-model="item.value_package_po" />
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
                                    <!-- <td>
                                        <b-button variant="danger-600" size="sm" @click="deleteSC(item.index+1)" class="mr-2">
                                            <feather-icon icon="TrashIcon" />
                                        </b-button>
                                    </td> -->
                                </tr>
                                <tr>
                                    <td colspan="14">&nbsp;</td>
                                    <td><b-form-input type="text" v-model="form.ntotalb" readonly/></td>
                                    <td><b-form-input type="text" v-model="form.ndiscount" readonly/></td>
                                    <td><b-form-input type="text" v-model="form.nfinishitem" readonly/></td>
                                </tr>
                                <tr>
                                    <td colspan="18">&nbsp;</td>
                                </tr>
                            </table>
                        </div>
                    </section>
                    <b-modal v-model="active.modal_price" scrollable size="lg" title="Review Harga Jual" hide-footer>
                        <div class="col">
                            <ModernLabel label="Review Harga Jual" />
                            <b-table small responsive head-variant="light" :fields="fieldsPrice" :items="itemsPrice">
                                <template #cell(no)="row">
                                    <div>{{ row.index+1 }}</div>
                                </template>
                                <template #cell(hp_item)="data" >
                                    <div class="text-regular-16"> {{ data.value | currency }}</div>
                                </template>
                                <template #cell(hja_item)="data" >
                                    <div class="text-regular-16"> {{ data.value | currency }}</div>
                                </template>
                                <template #cell(new_hja_item)="row">
                                    <span style="background-color: greenyellow;">
                                        <template v-for="(items, indexs) in form.receipt_items" v-if="row.item.pharmacy_catalog_id === items.pharmacy_catalog_id">
                                            {{ (items.price_item_view*1.3) | currency}}
                                        </template>
                                    </span>
                                </template>
                                <!-- <template #cell(type_allergy_id)="data" :fields="fieldsPrice" >
                                    <div class="text-regular-16">
                                        <b-badge size="sm" :variant=" data.value === '1' ? 'success-600' : 'info-600'" class="p-2 font-weight-normal">
                                            <span>
                                                {{ data.value === '1' ? 'Obat' : 'Makanan' }}
                                            </span>
                                        </b-badge>
                                    </div>
                                </template>
                                <template #cell(action)>
                                    <div>
                                        <b-button size="sm" variant="danger-500">
                                            <b-icon icon="trash" class="mr-2" /> <span>Hapus</span>
                                        </b-button>
                                    </div>
                                </template> -->
                            </b-table>
                            <b-button variant="primary-600" size="sm" @click.prevent="validationForm" class="mr-1">
                                <feather-icon icon="SaveIcon" /> Simpan
                            </b-button>
                        </div>
                    </b-modal>
                </b-overlay>
            </div>
        </section>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import TopPharmacyMenu from '@/@core/components/top-menu/TopPharmacyMenu.vue';
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import vSelect from 'vue-select'
import moment from 'moment'
export default {
    name: 'PatientPayment',
    components: {
        TopPharmacyMenu,vSelect,ModernLabel
    },
    data() {
        return {
            fieldsPrice: [
                { key: 'no', label: 'No', thStyle:'width: 50px' },
                { key: 'catalog_name', label: 'Nama Katalog', thStyle:'width: 500px' },
                { key: 'hp_item', label: 'Harga Perolehan' },
                { key: 'hja_item', label: 'Harga Jual' },
                { key: 'new_hja_item', label: 'Harga Penerimaan Baru' },
                { key: 'date_created', label: 'Last Update' },
            ],
            openModal: false,
            userData: JSON.parse(localStorage.getItem('userData')),
            active: {
                modal_price: false,
                main_tab: 'list',
            },
            params:{
                pharmacy_catalog_id:'',
                no_document:'',
                date_start:'',
                date_end:'',
                supplier_id:0,
                offset:0,
                limit:20,
                code:'',
                name:''
            },
            itemsPrice:[],
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
            datenow:moment().format('DD-MM-YYYY h:mm:ss'),
            form: {
                id:'',
                no_document:'',
                is_ppn:'1',
                pharmacy_planning_id:'',
                no_planning_select:'',
                purchase_order_id:'',
                no_invoice:'',
                flag:0,
                date:'',
                no_purchase_select:'',
                pharmacy_depo_id:'8da5d967-18da-4f87-86dc-0a9416611c11',
                supplier_id:'',
                payment_method_id:'',
                ntotalb:'',
                ntotal:'',
                ndiscount:'',
                ndiscountotal:'',
                ndiscounttotal:'',
                ntax:11,
                nntax:'',
                nround:'',
                nfinish:'',
                nfinishitem:'',
                validation_key_1:'',
                receipt_items:[],
                transaction_signature_val:[]
            },
            validation_key_1:'eba5ed94-e863-43c7-bb57-197c33678b4d',
            validation_key_1_show:true,
            fill_ttd:false,
            is_ppn:false,
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
                this.$store.dispatch('receipt/getAll',this.params)
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.$store.dispatch('receipt/getAll',this.params)
            }
        }
    },
    computed: {
        ...mapState({ 
            totalRows: state => state.receipt.totalRows, 
            fields: state => state.receipt.fields, 
            items: state => state.receipt.items, 
            pageOptions: state => state.receipt.pageOptions, 
            CatalogOptions: state => state.catalog.options.catalog, 
            PackageOptions: state => state.packages.options.packages,
            SignatureItems: state => state.signaturevalidation.items,
            PurchaseOptions: state => state.purchaseorder.options.purchase,
            PlanningOptions: state => state.planning.options.planning,
            SupplierOptions: state => state.company.options.supplier, 
            PaymentMethodOptions: state => state.paymentmethod.options.paymentmethod
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
            return new Intl.NumberFormat().format(value)
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
        deleteSC (index) {
            this.form.receipt_items.splice(index, 1)
            this.sumAll()
        },
        isRefPrice() {
            let element = []
            for (let ip = 0; ip < this.form.receipt_items.length; ip++) {
                element.push(this.form.receipt_items[ip].pharmacy_catalog_id);
            }
            this.$store.dispatch('receipt/getPrice',element).then(res => {
                this.itemsPrice = res.rows
            })
            this.active.modal_price = !this.active.modal_price
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
        vpChangedDeliveryFee (event) {
            this.sumAll()
        },
        vpChangedDiscTotal (event) {
            this.sumAll()
        },
        onAddSC (event) {
            this.catalog = ''
            this.collation = ''
            this.form.receipt_items.push(
                { 
                    id:'',
                    index:0,
                    code:event.data_catalog.code,
                    name:event.data_catalog.name,
                    pharmacy_catalog_id:event.data_catalog.id, 
                    package_id:event.data_catalog.package_id,
                    package_big_id:event.data_catalog.package_big_id,
                    package_name_big:event.data_catalog.package_name_big,
                    package_name:event.data_catalog.package_name,
                    value_package:0,
                    sum_package:event.data_catalog.value_package+' '+event.data_catalog.package_name, 
                    sum_item:'0 '+event.data_catalog.package_name, 
                    price_package:'0', 
                    price_item:'0', 
                    discount_item:'0', 
                    discount_price:'0', 
                    nt:'0', 
                    nf:'0', 
                    sort:this.form.receipt_items.length, 
                    data_catalog:event,
                    action: 'action'
                }
            )
        },
        onAddPurchase (event) {
            console.log(event)
            this.form.flag = ''
            this.form.date = ''
            this.form.no_invoice = ''
            this.form.ntotal = ''
            this.form.ndiscount = ''
            this.form.nmargins = ''
            this.form.nfinish = ''
            this.form.nfinishitem = ''
            this.form.receipt_items = []
            let pi = {}
            this.form.pharmacy_planning_id = event.purchase.no_document_planning
            this.form.payment_method_id = event.purchase.payment_method_id
            this.form.pharmacy_depo_id = event.purchase.pharmacy_depo_id
            this.form.supplier_id = event.purchase.supplier_id
            this.form.no_planning_select = {
                title:event.purchase.no_document_planning,
                value:event.purchase.pharmacy_planning_id
            }
            this.form.purchase_order_id = event.purchase.id
            this.form.no_purchase_select = {
                title:event.purchase.no_document,
                value:event.purchase.id
            }
            // if (event.purchase.is_ppn == 1) {
            //     this.is_ppn = true
            // } else {
            //     this.is_ppn = false
            // }
            // this.form.is_ppn = event.purchase.is_ppn
            this.form.delivery_fee = this.$options.filters.numberToDecimalCurr(event.purchase.delivery_fee)
            this.form.ndiscountotal = event.purchase.ndiscountotal
            this.form.ndiscounttotal = event.purchase.ndiscounttotal
            this.form.ntotalb = this.$options.filters.numberToDecimalCurr(event.purchase.ntotal)
            this.form.ntotal = this.$options.filters.numberToDecimalCurr(event.purchase.ntotal)
            this.form.ndiscount = this.$options.filters.numberToDecimalCurr(event.purchase.ndiscount)
            this.form.ntax = 11
            if (this.is_ppn === true) {
                this.form.nntax = event.purchase.nfinish*this.form.ntax/100
                this.form.nfinish = event.purchase.nfinish + (event.purchase.nfinish*this.form.ntax/100)
            } else {
                this.form.nfinish = event.purchase.nfinish
                this.form.nntax = 0
            }
            this.form.nntax = this.$options.filters.numberToDecimalCurr(this.form.nntax)
            this.form.nfinish = this.$options.filters.numberToDecimalCurr(this.form.nfinish)
            this.form.nfinishitem = this.$options.filters.numberToDecimalCurr(event.purchase.ntotal)
            console.log(event.purchase.purchase_order_items);
            for (let i = 0; i < event.purchase.purchase_order_items.length; i++) {
                pi = {
                    expired_date:'',
                    batch:'',
                    signature_validation_id:event.purchase.purchase_order_items[i].id,
                    pharmacy_purchase_order_id:event.purchase.purchase_order_items[i].pharmacy_purchase_order_id,
                    index:event.purchase.purchase_order_items[i].sort,
                    code:event.purchase.purchase_order_items[i].code,
                    name:event.purchase.purchase_order_items[i].pharmacy_catalog_name,
                    pharmacy_catalog_id:event.purchase.purchase_order_items[i].pharmacy_catalog_id, 
                    package_id:event.purchase.purchase_order_items[i].package_id,
                    package_big_id:event.purchase.purchase_order_items[i].package_big_id,
                    package_id_po:event.purchase.purchase_order_items[i].package_big_id,
                    package_name_big:event.purchase.purchase_order_items[i].package_name_big,
                    package_name:event.purchase.purchase_order_items[i].package_name,
                    value_package_po:this.$options.filters.numberToDecimal(event.purchase.purchase_order_items[i].value_package),
                    value_package:this.$options.filters.numberToDecimal(event.purchase.purchase_order_items[i].value_package),
                    sum_package:this.$options.filters.numberToDecimal(event.purchase.purchase_order_items[i].sum_package)+' '+event.purchase.purchase_order_items[i].package_name, 
                    sum_item:this.$options.filters.numberToDecimal(event.purchase.purchase_order_items[i].sum_item)+' '+event.purchase.purchase_order_items[i].package_name, 
                    price_package:this.$options.filters.numberToDecimalCurr(event.purchase.purchase_order_items[i].price_package), 
                    price_item_view:event.purchase.purchase_order_items[i].price_item,
                    price_item:this.$options.filters.numberToDecimalCurr(event.purchase.purchase_order_items[i].price_item), 
                    discount_item:event.purchase.purchase_order_items[i].discount_item, 
                    discount_price:this.$options.filters.numberToDecimalCurr(event.purchase.purchase_order_items[i].discount_price), 
                    nt:this.$options.filters.numberToDecimalCurr(event.purchase.purchase_order_items[i].price_item*event.purchase.purchase_order_items[i].sum_item), 
                    nf:this.$options.filters.numberToDecimalCurr((event.purchase.purchase_order_items[i].price_item*event.purchase.purchase_order_items[i].sum_item)-((event.purchase.purchase_order_items[i].price_item*event.purchase.purchase_order_items[i].sum_item)*event.purchase.purchase_order_items[i].discount_item/100)), 
                    sort:event.purchase.purchase_order_items[i].sort, 
                    action: 'action'
                }
                this.form.receipt_items.push(pi)
            }
            this.sumAll()
        },
        validationForm () {
            this.show = true
            const formData = this.form 
            this.$store.dispatch('receipt/formPostPut', formData)
            .then(res => {
                this.notification('Success','CheckIcon','success','You have successfully')
                this.$store.dispatch('receipt/getAll', this.params)
                this.active.main_tab = 'list'
                this.form.id = ''
                this.form.no_document = ''
                this.form.no_invoice = ''
                this.form.date = ''
                this.form.flag = 0
                this.form.pharmacy_planning_id = ''
                this.form.no_planning_select = ''
                this.form.purchase_order_id = ''
                this.form.no_purchase_select = ''
                this.form.pharmacy_depo_id = '8da5d967-18da-4f87-86dc-0a9416611c11'
                this.form.supplier_id = ''
                this.form.payment_method_id = ''
                this.form.ntotal = ''
                this.form.ndiscount = ''
                this.form.ntax = 11
                this.form.nntax = ''
                this.form.nround = ''
                this.form.nfinish = ''
                this.form.nfinishitem = ''
                this.form.receipt_items = []
                this.form.transaction_signature_val = []
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
            this.$store.dispatch('receipt/getAll',this.params)
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
            this.form.no_document = ''
            this.form.no_invoice = ''
            this.form.date = ''
            this.form.flag = 0
            this.form.pharmacy_planning_id = ''
            this.form.no_planning_select = ''
            this.form.purchase_order_id = ''
            this.form.no_purchase_select = ''
            this.form.pharmacy_depo_id = '8da5d967-18da-4f87-86dc-0a9416611c11'
            this.form.supplier_id = ''
            this.form.payment_method_id = ''
            this.form.ntotal = ''
            this.form.ndiscount = ''
            this.form.ntax = 11
            this.form.nntax = ''
            this.form.nround = ''
            this.form.nfinish = ''
            this.form.nfinishitem = ''
            this.form.receipt_items = []
            this.form.transaction_signature_val = []
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
            let sum_package = this.$options.filters.decimalToNumber(this.form.receipt_items[index].sum_package)
            this.form.receipt_items[index].value_package = this.$options.filters.numberToDecimal(value_package)
            this.form.receipt_items[index].sum_item = this.$options.filters.numberToDecimal(value_package * sum_package) + ' ' + this.form.receipt_items[index].package_name
            let price_item = this.$options.filters.decimalToNumber(this.form.receipt_items[index].price_item)
            let sum_item = this.$options.filters.decimalToNumber(this.form.receipt_items[index].sum_item)
            let discount_item = this.form.receipt_items[index].discount_item
            this.form.receipt_items[index].price_item = this.$options.filters.numberToDecimalCurr(price_item)
            this.form.receipt_items[index].price_package = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            this.form.receipt_items[index].nt = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            if (discount_item > 0.00) {
                this.form.receipt_items[index].discount_price = this.$options.filters.numberToDecimalCurr(((price_item * sum_item)*discount_item)/100)
                this.form.receipt_items[index].nf = this.$options.filters.numberToDecimalCurr((price_item * sum_item)-((price_item * sum_item)*discount_item)/100)
            } else {
                this.form.receipt_items[index].nf = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            }
            this.sumAll()
        },
        spChanged($event, index){
            let sum_package = this.$options.filters.decimalToNumber($event)
            let value_package = this.$options.filters.decimalToNumber(this.form.receipt_items[index].value_package)
            let discount_item = this.form.receipt_items[index].discount_item
            this.form.receipt_items[index].sum_package = this.$options.filters.numberToDecimal(sum_package) + ' ' + this.form.receipt_items[index].package_name
            this.form.receipt_items[index].sum_item = this.$options.filters.numberToDecimal(sum_package * value_package) + ' ' + this.form.receipt_items[index].package_name
            let price_item = this.$options.filters.decimalToNumber(this.form.receipt_items[index].price_item)
            let sum_item = this.$options.filters.decimalToNumber(this.form.receipt_items[index].sum_item)
            this.form.receipt_items[index].price_item = this.$options.filters.numberToDecimalCurr(price_item)
            this.form.receipt_items[index].price_package = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            this.form.receipt_items[index].nt = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            if (discount_item > 0.00) {
                this.form.receipt_items[index].discount_price = this.$options.filters.numberToDecimalCurr(((price_item * sum_item)*discount_item)/100)
                this.form.receipt_items[index].nf = this.$options.filters.numberToDecimalCurr((price_item * sum_item)-((price_item * sum_item)*discount_item)/100)
            } else {
                this.form.receipt_items[index].nf = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            }
            this.sumAll()
        },
        piChanged($event, index){
            let price_item = this.$options.filters.decimalToNumber($event)
            let discount_item = this.form.receipt_items[index].discount_item
            let sum_item = this.$options.filters.decimalToNumber(this.form.receipt_items[index].sum_item)
            this.form.receipt_items[index].price_item = this.$options.filters.numberToDecimalCurr(price_item)
            this.form.receipt_items[index].price_package = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            this.form.receipt_items[index].nt = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            if (discount_item > 0.00) {
                this.form.receipt_items[index].discount_price = this.$options.filters.numberToDecimalCurr(((price_item * sum_item)*discount_item)/100)
                this.form.receipt_items[index].nf = this.$options.filters.numberToDecimalCurr((price_item * sum_item)-((price_item * sum_item)*discount_item)/100)
            } else {
                this.form.receipt_items[index].nf = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            }
            this.sumAll()
        },
        diChanged($event, index){
            // let discount_item = this.$options.filters.decimalToNumber($event)
            let discount_item = $event
            let price_item = this.$options.filters.decimalToNumber(this.form.receipt_items[index].price_item)
            let sum_item = this.$options.filters.decimalToNumber(this.form.receipt_items[index].sum_item)
            this.form.receipt_items[index].discount_item = discount_item
            this.form.receipt_items[index].price_package = this.$options.filters.numberToDecimalCurr(price_item * sum_item)
            this.form.receipt_items[index].discount_price = this.$options.filters.numberToDecimalCurr(((price_item * sum_item)*discount_item)/100)
            this.form.receipt_items[index].nf = this.$options.filters.numberToDecimalCurr((price_item * sum_item)-((price_item * sum_item)*discount_item)/100)
            this.sumAll()
        },
        sumAll() {
            let ntotal = 0
            let ntotalb = 0
            let ndiscount = 0
            let nntax = 0
            let nfinish = 0
            let nfinishitem = 0
            let delivery_fee = 0
            let ndiscountotal = 0
            let ndiscounttotal = 0
            this.form.receipt_items.forEach(element => {
                ntotal += this.$options.filters.decimalToNumber(element.price_package)
                ntotalb += this.$options.filters.decimalToNumber(element.price_package)
                ndiscount += this.$options.filters.decimalToNumber(element.discount_price)
                nfinishitem += this.$options.filters.decimalToNumber(element.nf)
            })
            if (this.form.delivery_fee) {
                delivery_fee = this.$options.filters.decimalToNumber(this.form.delivery_fee)
                nfinishitem = nfinishitem + delivery_fee
            }
            if (this.form.ndiscountotal) {
                ndiscountotal = this.$options.filters.decimalToNumber(this.form.ndiscountotal)
                ndiscounttotal = (ntotal * ndiscountotal) / 100
                ntotal = ntotal - ndiscounttotal
            }
            // if (this.form.ndiscountotal) {
            //     ndiscountotal = this.$options.filters.decimalToNumber(this.form.ndiscountotal)
            //     nfinishitem = nfinishitem - (nfinishitem * ndiscountotal) / 100
            // }
            if (this.is_ppn === true) {
                this.form.is_ppn = '1'
                nntax = nfinishitem*this.form.ntax/100
                nfinish = nfinishitem + (nfinishitem*this.form.ntax/100)
            } else {
                this.form.is_ppn = '0'
                nfinish = nfinishitem
                nntax = 0
            }
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
            this.form.no_document = ''
            this.form.no_invoice = ''
            this.form.date = ''
            this.form.flag = 0
            this.form.pharmacy_planning_id = ''
            this.form.no_planning_select = ''
            this.form.purchase_order_id = ''
            this.form.no_purchase_select = ''
            this.form.pharmacy_depo_id = '8da5d967-18da-4f87-86dc-0a9416611c11'
            this.form.supplier_id = ''
            this.form.payment_method_id = ''
            this.form.ntotal = ''
            this.form.ndiscountotal = ''
            this.form.ndiscounttotal = ''
            this.form.ntax = 11
            this.form.nntax = ''
            this.form.nround = ''
            this.form.nfinish = ''
            this.form.nfinishitem = ''
            this.form.receipt_items = []
            this.form.transaction_signature_val = []
        },
        editData (obj) {
            console.log(obj)
            this.active.main_tab = 'add'
            this.form.id = obj.id
            this.form.pharmacy_depo_id = '8da5d967-18da-4f87-86dc-0a9416611c11'
            this.form.po_type = obj.po_type
            this.form.no_document = obj.no_document
            if (obj.validation_key_1) {
                this.sign = true
                this.form.validation_key_1 = obj.validation_key_1 
            } else {
                this.sign = false
                this.form.validation_key_1 = '' 
            }
            this.form.no_invoice = obj.no_invoice
            this.form.pharmacy_planning_id = obj.pharmacy_planning_id
            this.form.purchase_order_id = obj.purchase_order_id
            if (obj.pharmacy_planning_id) {
                this.form.no_planning_select = {title:obj.no_document_planning,value:obj.pharmacy_planning_id}
            }
            if (obj.purchase_order_id) {
                this.form.no_purchase_select = {title:obj.no_document_purchase,value:obj.purchase_order_id}
            }
            this.form.date = obj.date
            this.form.payment_method_id = obj.payment_method_id
            this.form.supplier_id = obj.supplier_id
            this.form.flag = obj.flag
            this.form.ntotal = this.$options.filters.numberToDecimalCurr(obj.ntotal)
            this.form.nfinishitem = this.$options.filters.numberToDecimalCurr(obj.ntotal-obj.ndiscount)
            this.form.nfinish = this.$options.filters.numberToDecimalCurr(obj.nfinish)
            this.form.ndiscount = this.$options.filters.numberToDecimalCurr(obj.ndiscount)
            this.form.ntax = 11
            this.form.nntax = this.$options.filters.numberToDecimalCurr(obj.nfinish*this.form.ntax/100)
            this.form.ntotalb = this.$options.filters.numberToDecimalCurr(obj.ntotal)
            let pi = {}
            for (let i = 0; i < obj.receipt_items.length; i++) {
                pi = {
                    id:obj.receipt_items[i].id,
                    signature_validation_id:obj.receipt_items[i].pharmacy_purchase_order_id,
                    pharmacy_purchase_order_id:obj.receipt_items[i].pharmacy_purchase_order_id,
                    index:obj.receipt_items[i].sort,
                    code:obj.receipt_items[i].code,
                    name:obj.receipt_items[i].pharmacy_catalog_name,
                    expired_date:obj.receipt_items[i].expired_date,
                    batch:obj.receipt_items[i].batch,
                    pharmacy_catalog_id:obj.receipt_items[i].pharmacy_catalog_id, 
                    package_id:obj.receipt_items[i].package_id,
                    package_big_id:obj.receipt_items[i].package_big_id,
                    package_id_po:obj.receipt_items[i].package_id,
                    package_name_big:obj.receipt_items[i].package_name_big,
                    package_name:obj.receipt_items[i].package_name,
                    value_package_po:this.$options.filters.numberToDecimal(obj.receipt_items[i].value_package),
                    value_package:this.$options.filters.numberToDecimal(obj.receipt_items[i].value_package),
                    sum_package:this.$options.filters.numberToDecimal(obj.receipt_items[i].sum_package)+' '+obj.receipt_items[i].package_name, 
                    sum_item:this.$options.filters.numberToDecimal(obj.receipt_items[i].sum_item)+' '+obj.receipt_items[i].package_name, 
                    price_package:this.$options.filters.numberToDecimalCurr(obj.receipt_items[i].price_package), 
                    price_item_view:obj.receipt_items[i].price_item, 
                    price_item:this.$options.filters.numberToDecimalCurr(obj.receipt_items[i].price_item), 
                    discount_item:obj.receipt_items[i].discount_item, 
                    discount_price:this.$options.filters.numberToDecimalCurr(obj.receipt_items[i].discount_price), 
                    nt:this.$options.filters.numberToDecimalCurr(obj.receipt_items[i].price_item*obj.receipt_items[i].sum_item), 
                    nf:this.$options.filters.numberToDecimalCurr((obj.receipt_items[i].price_item*obj.receipt_items[i].sum_item)-((obj.receipt_items[i].price_item*obj.receipt_items[i].sum_item)*obj.receipt_items[i].discount_item/100)), 
                    sort:obj.receipt_items[i].sort, 
                    action: 'action'
                }
                this.form.receipt_items.push(pi)
            }
            this.form.transaction_signature_val = obj.transaction_signature_validation
            this.show = false
        },
        sendData (obj) {
            this.$store.dispatch('receipt/formPostPutAccurate', {id:obj.id})
            .then(res => {
                this.notification('Success','CheckIcon','success','You have successfully')
                this.$store.dispatch('receipt/getAll', this.params)
            }, 
            err => {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
            })
        },
    },
    mounted() {
        if (this.form.id == '') {
            this.$store.dispatch('signaturevalidation/getParamModule','a4abf06d-70ab-494c-b14a-9bffe4b59587').then(res => {
                let sg = {}
                for (let i = 0; i < res.signature_validation_items.length; i++) {
                    sg = {
                        signature_validation_id:res.signature_validation_items[i].signature_validation_id,
                        group_name:res.signature_validation_items[i].group_name,
                        group_id:res.signature_validation_items[i].group_id,
                        level_validation:res.signature_validation_items[i].level_validation,
                        status:res.signature_validation_items[i].status
                    }
                    this.form.transaction_signature_val.push(sg)
                }
            })
        }
    },
    created () {
        this.$store.dispatch('receipt/getAll',this.params)
        this.$store.dispatch('helper/getParam', 'type_purchase_order')
        this.$store.dispatch('helper/getParam', 'type_price')
        this.$store.dispatch('paymentmethod/getParam', 2)
        this.$store.dispatch('purchaseorder/getParam')
        this.$store.dispatch('planning/getParam')
        this.$store.dispatch('packages/getParam')
        this.$store.dispatch('catalog/getParam')
        this.$store.dispatch('company/getParamFlag', 3)
    }
};
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>