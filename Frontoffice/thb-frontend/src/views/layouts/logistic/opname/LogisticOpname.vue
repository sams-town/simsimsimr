<template>
    <div>
        <TopLogisticMenu selectedMenu="menu10" />
        <div class="col mt-32">
            <section>
                <div class="row mx-0">
                    <b-button size="sm" @click="changeTab('stock')" pill
                        :variant="active.MainTab === 'stock' ? 'primary-600' : 'outline-primary-500'" class="mr-2 px-3">List
                        Stok Opname</b-button>
                    <b-button size="sm" @click="changeTab('aktivate')" pill
                        :variant="active.MainTab === 'aktivate' ? 'primary-600' : 'outline-primary-500'"
                        class="px-3">Aktivasi Stok Opname</b-button>
                </div>
            </section>
            <section v-if="active.MainTab === 'stock'" class="mt-24">
                <div class="label-head">
                    <span class="px-16 text-bold-24 text-neutral-700">List Stok Opname</span>
                </div>
                <div class="card mt-12">
                    <div class="card-header font-weight-bold bg-primary-200">Filter Data</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <div class="col-lg-5">
                                <div>
                                    <label class="c-form-label">Keterangan Aktifasi SO</label>
                                    <b-form-select />
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Unit Penyimpanan</label>
                                    <b-form-select />
                                </div>
                                <div class="d-flex justify-content-end mt-24">
                                    <b-button variant="danger-600">
                                        <feather-icon icon="XIcon" /> Clear Filter
                                    </b-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col mt-32">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="row mx-0">
                            <span class="text-regular-14">Tampilkan</span>
                            <select id="per-page-select" v-model="ListStockTable.perPage"
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
                    <b-table bordered responsive hover head-variant="light" :fields="ListStockTable.fields"
                        :items="filteredItems" :current-page="ListStockTable.currentPage" :per-page="ListStockTable.perPage"
                        class="mt-16 text-center" tbody-tr-class="text-medium-12" thead-class="text-bold-12">
                        <template #cell(activate_so)>
                            <div>
                                <b-form-checkbox></b-form-checkbox>
                            </div>
                        </template>
                        <template #cell(close_so)>
                            <div>
                                <b-form-checkbox></b-form-checkbox>
                            </div>
                        </template>
                    </b-table>
                    <div class="col px-0 mt-20">
                        <div class="d-flex">
                            <span class="text-regular-14">Menampilkan {{ firstRow }} hingga {{ lastRow }} dari {{
                                ListStockTable.totalRows }} entri</span>
                            <div class="ml-auto">
                                <b-pagination v-model="ListStockTable.currentPage" :total-rows="ListStockTable.totalRows"
                                    :per-page="ListStockTable.perPage" :current-page="ListStockTable.currentPage"
                                    align="fill" size="sm" class="my-0"></b-pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section v-else class="mt-24">
                <div class="label-head">
                    <span class="px-16 text-bold-24 text-neutral-700">Aktivasi Stok Opname</span>
                </div>
                <div class="card mt-12">
                    <div class="card-header font-weight-bold bg-primary-200">Aktivasi</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg">
                                        <label class="c-form-label">Tanggal Stok Opname</label>
                                        <b-form-input type="date" />
                                    </div>
                                    <div class="col-lg mt-12 mt-lg-0">
                                        <label class="c-form-label">Sampai Dengan</label>
                                        <b-form-input type="date" />
                                    </div>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Status Opname</label>
                                    <b-form-radio-group>
                                        <b-form-radio>Aktif</b-form-radio>
                                        <b-form-radio>Tidak Aktif</b-form-radio>
                                    </b-form-radio-group>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Keterangan</label>
                                    <b-form-textarea />
                                </div>
                                <div class="d-flex justify-content-end mt-24">
                                    <b-button variant="primary-600">
                                        <feather-icon icon="SaveIcon" /> Simpan
                                    </b-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col mt-32">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="row mx-0">
                            <span class="text-regular-14">Tampilkan</span>
                            <select id="per-page-select" v-model="ActivateStockTable.perPage"
                                class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                                <option value="5" selected>5</option>
                                <option value="10" selected>10</option>
                                <option value="15" selected>15</option>
                            </select>
                            <span class="text-regular-14">Entri</span>
                        </div>
                        <div class="col-6 col-lg-3 d-flex align-items-center position-relative">
                            <b-form-input size="sm" autocomplete="off" placeholder="Search" v-model="activateSearchTerm"
                                class="rounded-pill pl-5"></b-form-input>
                            <b-icon icon="search" class="position-absolute pl-3 text-neutral-400" />
                        </div>
                    </div>
                    <b-table bordered responsive hover head-variant="light" :fields="ActivateStockTable.fields"
                        :items="filteredActivateItems" :current-page="ActivateStockTable.currentPage" :per-page="ActivateStockTable.perPage"
                        class="mt-16 text-center" tbody-tr-class="text-medium-12" thead-class="text-bold-12">
                        <template #cell(action)>
                            <div>
                                <b-button size="sm" variant="warning-500"><feather-icon icon="Edit2Icon" /></b-button>
                            </div>
                        </template>
                    </b-table>
                    <div class="col px-0 mt-20">
                        <div class="d-flex">
                            <span class="text-regular-14">Menampilkan {{ firstActivateRow }} hingga {{ lastActivateRow }} dari {{
                                ActivateStockTable.totalRows }} entri</span>
                            <div class="ml-auto">
                                <b-pagination v-model="ActivateStockTable.currentPage" :total-rows="ActivateStockTable.totalRows"
                                    :per-page="ActivateStockTable.perPage" :current-page="ActivateStockTable.currentPage"
                                    align="fill" size="sm" class="my-0"></b-pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
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
                MainTab: 'stock',
            },
            searchTerm: '',
            activateSearchTerm: '',
            ListStockTable: {
                fields: [
                    { key: 'action', label: 'ACT' },
                    { key: 'storage_unit', label: 'Unit Penyimpanan' },
                    { key: 'notes', label: 'Keterangan' },
                    { key: 'activate_so', label: 'Aktifasi SO' },
                    { key: 'activate_date', label: 'Tgl Aktivasi' },
                    { key: 'activate_operator', label: 'Operator Aktifasi' },
                    { key: 'close_so', label: 'Tutup SO' },
                    { key: 'close_so_date', label: 'Tgl Tutup SO' },
                    { key: 'close_so_operator', label: 'Operator Tutup SO' },
                ],
                items: [
                    {
                        action: '',
                        storage_unit: 'FARMASI',
                        notes: 'Stok Opname November Unit: FARMASI',
                        activate_date: '',
                        activate_operator: '',
                        close_so_date: '',
                        close_so_operator: '',
                    },
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
            ActivateStockTable: {
                fields: [
                    { key: 'action', label: 'ACT' },
                    { key: 'start_stock', label: 'Tgl Mulai Stok Opname' },
                    { key: 'end_stock', label: 'Tgl Berakhir Stok Opname' },
                    { key: 'notes', label: 'Keterangan' },
                    { key: 'status', label: 'Status' },
                    { key: 'operator', label: 'Operator' },
                    { key: 'update', label: 'Update' },
                ],
                items: [
                    {
                        start_stock: '23-11-2021 09:24:11',
                        end_stock: '01-12-2021 09:24:11',
                        notes: 'Stok Opname November',
                        status: 'Berjalan',
                        operator: 'Giga, Adm',
                        update: '23-11-2021 09:24:26',
                    },
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
        };
    },
    methods: {
        changeTab(tab) {
            this.active.MainTab = tab;
        },
    },
    mounted() {
        this.ListStockTable.totalRows = this.ListStockTable.items.length
        this.ActivateStockTable.totalRows = this.ActivateStockTable.items.length
    },
    computed: {
        filteredItems() {
            const searchTerm = this.searchTerm.toLowerCase().trim()
            if (!searchTerm) {
                return this.ListStockTable.items
            }
            return this.ListStockTable.items.filter(item => {
                return item.storage_unit.toLowerCase().includes(searchTerm) ||
                    item.notes.toLowerCase().includes(searchTerm) ||
                    item.activate_date.toLowerCase().includes(searchTerm) ||
                    item.activate_operator.toLowerCase().includes(searchTerm) ||
                    item.close_so_date.toLowerCase().includes(searchTerm) ||
                    item.close_so_operator.toLowerCase().includes(searchTerm)
            })
        },
        firstRow() {
            return (this.ListStockTable.currentPage - 1) * this.ListStockTable.perPage + 1
        },
        lastRow() {
            return Math.min(this.ListStockTable.currentPage * this.ListStockTable.perPage, this.ListStockTable.totalRows)
        },

        // activate
        filteredActivateItems() {
            const searchTerm = this.activateSearchTerm.toLowerCase().trim()
            if (!searchTerm) {
                return this.ActivateStockTable.items
            }
            return this.ActivateStockTable.items.filter(item => {
                return item.start_stock.toLowerCase().includes(searchTerm) ||
                    item.end_stock.toLowerCase().includes(searchTerm) ||
                    item.notes.toLowerCase().includes(searchTerm) ||
                    item.status.toLowerCase().includes(searchTerm) ||
                    item.operator.toLowerCase().includes(searchTerm) ||
                    item.update.toLowerCase().includes(searchTerm)
            })
        },
        firstActivateRow() {
            return (this.ActivateStockTable.currentPage - 1) * this.ActivateStockTable.perPage + 1
        },
        lastActivateRow() {
            return Math.min(this.ActivateStockTable.currentPage * this.ActivateStockTable.perPage, this.ActivateStockTable.totalRows)
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
