<template>
    <div>
        <TopPharmacyMenu selectedMenu="menu7" />
        <div class="mt-32 col">
            <p class="text-semi-18">Transaksi Pengurangan Stok gudang</p>
            <div class="row mt-16 mx-0">
                <b-button @click="toggleTab('tab1')" pill
                    :variant="active.tab === 'tab1' ? 'primary-500' : 'outline-indigo-500'" class="mr-2">
                    <feather-icon icon="MenuIcon" /> List Jadwal
                </b-button>
                <b-button @click="toggleTab('tab2')" pill
                    :variant="active.tab === 'tab2' ? 'primary-500' : 'outline-indigo-500'">
                    <feather-icon icon="PlusIcon" /> Tambah Jadwal
                </b-button>
            </div>
            <section v-if="active.tab === 'tab1'" class="mt-24">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="row mx-0">
                        <span class="text-regular-14">Tampilkan</span>
                        <select id="per-page-select" v-model="ReductionTable.perPage"
                            class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                            <option value="5" selected>5</option>
                            <option value="10" selected>10</option>
                            <option value="15" selected>15</option>
                        </select>
                        <span class="text-regular-14">Entri</span>
                    </div>
                    <div class="col-6 col-lg-3 d-flex align-items-center position-relative">
                        <b-form-input size="sm" autocomplete="off" placeholder="Search" v-model="searchTerm"
                            class="rounded-pill pl-5"></b-form-input>
                        <b-icon icon="search" class="position-absolute pl-3 text-neutral-400" />
                    </div>
                </div>
                <b-table bordered responsive small head-variant="light" :fields="ReductionTable.fields"
                    :items="filteredItems" :current-page="ReductionTable.currentPage" :per-page="ReductionTable.perPage"
                    class="mt-12 text-center" thead-class="text-bold-14" tbody-tr-class="text-regular-14">
                    <template #cell(warehouse_verif)="data">
                        <div>
                            <b-badge v-if="data.value === 'acc'" variant="success-500" class="p-1"><feather-icon
                                    icon="CheckSquareIcon" /></b-badge>
                        </div>
                    </template>
                    <template #cell(recipt_verif)="data">
                        <div>
                            <b-badge v-if="data.value === 'acc'" variant="success-500" class="p-1"><feather-icon
                                    icon="CheckSquareIcon" /></b-badge>
                        </div>
                    </template>
                    <template #cell(account_verif)="data">
                        <div>
                            <b-badge v-if="data.value === 'acc'" variant="success-500" class="p-1"><feather-icon
                                    icon="CheckSquareIcon" /></b-badge>
                        </div>
                    </template>
                    <template #cell(action)>
                        <div class="d-flex">
                            <b-button size="sm" variant="info-600" class="mr-1"><feather-icon
                                    icon="FileTextIcon" /></b-button>
                            <b-button size="sm" variant="orange-500"><feather-icon icon="PrinterIcon" /></b-button>
                        </div>
                    </template>
                </b-table>
                <div class="col px-0 mt-20">
                    <div class="d-flex">
                        <!-- <span class="text-regular-14">Menampilkan 1 hingga 10 dari 200 entri</span> -->
                        <span class="text-regular-14">Menampilkan {{ firstRow }} hingga {{ lastRow }} dari {{
                            ReductionTable.totalRows }} entri</span>
                        <div class="ml-auto">
                            <b-pagination v-model="ReductionTable.currentPage" :total-rows="ReductionTable.totalRows"
                                :per-page="ReductionTable.perPage" :current-page="ReductionTable.currentPage" align="fill"
                                size="sm" class="my-0"></b-pagination>
                        </div>
                    </div>
                </div>
            </section>
            <section v-else class="mt-24">
                <section class="card">
                    <div class="card-header bg-primary-200">
                        <div class="d-flex justify-content-between">
                            <span class="font-weight-bold">Data Transaksi</span>
                            <div class="d-flex">
                                <b-button pill size="sm" variant="info-700" class="mr-1 px-3">Simpan</b-button>
                                <b-button pill size="sm" variant="orange-500" class="px-3"><feather-icon
                                        icon="ChevronLeftIcon" /> Kembali</b-button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body d-flex justify-content-center">
                        <div class="col-lg-10">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div>
                                        <label class="c-form-label">No Transaksi</label>
                                        <b-form-input />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">No Dokumen</label>
                                        <b-form-input />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Tanggal Dokumen</label>
                                        <b-form-datepicker />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Jenis Transaksi</label>
                                        <b-form-select />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Ref Penerimaan</label>
                                        <b-form-select />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Keterangan</label>
                                        <b-textarea rows="3" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mt-12 mt-lg-0">
                                        <label class="c-form-label">Supplier</label>
                                        <b-form-datepicker />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Gudang</label>
                                        <b-form-select />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Total</label>
                                        <div>
                                            <b-input-group prepend="PPN">
                                                <b-form-input disabled value="0,00" class="text-right" />
                                            </b-input-group>
                                            <b-input-group prepend="Nilai">
                                                <b-form-input disabled value="0,00" class="text-right" />
                                            </b-input-group>
                                            <b-input-group prepend="Nilai + PPN">
                                                <b-form-input disabled value="0,00" class="text-right" />
                                            </b-input-group>
                                            <b-input-group prepend="Pembulatan">
                                                <b-form-input value="0,00" class="text-right" />
                                            </b-input-group>
                                            <b-input-group prepend="Akhir">
                                                <b-form-input disabled value="0,00" class="text-right" />
                                            </b-input-group>
                                        </div>
                                        <!-- <b-form-select /> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="card mt-32">
                    <div class="card-header bg-primary-200 d-flex justify-content-between">
                        <span class="font-weight-bold">Total: Rp. 0,00</span>
                        <b-button pill variant="danger-500" size="sm" class="px-3">
                            <feather-icon icon="TrashIcon" /> Hapus
                        </b-button>
                    </div>
                    <div class="card-body">
                        <b-table-simple responsive small bordered>
                            <b-thead class="text-center text-bold-14">
                                <b-tr>
                                    <b-th rowspan="2">
                                        <b-form-checkbox>No</b-form-checkbox>
                                    </b-th>
                                    <b-th rowspan="2">KODE</b-th>
                                    <b-th rowspan="2" style="min-width: 200px;">NAMA BARANG</b-th>
                                    <b-th colspan="2">INFO</b-th>
                                    <b-th rowspan="2" style="min-width: 140px;">KEMASAN</b-th>
                                    <b-th rowspan="2" style="min-width: 70px;">ISI</b-th>
                                    <b-th colspan="2">JUMLAH</b-th>
                                    <b-th colspan="2">HARGA</b-th>
                                    <b-th rowspan="2">DISKON %</b-th>
                                    <b-th colspan="3">TOTAL</b-th>
                                </b-tr>
                                <b-tr>
                                    <b-th style="min-width: 140px;">BATCH</b-th>
                                    <b-th style="min-width: 140px;">EXP</b-th>
                                    <b-th style="min-width: 140px;">KEMASAN</b-th>
                                    <b-th style="min-width: 140px;">SATUAN</b-th>
                                    <b-th style="min-width: 140px;">KEMASAN</b-th>
                                    <b-th style="min-width: 140px;">SATUAN</b-th>
                                    <b-th style="min-width: 140px;">SEBELUM DISC</b-th>
                                    <b-th style="min-width: 140px;">DISC</b-th>
                                    <b-th style="min-width: 140px;">RP</b-th>
                                </b-tr>
                            </b-thead>
                            <b-tbody class="text-regular-14 text-center">
                                <b-tr>
                                    <b-td><b-form-checkbox>1</b-form-checkbox></b-td>
                                    <b-td></b-td>
                                    <b-td><b-form-select disabled /></b-td>
                                    <b-td><b-form-input disabled /></b-td>
                                    <b-td><b-form-input disabled /></b-td>
                                    <b-td><b-form-select disabled /></b-td>
                                    <b-td><b-form-input disabled value="1" /></b-td>
                                    <b-td><b-form-input disabled value="0 BOX" /></b-td>
                                    <b-td><b-form-input disabled value="0 BOX" /></b-td>
                                    <b-td><b-form-input disabled value="0,00" /></b-td>
                                    <b-td><b-form-input disabled value="0,00" /></b-td>
                                    <b-td><b-form-input disabled value="0,00" /></b-td>
                                    <b-td><b-form-input disabled value="0,00" /></b-td>
                                    <b-td><b-form-input disabled value="0,00" /></b-td>
                                    <b-td><b-form-input disabled value="0,00" /></b-td>
                                </b-tr>
                                <b-tr>
                                    <b-td colspan="12" class="text-right font-weight-bold">TOTAL</b-td>
                                    <b-td><b-form-input disabled value="0,00" /></b-td>
                                    <b-td><b-form-input disabled value="0,00" /></b-td>
                                    <b-td><b-form-input disabled value="0,00" /></b-td>
                                </b-tr>
                            </b-tbody>
                        </b-table-simple>
                    </div>
                </section>
                <section class="card mt-32">
                    <div class="card-header bg-primary-200 font-weight-bold">HISTORI VALIDASI PENERIMAAN</div>
                    <div class="card-body">
                        <b-table responsive bordered small :fields="AcceptanceTable.fields" :items="AcceptanceTable.items"
                            class="text-regular-12">
                            <template #cell(ver)>
                                <div>
                                    <b-form-checkbox value="0" />
                                </div>
                            </template>
                            <template #cell(updt_stock)>
                                <div>
                                    <b-form-checkbox value="0" />
                                </div>
                            </template>
                        </b-table>
                    </div>
                </section>
            </section>
        </div>
    </div>
</template>

<script>
import TopPharmacyMenu from '@/@core/components/top-menu/TopPharmacyMenu.vue';

export default {
    components: {
        TopPharmacyMenu
    },
    data() {
        return {
            active: {
                tab: 'tab1',
            },
            searchTerm: '',
            ReductionTable: {
                fields: [
                    { key: 'document_number', label: 'No Dokumen', thStyle: { minWidth: '140px' } },
                    { key: 'receipt_number', label: 'No Penerimaan', thStyle: { minWidth: '140px' } },
                    { key: 'date', label: 'Tanggal', thStyle: { minWidth: '140px' } },
                    { key: 'document', label: 'Dokumen', thStyle: { minWidth: '140px' } },
                    { key: 'supplier', label: 'Supplier', thStyle: { minWidth: '140px' } },
                    { key: 'warehouse', label: 'Gudang', thStyle: { minWidth: '140px' } },
                    { key: 'notes', label: 'Keterangan', thStyle: { minWidth: '140px' } },
                    { key: 'warehouse_verif', label: 'Verif Gudang', thStyle: { minWidth: '140px' } },
                    { key: 'recipt_verif', label: 'Verif Terima', thStyle: { minWidth: '140px' } },
                    { key: 'account_verif', label: 'Verif Akun', thStyle: { minWidth: '140px' } },
                    { key: 'values', label: 'Nilai', thStyle: { minWidth: '140px' } },
                    { key: 'action', label: 'Aksi', thStyle: { minWidth: '140px' } },
                ],
                items: [
                    {
                        document_number: 'PRM/Med/11/2021/00020',
                        receipt_number: '-',
                        date: '04-11-2021',
                        document: 'Retur Penerimaan',
                        supplier: 'PT PARIT PADANG GLOBAL',
                        warehouse: 'INSTALASI FARMASI',
                        notes: 'SALAH INPUT STOK',
                        warehouse_verif: 'acc',
                        recipt_verif: 'acc',
                        account_verif: 'acc',
                        values: '1',
                    },
                    {
                        document_number: 'PRM/Med/11/2021/00020',
                        receipt_number: '-',
                        date: '04-11-2021',
                        document: 'Retur Penerimaan',
                        supplier: 'PT PARIT PADANG GLOBAL',
                        warehouse: 'INSTALASI FARMASI',
                        notes: 'SALAH INPUT STOK',
                        warehouse_verif: 'acc',
                        recipt_verif: 'acc',
                        account_verif: 'acc',
                        values: '1',
                    },
                    {
                        document_number: 'PRM/Med/11/2021/00020',
                        receipt_number: '-',
                        date: '04-11-2021',
                        document: 'Retur Penerimaan',
                        supplier: 'PT PARIT PADANG GLOBAL',
                        warehouse: 'INSTALASI FARMASI',
                        notes: 'SALAH INPUT STOK',
                        warehouse_verif: 'acc',
                        recipt_verif: 'acc',
                        account_verif: 'acc',
                        values: '1',
                    },
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
            AcceptanceTable: {
                fields: [
                    { key: 'ver_number', label: 'No Ver' },
                    { key: 'ver', label: 'Ver' },
                    { key: 'authorization', label: 'Otorisasi' },
                    { key: 'user', label: 'User' },
                    { key: 'date', label: 'Tanggal' },
                    { key: 'updt_stock', label: 'Updt Stok' },
                ],
                items: [
                    {
                        ver_number: '1',
                        authorization: 'Gudang',
                        user: '-',
                        date: '00-00-0000 00:00:00',
                    },
                    {
                        ver_number: '2',
                        authorization: 'Tim Penerima',
                        user: '-',
                        date: '00-00-0000 00:00:00',
                    },
                    {
                        ver_number: '3',
                        authorization: 'Akuntansi',
                        user: '-',
                        date: '00-00-0000 00:00:00',
                    },
                ]
            }
        }
    },
    mounted() {
        this.ReductionTable.totalRows = this.ReductionTable.items.length
    },
    computed: {
        filteredItems() {
            const searchTerm = this.searchTerm.toLowerCase().trim()
            if (!searchTerm) {
                return this.ReductionTable.items
            }
            return this.ReductionTable.items.filter(item => {
                // Update the condition based on the structure of the items array
                return item.document_number.toLowerCase().includes(searchTerm) ||
                    item.receipt_number.toLowerCase().includes(searchTerm) ||
                    item.date.toLowerCase().includes(searchTerm) ||
                    item.document.toLowerCase().includes(searchTerm) ||
                    item.supplier.toLowerCase().includes(searchTerm) ||
                    item.warehouse.toLowerCase().includes(searchTerm) ||
                    item.notes.toLowerCase().includes(searchTerm) ||
                    item.values.toLowerCase().includes(searchTerm)
            })
        },
        firstRow() {
            return (this.ReductionTable.currentPage - 1) * this.ReductionTable.perPage + 1
        },
        lastRow() {
            return Math.min(this.ReductionTable.currentPage * this.ReductionTable.perPage, this.ReductionTable.totalRows)
        },
    },
    methods: {
        toggleTab(tab) {
            this.active.tab = tab;
        }
    }
} 
</script>