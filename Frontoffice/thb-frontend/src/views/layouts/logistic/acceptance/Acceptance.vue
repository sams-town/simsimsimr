<template>
    <div>
        <TopLogisticMenu selectedMenu="menu4" />
        <div class="col mt-32">
            <section class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="label-head">
                    <span class="px-16 text-bold-24 text-neutral-700">Penerimaan</span>
                </div>
                <div class="d-flex flex-wrap">
                    <b-button @click="active.ModalOpen = !active.ModalOpen" pill variant="primary" class="px-3">
                        <feather-icon icon="PlusIcon" class="mr-8" /> Data Perencanaan
                    </b-button>
                </div>
            </section>
            <section class="mt-24">
                <section class="card">
                    <div class="card-header font-weight-bold bg-primary-200">Filter Data</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <div class="col-lg-10">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <label class="c-form-label">No Transaksi</label>
                                        <b-form-input />
                                    </div>
                                    <div class="col-lg-4 mt-12 mt-lg-0">
                                        <label class="c-form-label">No Perencanaan</label>
                                        <b-form-input />
                                    </div>
                                    <div class="col-lg-4 mt-12 mt-lg-0">
                                        <label class="c-form-label">Status Permintaan</label>
                                        <b-form-select />
                                    </div>
                                    <div class="col-lg-4 mt-12">
                                        <label class="c-form-label">Tanggal Perencanaan</label>
                                        <b-form-datepicker />
                                    </div>
                                    <div class="col-lg-4 mt-12">
                                        <label class="c-form-label">Sampai Dengan</label>
                                        <b-form-datepicker />
                                    </div>
                                </div>
                                <div class="d-flex mt-24">
                                    <b-button pill variant="outline-primary" class="px-3 mr-2 ml-auto">
                                        <feather-icon icon="XIcon" class="mr-8" /> Clear Filter
                                    </b-button>
                                    <b-button pill variant="primary" class="px-3">
                                        <feather-icon icon="FileTextIcon" class="mr-8" /> Export Excel
                                    </b-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- table here -->
                <div class="mt-32 mt-lg-40">
                    <p class="text-bold-24">List Data</p>
                    <div class="row mx-0 mt-8">
                        <span class="text-regular-14">Tampilkan</span>
                        <select id="per-page-select" v-model="AcceptanceTable.perPage"
                            class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                            <option value="5" selected>5</option>
                            <option value="10" selected>10</option>
                            <option value="15" selected>15</option>
                        </select>
                        <span class="text-regular-14">Entri</span>
                    </div>
                    <b-table bordered responsive hover small head-variant="light" :fields="AcceptanceTable.field"
                        :items="AcceptanceTable.item" :current-page="AcceptanceTable.currentPage" @filtered="patientFiltered"
                        :per-page="AcceptanceTable.perPage" thead-class="text-bold-14" tbody-tr-class="text-regular-14"
                        class="mt-12 text-center">
                        <template #cell(validation_status)>
                            <div class="row mx-0">
                                <b-badge size="sm" variant="success-500" class="mr-1 mt-1 p-1 font-weight-normal">
                                    <feather-icon icon="CheckSquareIcon"/> Purchase Order Umum
                                </b-badge>
                                <b-badge size="sm" variant="success-500" class="mr-1 mt-1 p-1 font-weight-normal">
                                    <feather-icon icon="CheckSquareIcon"/> Manajer Umum
                                </b-badge>
                                <b-badge size="sm" variant="success-500" class="mr-1 mt-1 p-1 font-weight-normal">
                                    <feather-icon icon="CheckSquareIcon"/> DIrektur Keuangan
                                </b-badge>
                                <b-badge size="sm" variant="success-500" class="mr-1 mt-1 p-1 font-weight-normal">
                                    <feather-icon icon="CheckSquareIcon"/> DIrektur Utama
                                </b-badge>
                            </div>
                        </template>
                        <template #cell(action)>
                            <div class="row mx-0">
                                <b-button @click="active.ModalOpen = !active.ModalOpen" size="sm" variant="info-700" class="mr-1">
                                    <feather-icon icon="EyeIcon" /> View
                                </b-button>
                                <router-link :to="{ name: 'LogisticAcceptancePrintView' }" target="_blank">
                                    <b-button size="sm" variant="neutral-200">
                                        <feather-icon icon="PrinterIcon" /> Print
                                    </b-button>
                                </router-link>
                            </div>
                        </template>
                    </b-table>
                    <div class="col px-0 mt-20">
                        <div class="d-flex">
                            <span class="text-regular-14">Menampilkan {{ firstRow }} hingga {{ lastRow }} dari {{
                                AcceptanceTable.totalRows }} entri</span>
                            <div class="ml-auto">
                                <b-pagination v-model="AcceptanceTable.currentPage" :total-rows="AcceptanceTable.totalRows"
                                    :per-page="AcceptanceTable.perPage" :current-page="AcceptanceTable.currentPage" align="fill"
                                    size="sm" class="my-0"></b-pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <b-modal scrollable v-model="active.ModalOpen" title="Penerimaan" size="xl" hide-footer>
                <div class="card">
                    <div class="card-header bg-primary-200 font-weight-bold">Edit Data Transaksi</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <div class="col-lg-11">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div>
                                            <label class="c-form-label">Nomor Transaksi</label>
                                            <b-form-input />
                                        </div>
                                        <div class="mt-12">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label class="c-form-label">Jenis Dokumen</label>
                                                    <b-form-input />
                                                </div>
                                                <div class="col-lg-6 mt-12 mt-lg-0">
                                                    <label class="c-form-label">Type</label>
                                                    <b-form-select />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-12">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label class="c-form-label">Nomor Faktur</label>
                                                    <b-form-input />
                                                </div>
                                                <div class="col-lg-6 mt-12 mt-lg-0">
                                                    <label class="c-form-label">Faktur Pajak</label>
                                                    <b-form-select />
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="c-form-label">Reff Pembelian</label>
                                            <b-form-input />
                                        </div>
                                        <div>
                                            <label class="c-form-label">Supplier</label>
                                            <b-form-select />
                                        </div>
                                        <div class="mt-12">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label class="c-form-label">Tanggal PO</label>
                                                    <b-form-input type="date" />
                                                </div>
                                                <div class="col-lg-6 mt-12 mt-lg-0">
                                                    <label class="c-form-label">Jatuh Tempo</label>
                                                    <b-form-input type="date" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Tanggal Dokumen Penerimaan</label>
                                            <b-form-datepicker />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mt-12 mt-lg-0">
                                            <label class="c-form-label">Jenis Harga</label>
                                            <b-form-select />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Cara Bayar</label>
                                            <b-form-select />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Tanggal</label>
                                            <b-form-radio-group>
                                                <b-form-radio>Gudang Umum</b-form-radio>
                                                <b-form-radio>Gudang Farmasi</b-form-radio>
                                            </b-form-radio-group>
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Total</label>
                                            <b-input-group prepend="Nilai">
                                                <b-form-input type="number" />
                                            </b-input-group>
                                            <b-input-group prepend="PPN">
                                                <b-form-input type="number" />
                                            </b-input-group>
                                            <b-input-group prepend="Nilai + PPN">
                                                <b-form-input type="number" />
                                            </b-input-group>
                                            <b-input-group prepend="Pembulatan">
                                                <b-form-input type="number" />
                                            </b-input-group>
                                            <b-input-group prepend="Akhir">
                                                <b-form-input type="number" />
                                            </b-input-group>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-24">
                    <div class="card-header bg-primary-200 font-weight-bold">Total : Rp. 366.000,00</div>
                    <div class="card-body">
                        <b-table-simple bordered responsive small>
                            <b-thead class="text-center text-bold-12">
                                <b-tr>
                                    <b-th rowspan="2">No</b-th>
                                    <b-th rowspan="2">Kode</b-th>
                                    <b-th rowspan="2" style="min-width:200px;">Nama Barang</b-th>
                                    <b-th rowspan="2" style="min-width:100px;">Principal</b-th>
                                    <b-th colspan="2">Info</b-th>
                                    <b-th rowspan="2">Kemasan</b-th>
                                    <b-th rowspan="2">PO</b-th>
                                    <b-th rowspan="2">Terima</b-th>
                                    <b-th colspan="2">Jumlah</b-th>
                                    <b-th colspan="2">Harga</b-th>
                                    <b-th rowspan="2">Diskon</b-th>
                                    <b-th colspan="3">Total</b-th>
                                </b-tr>
                                <b-tr>
                                    <b-th style="min-width:100px;">Batch</b-th>
                                    <b-th style="min-width:100px;">EXP</b-th>
                                    <b-th style="min-width:100px;">Kemasan</b-th>
                                    <b-th style="min-width:100px;">Satuan</b-th>
                                    <b-th style="min-width:100px;">Kemasan</b-th>
                                    <b-th style="min-width:100px;">Satuan</b-th>
                                    <b-th style="min-width:100px;">Sebelum Disc</b-th>
                                    <b-th style="min-width:100px;">Diskon</b-th>
                                    <b-th style="min-width:100px;">Rp</b-th>
                                </b-tr>
                            </b-thead>
                            <b-tbody class="text-regular-12">
                                <!-- loop this line -->
                                <b-tr>
                                    <b-td>1</b-td>
                                    <b-td></b-td>
                                    <b-td>Stiker AsturoWarna Biru <b-badge variant="warning-500" class="p-1 font-weight-normal">Stock</b-badge></b-td>
                                    <b-td><b-form-select size="sm" /></b-td>
                                    <b-td><b-form-input size="sm" type="number" value="1" /></b-td>
                                    <b-td><b-form-input size="sm" type="date" value="1" /></b-td>
                                    <b-td><b-form-select size="sm" /></b-td>
                                    <b-td><b-form-input size="sm" type="number" value="1" /></b-td>
                                    <b-td><b-form-input size="sm" type="number" value="5" /></b-td>
                                    <b-td><b-form-input size="sm" value="1 pack" /></b-td>
                                    <b-td><b-form-input size="sm" value="5 pack" /></b-td>
                                    <b-td><b-form-input size="sm" type="number" value="45000" /></b-td>
                                    <b-td><b-form-input size="sm" type="number" value="45000" /></b-td>
                                    <b-td><b-form-input size="sm" type="number" value="0" /></b-td>
                                    <b-td><b-form-input size="sm" type="number" value="225000" /></b-td>
                                    <b-td><b-form-input size="sm" type="number" value="0" /></b-td>
                                    <b-td><b-form-input size="sm" type="number" value="225000" /></b-td>
                                </b-tr>
                                <b-tr>
                                    <b-th colspan="14" class="text-right">TOTAL</b-th>
                                    <b-td><b-form-input size="sm" type="number" value="0" /></b-td>
                                    <b-td><b-form-input size="sm" type="number" value="0" /></b-td>
                                    <b-td><b-form-input size="sm" type="number" value="0" /></b-td>
                                </b-tr>
                            </b-tbody>
                        </b-table-simple>
                    </div>
                </div>
                <div class="card mt-24">
                    <div class="card-header bg-primary-200 d-flex flex-wrap">
                        <span class="font-weight-bold col-12 col-lg px-0">Histori Validasi Permintaan</span>
                        <div class="ml-lg-auto d-flex">
                            <b-button pill size="sm" variant="success-500" class="mr-1">
                                <feather-icon icon="PrinterIcon" /> Cetak kajian
                            </b-button>
                            <b-button pill size="sm" variant="info-600" class="mr-1">
                                <feather-icon icon="FileIcon" /> Lihat kajian
                            </b-button>
                            <!-- <b-button size="sm" variant="neutral-500"> <feather-icon icon="RotateCcwIcon" /> </b-button> -->
                        </div>
                    </div>
                    <div class="card-body">
                        <b-table bordered responsive hover small head-variant="light" :fields="AcceptanceModalTable.field"
                            :items="AcceptanceModalTable.item" thead-class="text-bold-14" tbody-tr-class="text-regular-14"
                            class="text-center"></b-table>
                    </div>
                </div>
            </b-modal>
        </div>
    </div>
</template>
<script>
import TopLogisticMenu from '@/@core/components/top-menu/TopLogisticMenu.vue';

export default {
    name: 'PatientPayment',
    components: {
        TopLogisticMenu
    },
    data() {
        return {
            active: {
                ModalOpen: false,
            },
            AcceptanceTable: {
                field: [
                    { key: 'PO_number', label: 'No.PO - Purchase Order' },
                    { key: 'types', label: 'Jenis' },
                    { key: 'PO_date', label: 'Tanggal PO' },
                    { key: 'warehouse', label: 'Gudang' },
                    { key: 'values', label: 'Nilai' },
                    { key: 'user_updt', label: 'User Update' },
                    { key: 'last_updt', label: 'Last Update' },
                    { key: 'validation_status', label: 'Status Validasi' },
                    { key: 'acceptance_status', label: 'Status Penerimaan' },
                    { key: 'action', label: 'Action', thStyle: { minWidth: '150px' } },
                ],
                item: [
                    {
                        PO_number: '20220929090001',
                        types: 'PO - Perencanaan',
                        PO_date: '28/10/2021',
                        warehouse: 'Gudang Umum',
                        values: '0.00',
                        user_updt: 'dr. Titi Setyoni',
                        last_updt: '28/10/2021',
                        // validation_status: 'Belum Validasi',
                        acceptance_status: '-',
                    },
                    {
                        PO_number: '20220929090001',
                        types: 'PO - Perencanaan',
                        PO_date: '28/10/2021',
                        warehouse: 'Gudang Umum',
                        values: '366.000.00',
                        user_updt: 'dr. Titi Setyoni',
                        last_updt: '28/10/2021',
                        acceptance_status: '-',
                    },
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
            AcceptanceModalTable: {
                field: [
                    { key: 'ver_no', label: 'Ver No' },
                    { key: 'ver', label: 'Ver' },
                    { key: 'authorization', label: 'Otorisasi' },
                    { key: 'user', label: 'User' },
                    { key: 'date', label: 'Tanggal' },
                ],
                item: [
                    {
                        ver_no: '-',
                        ver: '-',
                        authorization: '-',
                        user: '-',
                        date: '-',
                    },
                ],
                // currentPage: 1,
                // totalRows: 1,
                // perPage: 5,
            },

        }
    },
    mounted() {
        this.AcceptanceTable.totalRows = this.AcceptanceTable.item.length
    },
    computed: {
        firstRow() {
            return (this.AcceptanceTable.currentPage - 1) * this.AcceptanceTable.perPage + 1
        },
        lastRow() {
            return Math.min(this.AcceptanceTable.currentPage * this.AcceptanceTable.perPage, this.AcceptanceTable.totalRows)
        },
    },
    methods: {
        // goToTab(tab) {
        //     this.active.tab = tab;
        // },
        patientFiltered(patientItems) {
            this.AcceptanceTable.totalRows = patientItems.length
            this.AcceptanceTable.currentPage = 1
        },
    },
};
</script>
<style>
.label-head::before {
    content: '';
    border-left: 6px solid #648DE1;
}
</style>