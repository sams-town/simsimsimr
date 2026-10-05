<template>
    <div>
        <TopLogisticMenu selectedMenu="menu3" />
        <div class="col mt-32">
            <div class="label-head">
                <span class="px-16 text-bold-24 text-neutral-700">PO - Purchase Order</span>
            </div>
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
                        <select id="per-page-select" v-model="POTable.perPage"
                            class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                            <option value="5" selected>5</option>
                            <option value="10" selected>10</option>
                            <option value="15" selected>15</option>
                        </select>
                        <span class="text-regular-14">Entri</span>
                    </div>
                    <b-table bordered responsive small head-variant="light" :fields="POTable.field" :items="POTable.item"
                        :current-page="POTable.currentPage" @filtered="patientFiltered" :per-page="POTable.perPage"
                        thead-class="text-bold-14" tbody-tr-class="text-regular-14" class="mt-12 text-center">
                        <template #cell(validation_status)>
                            <div class="row mx-0">
                                <b-badge size="sm" variant="success-500" class="mr-1 mt-1 p-1 font-weight-normal">
                                    <feather-icon icon="CheckSquareIcon" /> Purchase Order Umum
                                </b-badge>
                                <b-badge size="sm" variant="success-500" class="mr-1 mt-1 p-1 font-weight-normal">
                                    <feather-icon icon="CheckSquareIcon" /> Manajer Umum
                                </b-badge>
                                <b-badge size="sm" variant="success-500" class="mr-1 mt-1 p-1 font-weight-normal">
                                    <feather-icon icon="CheckSquareIcon" /> DIrektur Keuangan
                                </b-badge>
                                <b-badge size="sm" variant="success-500" class="mr-1 mt-1 p-1 font-weight-normal">
                                    <feather-icon icon="CheckSquareIcon" /> DIrektur Utama
                                </b-badge>
                            </div>
                        </template>
                        <!-- click-show here! -->
                        <template #cell(acceptance_status)="row">
                            <div>
                                <b-button size="sm" @click="row.toggleDetails"
                                    :variant="row.value === 'Sudah Diterima' ? 'success-500' : 'danger-600'">
                                    <feather-icon icon="ClockIcon" class="mr-1" />{{ row.value }}
                                </b-button>
                            </div>
                        </template>
                        <template #row-details="row">
                            <div class="card-body">
                                <b-table bordered responsive small head-variant="light" :fields="InsidePOTable.field"
                                    :items="InsidePOTable.item" thead-class="text-bold-12" tbody-tr-class="text-regular-12"
                                    class="mt-12 text-center">
                                </b-table>
                            </div>
                        </template>
                        <template #cell(action)>
                            <div class="row mx-0">
                                <b-button @click="active.ModalOpen = !active.ModalOpen" size="sm" variant="info-700"
                                    class="mr-1">
                                    <feather-icon icon="EyeIcon" /> View
                                </b-button>
                                <router-link :to="{ name: 'LogisticPOPrintView' }" target="_blank">
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
                                POTable.totalRows }} entri</span>
                            <div class="ml-auto">
                                <b-pagination v-model="POTable.currentPage" :total-rows="POTable.totalRows"
                                    :per-page="POTable.perPage" :current-page="POTable.currentPage" align="fill" size="sm"
                                    class="my-0"></b-pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <b-modal scrollable v-model="active.ModalOpen" title="PO - Purchase Order " size="xl" hide-footer>
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
                                            <label class="c-form-label">Jenis Po</label>
                                            <b-form-select />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Reff Permintaan</label>
                                            <b-form-select />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Jatuh Tempo</label>
                                            <b-form-select />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Hari</label>
                                            <b-form-datepicker />
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
                                            <label class="c-form-label">Supplier</label>
                                            <div class="row align-items-center">
                                                <div class="col-lg-7">
                                                    <b-form-select />
                                                </div>
                                                <div class="col-lg mt-2 mt-lg-0">
                                                    <b-button size="sm" variant="outline-primary-600"
                                                        class="w-100 font-weight-bold border-0">
                                                        <feather-icon icon="PlusIcon" /> Tambah Supplier
                                                    </b-button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mt-12 mt-lg-0">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label class="c-form-label">Nomor Rekening</label>
                                                    <b-form-input type="number" />
                                                </div>
                                                <div class="col-lg-6 mt-12 mt-lg-0">
                                                    <label class="c-form-label">A/N Rekening</label>
                                                    <b-form-input />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-12">
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
                    <div class="card-header bg-primary-200 font-weight-bold">Total : Rp. 12.567.200,-</div>
                    <div class="card-body">
                        <b-table-simple bordered responsive small>
                            <b-thead class="text-center text-bold-12">
                                <b-tr>
                                    <b-th rowspan="2"><b-form-checkbox /></b-th>
                                    <b-th rowspan="2">No</b-th>
                                    <b-th rowspan="2">Kode</b-th>
                                    <b-th rowspan="2" style="min-width:200px;">Nama Barang</b-th>
                                    <b-th rowspan="2" style="min-width:100px;">Ref permintaan</b-th>
                                    <b-th colspan="4">Pemakaian</b-th>
                                    <b-th colspan="2">Jumlah</b-th>
                                    <b-th colspan="2">Harga</b-th>
                                    <b-th colspan="2">Total</b-th>
                                </b-tr>
                                <b-tr>
                                    <b-th style="min-width:100px;">Kemasan</b-th>
                                    <b-th style="min-width:100px;">Isi</b-th>
                                    <b-th style="min-width:100px;">Kemasan</b-th>
                                    <b-th style="min-width:100px;">Satuan</b-th>
                                    <b-th style="min-width:100px;">Kemasan</b-th>
                                    <b-th style="min-width:100px;">Satuan</b-th>
                                    <b-th style="min-width:100px;">Diskon</b-th>
                                    <b-th style="min-width:100px;">Sebelum Disc</b-th>
                                    <b-th style="min-width:100px;">Diskon</b-th>
                                    <b-th style="min-width:100px;">Rp</b-th>
                                </b-tr>
                            </b-thead>
                            <b-tbody class="text-regular-12">
                                <!-- loop this line -->
                                <b-tr>
                                    <b-td><b-form-checkbox /></b-td>
                                    <b-td>1</b-td>
                                    <b-td>PCS 20220929090001</b-td>
                                    <b-td>Pulpen Hitam</b-td>
                                    <b-td><feather-icon icon="FileTextIcon" /></b-td>
                                    <b-td>Pcs</b-td>
                                    <b-td>20</b-td>
                                    <b-td>0,00 Pcs</b-td>
                                    <b-td>0,00 Pcs</b-td>
                                    <b-td>Pcs</b-td>
                                    <b-td>1 Pcs</b-td>
                                    <b-td>1.320.00</b-td>
                                    <b-td>1.320.00</b-td>
                                    <b-td>0.00</b-td>
                                    <b-td>1.320.00</b-td>
                                </b-tr>
                                <b-tr>
                                    <b-th colspan="12" class="text-right">TOTAL</b-th>
                                    <b-td>0.00</b-td>
                                    <b-td>0.00</b-td>
                                    <b-td>0.00</b-td>
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
                        <b-table bordered responsive hover small head-variant="light" :fields="POModalTable.field"
                            :items="POModalTable.item" thead-class="text-bold-14" tbody-tr-class="text-regular-14"
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
            POTable: {
                field: [
                    { key: 'PO_number', label: 'No.PO - Purchase Order' },
                    { key: 'types', label: 'Jenis' },
                    { key: 'PO_date', label: 'Tanggal PO' },
                    { key: 'warehouse', label: 'Gudang' },
                    { key: 'values', label: 'Nilai' },
                    { key: 'user_updt', label: 'User Update' },
                    { key: 'last_updt', label: 'Last Update' },
                    { key: 'validation_status', label: 'Status Validasi' },
                    { key: 'acceptance_status', label: 'Status Penerimaan', thStyle: { minWidth: '150px' } },
                    { key: 'action', label: 'Action', thStyle: { minWidth: '170px' } },
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
                        acceptance_status: 'Sudah Diterima',
                    },
                    {
                        PO_number: '20220929090001',
                        types: 'PO - Perencanaan',
                        PO_date: '28/10/2021',
                        warehouse: 'Gudang Umum',
                        values: '366.000.00',
                        user_updt: 'dr. Titi Setyoni',
                        last_updt: '28/10/2021',
                        acceptance_status: 'Belum Diterima',
                    },
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
            InsidePOTable: {
                field: [
                    { key: 'no', label: 'No' },
                    { key: 'acc_documnet', label: 'No.Dokumen Penerimaan' },
                    { key: 'catalog_name', label: 'Nama Katalog' },
                    { key: 'po_package', label: 'Isi Kemasan PO' },
                    { key: 'acc_packege', label: 'Isi Kemasan Terima' },
                    { key: 'acc_date', label: 'Tanggal Terima' },
                    { key: 'acc_user', label: 'Ver User Terima' },
                ],
                item: [
                    {
                        no: '1',
                        acc_documnet: 'PNM/Med/11/2021/00001',
                        catalog_name: 'Amplop Coklat A4',
                        po_package: '1 Pcs',
                        acc_package: '1 Pcs',
                        acc_date: '01-11-2021 10:32:56',
                        acc_user: 'TOTO',
                    },
                    {
                        no: '2',
                        acc_documnet: 'PNM/Med/11/2021/00001',
                        catalog_name: 'Amplop Coklat A4',
                        po_package: '1 Pcs',
                        acc_package: '1 Pcs',
                        acc_date: '01-11-2021 10:32:56',
                        acc_user: 'TOTO',
                    },
                ],
            },
            POModalTable: {
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
        this.POTable.totalRows = this.POTable.item.length
    },
    computed: {
        firstRow() {
            return (this.POTable.currentPage - 1) * this.POTable.perPage + 1
        },
        lastRow() {
            return Math.min(this.POTable.currentPage * this.POTable.perPage, this.POTable.totalRows)
        },
    },
    methods: {
        // goToTab(tab) {
        //     this.active.tab = tab;
        // },
        patientFiltered(patientItems) {
            this.POTable.totalRows = patientItems.length
            this.POTable.currentPage = 1
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