<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'FARMASI' },
                { text: 'APROVAL PO' },
                // { text: this.form_transaction.no_order }
            ]"
        >
        <!-- <Layout selected_menu="menu10"/> -->
        <TopPharmacyMenu selectedMenu="menu2" />
        <!-- <PharmacyReportMenu selectedMenu="menu1" class="mt-32" /> -->
        <section class="mt-24 d-flex">
            <b-button pill @click="toggleMainTab('list')"
                :variant="active.main_tab === 'list' ? 'primary-500' : 'outline-primary-500'" class="mr-2">List
            </b-button>
            <b-button pill @click="toggleMainTab('add')"
                :variant="active.main_tab === 'add' ? 'primary-500' : 'outline-primary-500'">Tambah Data</b-button>
        </section>
        <b-overlay
            :show="show"
            spinner-variant="primary"
            spinner-type="grow"
            spinner-small
            rounded="sm"
            >
            <div v-if="active.main_tab === 'list'">
                <section class="mt-32 mt-24">
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
                                        <div class="col-lg-6 mt-1">
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
                <section class="mt-32 mt-24">
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
                        <!-- <template #cell(status)="data">
                            <b-badge :variant="status[1][data.value]">
                            {{ status[0][data.value] }}
                            </b-badge>
                        </template> -->
                        <template #cell(validation_key_1)="row">
                            <template v-if="row.item.validation_key_1">
                                <b-badge variant="primary">Tervalidasi</b-badge>
                            </template>
                            <template v-else>
                                <b-badge variant="danger">PO BARU</b-badge>
                            </template>
                        </template>
                        <template #cell(action)="row">
                            <div class="row mx-0">
                                <template v-if="!row.item.validation_key_1">
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
                                    <b-button variant="primary-500" size="sm" class="mr-1" @click="editData(row.item)">
                                        <feather-icon icon="EditIcon"  />
                                    </b-button>
                                    <template v-if="row.item.po_type === 5  ">
                                        <router-link :to="{ path: '/pharmacy/purchase/print/prekusor/'+ row.item.id}" v-if="row.item.validation_key_1">
                                            <b-button size="sm" variant="info-700">
                                                <feather-icon icon="PrinterIcon" />
                                            </b-button>
                                        </router-link>
                                    </template>
                                    <template v-else-if="row.item.po_type === 4  ">
                                        <router-link :to="{ path: '/pharmacy/purchase/print/psikotropika/'+ row.item.id}" v-if="row.item.validation_key_1">
                                            <b-button size="sm" variant="info-700">
                                                <feather-icon icon="PrinterIcon" />
                                            </b-button>
                                        </router-link>
                                    </template>
                                    <template v-else-if="row.item.po_type === 3  ">
                                        <router-link :to="{ path: '/pharmacy/purchase/print/oot/'+ row.item.id}" v-if="row.item.validation_key_1">
                                            <b-button size="sm" variant="info-700">
                                                <feather-icon icon="PrinterIcon" />
                                            </b-button>
                                        </router-link>
                                    </template>
                                    <template v-else>
                                        <router-link :to="{ path: '/pharmacy/purchase/print/'+ row.item.id}" v-if="row.item.validation_key_1">
                                            <b-button size="sm" variant="info-700">
                                                <feather-icon icon="PrinterIcon" />
                                            </b-button>
                                        </router-link>
                                    </template>
                                    <b-button variant="danger-500" size="sm" class="ml-1" @click="rowOnCancel(row.item.id)">
                                        Batalkan PO
                                        <b-link v-b-modal.modal-center>
                                        </b-link>
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
                <section class="card mt-32">
                    <div class="card-header bg-primary-200 font-weight-bold">
                        <div class="d-flex justify-content-lg-between">
                            <span class="font-weight-bold">DATA PO</span>
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
                                    <!-- <b-button variant="primary-600" size="sm" @click.prevent="validationForm" class="mr-1">
                                        <feather-icon icon="SaveIcon" /> Simpan
                                    </b-button> -->
                                <!-- </template> -->
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
                                                <label>Jenis PO</label>
                                            </div>
                                            <div class="col-lg">
                                                <v-select v-model="form.po_type" :options="PurchaseOrderOptions" :reduce="PurchaseOrderOptions => PurchaseOrderOptions.value" label="title" placeholder="-- Pilih Jenis PO --"></v-select>
                                            </div>
                                        </div>
                                        <template v-if="form.po_type === 1">
                                            <div class="row mt-1">
                                                <div class="col-lg-3 text-lg-right">
                                                    <label>Reff Perencanaan</label>
                                                </div>
                                                <div class="col-lg">
                                                    <v-select v-model="form.no_planning_select" :options="PlanningOptions" :reduce="PlanningOptions => PlanningOptions" label="title" placeholder="-- Pilih Referensi Perencanaan --"></v-select>
                                                </div>
                                            </div>
                                        </template>
                                        <div class="row mt-1">
                                            <div class="col-lg-3 text-lg-right">
                                                <label>Supplier/Pemasok</label>
                                            </div>
                                            <div class="col-lg">
                                                <v-select v-model="form.supplier_id" @input="onAddPlanning($event)" :options="SupplierOptions" :reduce="SupplierOptions => SupplierOptions.value" label="title" placeholder="-- Pilih Supplier --"></v-select>
                                            </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-lg-3 text-lg-right">
                                                <label>Tanggal</label>
                                            </div>
                                            <div class="col-lg">
                                                <b-form-input type="date" v-model="form.date" />
                                            </div>
                                        </div>
                                        <!-- <div class="row mt-1">
                                            <div class="col-lg-3 text-lg-right">
                                                <label>Jatuh Tempo</label>
                                            </div>
                                            <div class="col-lg">
                                                <b-form-input type="number" v-model="form.due_day" />
                                            </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-lg-3 text-lg-right">
                                                <label class="m-0">Tgl PO & Jatuh Tempo</label>
                                            </div>
                                            <div class="col-lg">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <b-input-group>
                                                            <b-form-input type="date" v-model="form.due_date_start" autocomplete="off"></b-form-input>
                                                        </b-input-group>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <b-input-group>
                                                            <b-form-input type="date" v-model="form.due_date_end" autocomplete="off"></b-form-input>
                                                        </b-input-group>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-lg-3 text-lg-right">
                                                <label class="m-0">No Rekening</label>
                                            </div>
                                            <div class="col-lg">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <b-form-input type="text" v-model="form.account_number"></b-form-input>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <b-input-group prepend="A/N">
                                                            <b-form-input type="text" v-model="form.account_name" />
                                                        </b-input-group>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
                                        <div class="row mt-1">
                                            <div class="col-lg-3 text-lg-right">
                                                <label>Jenis Harga</label>
                                            </div>
                                            <div class="col-lg">
                                                <v-select v-model="form.price_type" :options="PriceTypeOptions" :reduce="PriceTypeOptions => PriceTypeOptions.value" label="title" placeholder="-- Pilih Jenis Harga --"></v-select>
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
                                        <template v-if="form.payment_method_id === 1">
                                            <div class="row mt-1">
                                                <div class="col-lg-3 text-lg-right">
                                                    <label>Waktu Kredit</label>
                                                </div>
                                                <div class="col-lg">
                                                    <b-form-input type="number" v-model="form.time_period" />
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <div class="col-lg-3 text-lg-right">
                                                <label>Gudang</label>
                                            </div>
                                            <div class="col-lg">
                                                <b-input-group>
                                                    <b-input-group-prepend is-text checked>
                                                        <b-form-radio type="radio" v-model="form.pharmacy_depo_id" value="8da5d967-18da-4f87-86dc-0a9416611c11" :checked="true"/>
                                                    </b-input-group-prepend>
                                                    <b-form-input value="GUDANG INSTALASI FARMASI" disabled></b-form-input>
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
                                                <!-- <b-input-group prepend="Diskon Item">
                                                    <b-form-input readonly v-model="form.ndiscount" class="text-right" />
                                                </b-input-group> -->
                                                <!-- <b-input-group prepend="Ongkir">
                                                    <b-form-input @change="vpChangedDeliveryFee($event)" v-model="form.delivery_fee" class="text-right" />
                                                </b-input-group> -->
                                                <b-input-group prepend="Diskon %">
                                                    <b-form-checkbox @change="changePercent" v-model="percent" />
                                                    <b-form-input type="number" :disabled="!is_percent" @change="vpChangedDiscTotal($event)" v-model="form.ndiscountotal" class="text-right" />
                                                </b-input-group>
                                                <b-input-group prepend="Diskon IDR">
                                                    <b-form-input :disabled="is_percent" @change="vpChangedDiscTotal($event)" v-model="form.ndiscounttotal" class="text-right" />
                                                </b-input-group>
                                                <b-input-group prepend="Nilai Total">
                                                    <b-form-input readonly v-model="form.nfinishitem" class="text-right" />
                                                </b-input-group>
                                                <b-input-group prepend="PPN">
                                                    <b-input-group-prepend is-text checked>
                                                        <b-form-checkbox type="checkbox" @change="vpChangedPPN($event)" v-model="is_ppn"/>
                                                    </b-input-group-prepend>
                                                    <b-form-input readonly v-model="form.ntax" class="text-right" />
                                                </b-input-group>
                                                <b-input-group prepend="Nilai Total + PPN">
                                                    <b-form-input readonly v-model="form.nntax" class="text-right" />
                                                </b-input-group>
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
                        <template v-if="form.po_type !== 1">
                            <table class="table">
                                <tr>
                                    <td>
                                        <v-select v-model="catalog" @input="onAddSC($event)" :options="CatalogOptions" :reduce="CatalogOptions => CatalogOptions" label="title" placeholder="-- Pilih Katalog --"></v-select>
                                    </td>
                                </tr>
                            </table>
                        </template>
                        <table class="table table-responsive table-bordered" responsive small bordered>
                            <thead class="text-center text-bold-14">
                                <tr>
                                    <th rowspan="2">
                                        <!-- <b-form-checkbox /> -->
                                    </th>
                                    <th rowspan="2">NO</th>
                                    <th rowspan="2">KODE</th>
                                    <th rowspan="2" style="min-width: 200px;">NAMA BARANG</th>
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
                            <tr class="item" v-for="(item, index) in form.purchase_order_items">
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
                                    <v-select v-model="item.package_big_id" :options="PackageOptions" :reduce="PackageOptions => PackageOptions.value" label="title" placeholder="Kemasan"></v-select>
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
                                <td colspan="11">&nbsp;</td>
                                <td><b-form-input type="text" v-model="form.ntotalb" readonly/></td>
                                <td><b-form-input type="text" v-model="form.ndiscount" readonly/></td>
                                <td><b-form-input type="text" v-model="form.nfinishitem" readonly/></td>
                            </tr>
                            <tr>
                                <td colspan="15">&nbsp;</td>
                            </tr>
                        </table>
                    </div>
                </section>
            </div>
        </b-overlay>
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
            openModal: false,
            active: {
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
                po_type:'',
                is_ppn:'1',
                no_document:'',
                no_planning:'',
                no_planning_select:'',
                due_day:'',
                pharmacy_depo_id:'8da5d967-18da-4f87-86dc-0a9416611c11',
                type_doc_id:1,
                payment_method_id:'',
                supplier_id:'',
                due_date_start:'',
                due_date_end:'',
                account_number:'',
                account_name:'',
                price_type:'',
                time_period:'',
                ntotalb:'',
                ntotal:'',
                ndiscountotal:0,
                delivery_fee:0,
                ndiscount:'',
                ntax:11,
                nntax:'',
                nround:'',
                ntotalb:'',
                nfinish:'',
                nfinishitem:'',
                validation_key_1:'',
                purchase_order_items:[],
                transaction_signature_val:[]
            },
            sign:false,
            validation_key_1:'eba5ed94-e863-43c7-bb57-197c33678b4d',
            validation_key_1_show:true,
            fill_ttd:false,
            is_ppn:true,
            dataPlanning:[],
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
                this.$store.dispatch('purchaseorder/getAll',this.params)
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.$store.dispatch('purchaseorder/getAll',this.params)
            }
        }
    },
    computed: {
        ...mapState({ 
            totalRows: state => state.purchaseorder.totalRows, 
            fields: state => state.purchaseorder.fields, 
            items: state => state.purchaseorder.items, 
            pageOptions: state => state.purchaseorder.pageOptions, 
            CatalogOptions: state => state.catalog.options.catalog, 
            PackageOptions: state => state.packages.options.packages,
            SignatureItems: state => state.signaturevalidation.items,
            PlanningOptions: state => state.planning.options.planning,
            SupplierOptions: state => state.company.options.supplier, 
            PurchaseOrderOptions: state => state.helper.options.typepurchaseorder,
            PriceTypeOptions: state => state.helper.options.typeprice,
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
            this.sign = true
        },
        vpChangedDeliveryFee (event) {
            this.sumAll()
        },
        vpChangedDiscTotal (event) {
            this.sumAll()
        },
        vpChangedPPN (event) {
            this.form.is_ppn = '1'
            this.sumAll()
        },
        deleteSC (index) {
            this.form.purchase_order_items.splice(index, 1)
            this.sumAll()
        },
        onAddSC (event) {
            this.catalog = ''
            this.collation = ''
            this.form.purchase_order_items.push(
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
                    value_package:'0',
                    sum_package:event.data_catalog.value_package !== 0 ? event.data_catalog.value_package+' '+event.data_catalog.package_name : 1+' '+event.data_catalog.package_name, 
                    sum_item:'0 '+event.data_catalog.package_name, 
                    price_package:this.$options.filters.numberToDecimalCurr(event.data_catalog.price), 
                    price_item:this.$options.filters.numberToDecimalCurr(event.data_catalog.price), 
                    discount_item:'0', 
                    discount_price:'0', 
                    nt:'0', 
                    nf:'0', 
                    sort:this.form.purchase_order_items.length, 
                    data_catalog:event,
                    action: 'action'
                }
            )
        },
        onAddSupplier (events) {
            const event = this.dataPlanning
            console.log(events)
            console.log(event)
            this.form.account_number = ''
            this.form.account_name = ''
            this.form.account_number = '123'
            this.form.account_name = 'Test'
            this.form.ntotal = ''
            this.form.ndiscount = ''
            this.form.nmargins = ''
            this.form.nfinish = ''
            this.form.nfinishitem = ''
            this.form.purchase_order_items = []
            let pi = {}
            this.form.no_planning = event.planning.id
            this.form.no_planning_select = {
                title:event.planning.no_document,
                value:event.planning.id
            }
            this.form.ntotalb = this.$options.filters.numberToDecimalCurr(event.planning.ntotal)
            this.form.ntotal = this.$options.filters.numberToDecimalCurr(event.planning.ntotal)
            this.form.ndiscount = this.$options.filters.numberToDecimalCurr(event.planning.ndiscount)
            this.form.ntax = 11
            this.form.nntax = this.$options.filters.numberToDecimalCurr(event.planning.nfinish*this.form.ntax/100)
            this.form.nfinish = this.$options.filters.numberToDecimalCurr(event.planning.nfinish+(event.planning.nfinish*this.form.ntax/100))
            this.form.nfinishitem = this.$options.filters.numberToDecimalCurr(event.planning.nfinish)
            for (let i = 0; i < event.planning.planning_items.length; i++) {
                if (events == event.planning.planning_items[i].supplier_id) {
                    pi = {
                        signature_validation_id:event.planning.planning_items[i].id,
                        pharmacy_purchase_order_id:event.planning.planning_items[i].pharmacy_purchase_order_id,
                        index:event.planning.planning_items[i].sort,
                        code:event.planning.planning_items[i].code,
                        name:event.planning.planning_items[i].pharmacy_catalog_name,
                        pharmacy_catalog_id:event.planning.planning_items[i].pharmacy_catalog_id, 
                        package_id:event.planning.planning_items[i].package_id,
                        package_name_big:event.planning.planning_items[i].package_name_big,
                        package_name:event.planning.planning_items[i].package_name,
                        value_package:this.$options.filters.numberToDecimal(event.planning.planning_items[i].value_package),
                        sum_package:this.$options.filters.numberToDecimal(event.planning.planning_items[i].sum_package)+' '+event.planning.planning_items[i].package_name, 
                        sum_item:this.$options.filters.numberToDecimal(event.planning.planning_items[i].sum_item)+' '+event.planning.planning_items[i].package_name, 
                        price_package:this.$options.filters.numberToDecimalCurr(event.planning.planning_items[i].price_package), 
                        price_item:this.$options.filters.numberToDecimalCurr(event.planning.planning_items[i].price_item), 
                        discount_item:this.$options.filters.numberToDecimal(event.planning.planning_items[i].discount_item), 
                        discount_price:this.$options.filters.numberToDecimalCurr(event.planning.planning_items[i].discount_price), 
                        nt:this.$options.filters.numberToDecimalCurr(event.planning.planning_items[i].price_item*event.planning.planning_items[i].sum_item), 
                        nf:this.$options.filters.numberToDecimalCurr((event.planning.planning_items[i].price_item*event.planning.planning_items[i].sum_item)-((event.planning.planning_items[i].price_item*event.planning.planning_items[i].sum_item)*event.planning.planning_items[i].discount_item/100)), 
                        sort:event.planning.planning_items[i].sort, 
                        action: 'action'
                    }
                    this.form.purchase_order_items.push(pi)
                }
            }
        },
        onAddPlanning (event) {
            // this.dataPlanning = event
            // this.form.ntotal = ''
            // this.form.ndiscount = ''
            // this.form.nmargins = ''
            // this.form.nfinish = ''
            // this.form.nfinishitem = ''
            // this.form.purchase_order_items = []
            // let pi = {}
            // this.form.no_planning = this.form.no_planning_select
            // // this.form.no_planning_select = {
            // //     title:this.form.no_planning_select.no_document,
            // //     value:this.form.no_planning_select.id
            // // }
            // this.form.ntotalb = this.$options.filters.numberToDecimalCurr(this.form.no_planning_select.ntotal)
            // this.form.ntotal = this.$options.filters.numberToDecimalCurr(this.form.no_planning_select.ntotal)
            // this.form.ndiscount = this.$options.filters.numberToDecimalCurr(this.form.no_planning_select.ndiscount)
            // this.form.ntax = 0
            // this.is_ppn = false
            // this.form.nntax = this.$options.filters.numberToDecimalCurr(this.form.no_planning_select.nfinish*this.form.ntax/100)
            // this.form.nfinish = this.$options.filters.numberToDecimalCurr(this.form.no_planning_select.nfinish+(this.form.no_planning_select.nfinish*this.form.ntax/100))
            // this.form.nfinishitem = this.$options.filters.numberToDecimalCurr(this.form.no_planning_select.nfinish)
            // this.$store.dispatch('planning/getPlanningSupplier', {planning_id:this.form.no_planning_select.value,supplier_id:this.form.supplier_id})
            // .then(res => {
            //     for (let i = 0; i < res.length; i++) {
            //         pi = {
            //             signature_validation_id:res[i].id,
            //             pharmacy_purchase_order_id:res[i].pharmacy_purchase_order_id,
            //             index:res[i].sort,
            //             code:res[i].code,
            //             name:res[i].pharmacy_catalog_name,
            //             pharmacy_catalog_id:res[i].pharmacy_catalog_id, 
            //             package_id:res[i].package_id,
            //             package_big_id:res[i].package_big_id,
            //             package_name_big:res[i].package_name_big,
            //             package_name:res[i].package_name,
            //             value_package:this.$options.filters.numberToDecimal(res[i].value_package),
            //             sum_package:this.$options.filters.numberToDecimal(res[i].sum_package)+' '+res[i].package_name, 
            //             sum_item:this.$options.filters.numberToDecimal(res[i].sum_item)+' '+res[i].package_name, 
            //             price_package:this.$options.filters.numberToDecimalCurr(res[i].price_package), 
            //             price_item:this.$options.filters.numberToDecimalCurr(res[i].price_item), 
            //             discount_item:res[i].discount_item, 
            //             discount_price:this.$options.filters.numberToDecimalCurr(res[i].discount_price), 
            //             nt:this.$options.filters.numberToDecimalCurr(res[i].price_item*res[i].sum_item), 
            //             nf:this.$options.filters.numberToDecimalCurr((res[i].price_item*res[i].sum_item)-((res[i].price_item*res[i].sum_item)*res[i].discount_item/100)), 
            //             sort:res[i].sort, 
            //             action: 'action'
            //         }
            //         this.sumAll()
            //         this.form.purchase_order_items.push(pi)
            //     }
            // })
        },
        validationForm () {
            this.show = true
            this.sumAll()
            const formData = this.form 
            this.$store.dispatch('purchaseorder/formPostPut', formData)
            .then(res => {
                this.notification('Success','CheckIcon','success','You have successfully')
                this.$store.dispatch('purchaseorder/getAll', this.params)
                this.active.main_tab = 'list'
                this.form.id = ''
                this.form.po_type = ''
                this.form.no_document = ''
                this.form.no_planning = ''
                this.form.no_planning_select = ''
                this.form.due_day = ''
                this.form.pharmacy_depo_id = '8da5d967-18da-4f87-86dc-0a9416611c11'
                this.form.type_doc_id = 1
                this.form.payment_method_id = ''
                this.form.supplier_id = ''
                this.form.due_date_start = ''
                this.form.due_date_end = ''
                this.form.account_number = ''
                this.form.account_name = ''
                this.form.price_type = ''
                this.form.time_period = ''
                this.form.ntotal = ''
                this.form.ndiscount = ''
                this.form.ntax = 11
                this.form.nntax = ''
                this.form.nround = ''
                this.form.nfinish = ''
                this.form.nfinishitem = ''
                this.form.purchase_order_items = []
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
            this.$store.dispatch('purchaseorder/getAll',this.params)
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
            this.form.po_type = ''
            this.form.no_document = ''
            this.form.no_planning = ''
            this.form.no_planning_select = ''
            this.form.due_day = ''
            this.form.pharmacy_depo_id = '8da5d967-18da-4f87-86dc-0a9416611c11'
            this.form.type_doc_id = 1
            this.form.payment_method_id = ''
            this.form.supplier_id = ''
            this.form.due_date_start = ''
            this.form.due_date_end = ''
            this.form.account_number = ''
            this.form.account_name = ''
            this.form.price_type = ''
            this.form.time_period = ''
            this.form.ntotal = ''
            this.form.ndiscount = ''
            this.form.ntax = 11
            this.form.nntax = ''
            this.form.nround = ''
            this.form.nfinish = ''
            this.form.nfinishitem = ''
            this.form.purchase_order_items = []
            this.form.transaction_signature_val = []
            this.params.offset = 0
            this.params.limit = this.perPage
            this.$store.dispatch('catalog/getAll',this.params)
        },
        rowOnDelete (id) {
            this.$bvModal
                .msgBoxConfirm('Please confirm that you want to delete everything.', {
                title: 'Please Confirm', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('purchaseorder/remove', id)
                    .then(response => {
                        if (response.status === 200) {
                            this.$store.dispatch('purchaseorder/getAll', this.params)
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
        rowOnDelete (id) {
            this.$bvModal
                .msgBoxConfirm('Please confirm that you want to delete everything.', {
                title: 'Please Confirm', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('purchaseorder/remove', id)
                    .then(response => {
                        if (response.status === 200) {
                            this.$store.dispatch('purchaseorder/getAll', this.params)
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
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        vpChanged($event, index){
            let value_package = $event
            let sum_package = this.$options.filters.decimalToNumber(this.form.purchase_order_items[index].sum_package)
            this.form.purchase_order_items[index].value_package = this.$options.filters.numberToDecimal(value_package)
            this.form.purchase_order_items[index].sum_item = this.$options.filters.numberToDecimal(value_package * sum_package) + ' ' + this.form.purchase_order_items[index].package_name
            let price_item = this.$options.filters.decimalToNumber(this.form.purchase_order_items[index].price_item)
            let sum_item = this.$options.filters.decimalToNumber(this.form.purchase_order_items[index].sum_item)
            let discount_item = this.form.purchase_order_items[index].discount_item
            this.form.purchase_order_items[index].price_item = this.$options.filters.numberToDecimalCurr(price_item)
            console.log(price_item)
            console.log(sum_item)
            let total = Math.floor(price_item * sum_item)
            this.form.purchase_order_items[index].nt = this.$options.filters.numberToDecimalCurr(total)
            this.form.purchase_order_items[index].price_package = this.$options.filters.numberToDecimalCurr(total)
            console.log('price_package'+ this.form.purchase_order_items[index].price_package)
            if (discount_item > 0.00) {
                this.form.purchase_order_items[index].discount_price = this.$options.filters.numberToDecimalCurr(((total)*discount_item)/100)
                this.form.purchase_order_items[index].nf = this.$options.filters.numberToDecimalCurr((total)-((total)*discount_item)/100)
            } else {
                this.form.purchase_order_items[index].nf = this.$options.filters.numberToDecimalCurr(total)
                console.log('nf'+ this.form.purchase_order_items[index].nf)
            }
            this.sumAll()
        },
        spChanged($event, index){
            let sum_package = this.$options.filters.decimalToNumber($event)
            let value_package = this.$options.filters.decimalToNumber(this.form.purchase_order_items[index].value_package)
            let discount_item = this.$options.filters.decimalToNumber(this.form.purchase_order_items[index].discount_item)
            this.form.purchase_order_items[index].sum_package = this.$options.filters.numberToDecimal(sum_package) + ' ' + this.form.purchase_order_items[index].package_name
            this.form.purchase_order_items[index].sum_item = this.$options.filters.numberToDecimal(sum_package * value_package) + ' ' + this.form.purchase_order_items[index].package_name
            let price_item = this.$options.filters.decimalToNumber(this.form.purchase_order_items[index].price_item)
            let sum_item = this.$options.filters.decimalToNumber(this.form.purchase_order_items[index].sum_item)
            this.form.purchase_order_items[index].price_item = this.$options.filters.numberToDecimalCurr(price_item)
            let total = Math.floor(price_item * sum_item)
            this.form.purchase_order_items[index].price_package = this.$options.filters.numberToDecimalCurr(total)
            this.form.purchase_order_items[index].nt = this.$options.filters.numberToDecimalCurr(total)
            if (discount_item > 0.00) {
                this.form.purchase_order_items[index].discount_price = this.$options.filters.numberToDecimalCurr(((total)*discount_item)/100)
                this.form.purchase_order_items[index].nf = this.$options.filters.numberToDecimalCurr((total)-((total)*discount_item)/100)
            } else {
                this.form.purchase_order_items[index].nf = this.$options.filters.numberToDecimalCurr(total)
            }
            this.sumAll()
        },
        piChanged($event, index){
            let price_item = $event
            let discount_item = this.form.purchase_order_items[index].discount_item
            let sum_item = this.$options.filters.decimalToNumber(this.form.purchase_order_items[index].sum_item)
            this.form.purchase_order_items[index].price_item = this.$options.filters.numberToDecimalCurr(price_item)
            let total = Math.floor(price_item * sum_item)
            console.log(total)
            this.form.purchase_order_items[index].price_package = this.$options.filters.numberToDecimalCurr(total)
            this.form.purchase_order_items[index].nt = this.$options.filters.numberToDecimalCurr(total)
            if (discount_item > 0.00) {
                this.form.purchase_order_items[index].discount_price = this.$options.filters.numberToDecimalCurr(((total)*discount_item)/100)
                this.form.purchase_order_items[index].nf = this.$options.filters.numberToDecimalCurr((total)-((total)*discount_item)/100)
            } else {
                this.form.purchase_order_items[index].nf = this.$options.filters.numberToDecimalCurr(total)
            }
            this.sumAll()
        },
        diChanged($event, index){
            // let discount_item = this.$options.filters.decimalToNumber($event)
            let discount_item = $event
            let price_item = this.$options.filters.decimalToNumber(this.form.purchase_order_items[index].price_item)
            let sum_item = this.$options.filters.decimalToNumber(this.form.purchase_order_items[index].sum_item)
            let total = Math.floor(price_item * sum_item)
            this.form.purchase_order_items[index].discount_item = discount_item
            this.form.purchase_order_items[index].price_package = this.$options.filters.numberToDecimalCurr(total)
            this.form.purchase_order_items[index].discount_price = this.$options.filters.numberToDecimalCurr(((total)*discount_item)/100)
            this.form.purchase_order_items[index].nf = this.$options.filters.numberToDecimalCurr((total)-((total)*discount_item)/100)
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
            this.form.purchase_order_items.forEach(element => {
                ntotal += this.$options.filters.decimalToNumber(element.price_package)
                ntotalb += this.$options.filters.decimalToNumber(element.price_package)
                ndiscount += this.$options.filters.decimalToNumber(element.discount_price)
                nfinishitem += this.$options.filters.decimalToNumber(element.nf)
            })
            if (this.form.delivery_fee) {
                delivery_fee = this.$options.filters.decimalToNumber(this.form.delivery_fee)
                nfinishitem = nfinishitem + delivery_fee
            }
            console.log(ntotalb)
            console.log(nfinishitem)
            if (this.form.ndiscountotal) {
                ndiscountotal = this.$options.filters.decimalToNumber(this.form.ndiscountotal)
                ndiscounttotal = Math.floor((ntotal * ndiscountotal) / 100)
                ntotal = Math.floor(ntotal - ndiscounttotal)
            }
            // if (this.form.ndiscounttotal) {
            //     ndiscounttotal = this.$options.filters.decimalToNumber(this.form.ndiscounttotal)
            //     nfinishitem = ntotal - ndiscounttotal
            // }
            if (this.is_ppn === true) {
                this.form.is_ppn = '1'
                nntax = Math.floor(nfinishitem*this.form.ntax/100)
                nfinish = Math.floor(nfinishitem + (nfinishitem*this.form.ntax/100))
            } else {
                this.form.is_ppn = '0'
                nfinish = Math.floor(nfinishitem)
                nntax = 0
            }
            this.form.delivery_fee = this.$options.filters.numberToDecimalCurr(delivery_fee)
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
            this.form.po_type = ''
            this.form.no_document = ''
            this.form.no_planning = ''
            this.form.no_planning_select = ''
            this.form.due_day = ''
            this.form.pharmacy_depo_id = '8da5d967-18da-4f87-86dc-0a9416611c11'
            this.form.type_doc_id = 1
            this.form.payment_method_id = ''
            this.form.supplier_id = ''
            this.form.due_date_start = ''
            this.form.due_date_end = ''
            this.form.date = ''
            this.form.account_number = ''
            this.form.account_name = ''
            this.form.price_type = ''
            this.form.time_period = ''
            this.form.ntotal = ''
            this.form.ndiscount = ''
            this.form.is_percent = false
            this.form.percent = false
            this.form.ndiscountotal = ''
            this.form.ndiscounttotal = ''
            this.form.ntax = 11
            this.form.nntax = ''
            this.form.nround = ''
            this.form.nfinish = ''
            this.form.nfinishitem = ''
            this.form.validation_key_1 = ''
            this.form.purchase_order_items = []
            this.form.transaction_signature_val = []
        },
        editData (obj) {
            console.log(obj)
            this.active.main_tab = 'add'
            this.form.id = obj.id
            this.form.pharmacy_depo_id = '8da5d967-18da-4f87-86dc-0a9416611c11'
            this.form.po_type = obj.po_type
            this.form.no_document = obj.no_document
            this.form.no_planning = obj.no_planning
            if (obj.no_planning) {
                this.form.no_planning_select = {title:obj.no_document_planning,value:obj.no_planning}
            }
            // if (obj.validation_key_1) {
            //     this.fill_ttd = true
            //     this.sign = true
            //     this.validation_key_1_show = true
            //     // this.$refs.signaturePad.fromDataURL(`http://localhost/be-ci/${obj.validation_key_1}`);
            //     this.form.validation_key_1 = `http://192.168.100.3/${obj.validation_key_1}`
            // } else {
            //     this.fill_ttd = false
            //     this.sign = false
            //     this.validation_key_1_show = true
            // }
            if (obj.validation_key_1) {
                this.sign = true
                this.form.validation_key_1 = obj.validation_key_1 
            } else {
                this.sign = false
                this.form.validation_key_1 = '' 
            }
            if (obj.ndiscountotal) {
                this.percent = true
                this.is_percent = true
            }
            this.form.due_day = obj.due_day
            this.form.type_doc_id = obj.type_doc_id
            this.form.payment_method_id = obj.payment_method_id
            this.form.supplier_id = obj.supplier_id
            this.form.due_date_start = obj.due_date_start
            this.form.due_date_end = obj.due_date_end
            this.form.date = obj.date
            this.form.account_number = obj.account_number
            this.form.account_name = obj.account_name
            this.form.price_type = obj.price_type
            this.form.time_period = obj.time_period
            this.form.delivery_fee = this.$options.filters.numberToDecimalCurr(obj.delivery_fee)
            this.form.ndiscountotal = this.$options.filters.numberToDecimal(obj.ndiscountotal)
            this.form.ndiscounttotal = this.$options.filters.numberToDecimal(obj.ndiscounttotal)
            this.form.ntotal = this.$options.filters.numberToDecimalCurr(obj.ntotal)
            this.form.nfinishitem = this.$options.filters.numberToDecimalCurr(obj.ntotal-obj.ndiscount-obj.ndiscountotal)
            this.form.nfinish = this.$options.filters.numberToDecimalCurr(obj.nfinish)
            this.form.ndiscount = this.$options.filters.numberToDecimalCurr(obj.ndiscount)
            
            if (obj.is_ppn === 1) {
                this.is_ppn = true
                this.form.ntax = 11
                this.form.nntax = this.$options.filters.numberToDecimalCurr(obj.nfinish*this.form.ntax/100)
            } else {
                this.form.ntax = 11
                this.form.nntax = this.$options.filters.numberToDecimalCurr(0)
                this.is_ppn = false
            }
            this.form.ntotalb = this.$options.filters.numberToDecimalCurr(obj.ntotal)
            let pi = {}
            for (let i = 0; i < obj.purchase_order_items.length; i++) {
                pi = {
                    id:obj.purchase_order_items[i].id,
                    pharmacy_purchase_order_id:obj.purchase_order_items[i].pharmacy_purchase_order_id,
                    index:obj.purchase_order_items[i].sort,
                    code:obj.purchase_order_items[i].code,
                    name:obj.purchase_order_items[i].pharmacy_catalog_name,
                    pharmacy_catalog_id:obj.purchase_order_items[i].pharmacy_catalog_id, 
                    package_id:obj.purchase_order_items[i].package_id,
                    package_big_id:obj.purchase_order_items[i].package_big_id,
                    package_name_big:obj.purchase_order_items[i].package_name_big,
                    package_name:obj.purchase_order_items[i].package_name,
                    value_package:this.$options.filters.numberToDecimal(obj.purchase_order_items[i].value_package),
                    sum_package:this.$options.filters.numberToDecimal(obj.purchase_order_items[i].sum_package)+' '+obj.purchase_order_items[i].package_name, 
                    sum_item:this.$options.filters.numberToDecimal(obj.purchase_order_items[i].sum_item)+' '+obj.purchase_order_items[i].package_name, 
                    price_package:this.$options.filters.numberToDecimalCurr(obj.purchase_order_items[i].price_package), 
                    price_item:this.$options.filters.numberToDecimalCurr(obj.purchase_order_items[i].price_item), 
                    discount_item:obj.purchase_order_items[i].discount_item, 
                    discount_price:this.$options.filters.numberToDecimalCurr(obj.purchase_order_items[i].discount_price), 
                    nt:this.$options.filters.numberToDecimalCurr(Math.floor(obj.purchase_order_items[i].price_item*obj.purchase_order_items[i].sum_item)), 
                    nf:this.$options.filters.numberToDecimalCurr((Math.floor(obj.purchase_order_items[i].price_item*obj.purchase_order_items[i].sum_item))-((Math.floor(obj.purchase_order_items[i].price_item*obj.purchase_order_items[i].sum_item))*obj.purchase_order_items[i].discount_item/100)), 
                    sort:obj.purchase_order_items[i].sort, 
                    action: 'action'
                }
                this.form.purchase_order_items.push(pi)
            }
            this.form.transaction_signature_val = obj.transaction_signature_validation
            this.show = false
        },
    },
    mounted() {
        // if (this.form.id == '') {
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
        this.$store.dispatch('purchaseorder/getAll',this.params)
        this.$store.dispatch('helper/getParam', 'type_purchase_order')
        this.$store.dispatch('helper/getParam', 'type_price')
        this.$store.dispatch('paymentmethod/getParam', 2)
        this.$store.dispatch('planning/getRef')
        this.$store.dispatch('packages/getParam')
        this.$store.dispatch('catalog/getParam')
        this.$store.dispatch('company/getParamFlag', 3)
    }
};
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>