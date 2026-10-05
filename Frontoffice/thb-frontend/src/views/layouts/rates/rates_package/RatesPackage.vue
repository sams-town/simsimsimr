<template>
    <div>
        <TopRatesMenu selectedMenu="menu6" />
        <div class="col mt-32">
            <section class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="label-head">
                    <span class="px-16 text-bold-24 text-neutral-700">Master Data Paket</span>
                </div>
                <div class="d-flex flex-wrap">
                    <b-button @click="active.ModalOpen = !active.ModalOpen" pill variant="primary" class="px-3">
                        <feather-icon icon="PlusIcon" class="mr-8" /> Tambah Paket
                    </b-button>
                </div>
                <b-modal v-model="active.ModalOpen" size="lg" title="Mapping Data" hide-footer>
                    <div class="card-body">
                        <div>
                            <label class="c-form-label">Nama Paket</label>
                            <b-form-input />
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Harga</label>
                            <b-form-input />
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Date Start</label>
                            <b-form-input type="date" />
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Date End</label>
                            <b-form-input type="date" />
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Tindakan</label>
                            <b-table-simple small bordered>
                                <b-thead>
                                    <b-tr>
                                        <b-th style="width: 10%;">No</b-th>
                                        <b-th>Jenis Tindakan</b-th>
                                        <b-th>Tindakan</b-th>
                                        <b-th>Qty</b-th>
                                        <b-th style="width: 15%;">
                                            <b-button @click="addRow" size="sm" variant="primary"
                                                class="btn-block"><feather-icon icon="PlusIcon" />Tambah</b-button>
                                        </b-th>
                                    </b-tr>
                                </b-thead>
                                <b-tbody>
                                    <b-tr v-for="(row, index) in tableData" :key="index">
                                        <b-td>{{ index + 1 }}</b-td>
                                        <b-td><b-form-select /></b-td>
                                        <b-td><b-form-select /></b-td>
                                        <b-td><b-form-input /></b-td>
                                        <b-td>
                                            <b-button @click="deleteRow(index)" size="sm" variant="danger-500"><feather-icon
                                                    icon="XIcon" /></b-button>
                                        </b-td>
                                    </b-tr>
                                </b-tbody>
                            </b-table-simple>
                        </div>
                        <div class="d-flex justify-content-lg-end mt-32">
                            <b-button size="sm" variant="outline-primary-500" class="mr-2">
                                <feather-icon icon="XIcon" /> Reset
                            </b-button>
                            <b-button size="sm" variant="primary">
                                <feather-icon icon="SaveIcon" /> Simpan
                            </b-button>
                        </div>
                    </div>
                </b-modal>
            </section>
            <section class="mt-24">
                <div class="card">
                    <div class="card-header font-weight-bold">Filter Data</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <div class="col-lg-5">
                                <div>
                                    <label class="c-form-label">Kode Paket</label>
                                    <b-form-input />
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Nama Paket</label>
                                    <b-form-input />
                                </div>
                                <div class="d-flex justify-content-lg-end mt-24">
                                    <b-button variant="primary"> <feather-icon icon="XIcon" /> Clear Filter</b-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="mt-32 mt-lg-40">
                <p class="text-bold-24">List Data</p>
                <div class="row mx-0 mt-8">
                    <span class="text-regular-14">Tampilkan</span>
                    <select id="per-page-select" v-model="PackageTable.perPage"
                        class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                        <option value="5" selected>5</option>
                        <option value="10" selected>10</option>
                        <option value="15" selected>15</option>
                    </select>
                    <span class="text-regular-14">Entri</span>
                </div>
                <b-table bordered responsive hover small head-variant="light" :fields="PackageTable.field"
                    :items="PackageTable.item" :current-page="PackageTable.currentPage" @filtered="TableFilter"
                    :per-page="PackageTable.perPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12"
                    class="mt-12 text-center">
                    <template #cell(action)>
                        <div class="row mx-0">
                            <b-button @click="active.delete_modal = true" size="sm" variant="danger-600">
                                <feather-icon icon="TrashIcon" />
                            </b-button>
                            <b-modal v-model="active.delete_modal" title="Delete Data Paket" header-bg-variant="danger-500"
                                header-text-variant="neutral-100">
                                Are you sure to Delete Data undefined ?
                                <template #modal-footer>
                                    <div class="d-flex w-100">
                                        <b-button variant="danger-500" class="ml-auto mr-2"
                                            @click="active.delete_modal = false">Save Changes</b-button>
                                        <b-button variant="neutral-200"
                                            @click="active.delete_modal = false">Close</b-button>
                                    </div>
                                </template>
                            </b-modal>
                        </div>
                    </template>
                </b-table>
                <div class="col px-0 mt-20">
                    <div class="d-flex">
                        <span class="text-regular-14">Menampilkan {{ firstRow }} hingga {{ lastRow }} dari {{
                            PackageTable.totalRows }} entri</span>
                        <div class="ml-auto">
                            <b-pagination v-model="PackageTable.currentPage" :total-rows="PackageTable.totalRows"
                                :per-page="PackageTable.perPage" :current-page="PackageTable.currentPage" align="fill"
                                size="sm" class="my-0"></b-pagination>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
import TopRatesMenu from '@/@core/components/top-menu/TopRatesMenu.vue';

export default {
    components: {
        TopRatesMenu
    },
    data() {
        return {
            active: {
                ModalOpen: false,
                delete_modal: false,
            },
            PackageTable: {
                field: [
                    { key: 'package_code', label: 'Kode Paket' },
                    { key: 'package_name', label: 'Nama Paket' },
                    { key: 'price', label: 'Harga' },
                    { key: 'start_periode', label: 'Periode Start' },
                    { key: 'end_periode', label: 'Periode End' },
                    { key: 'action', label: 'Aksi' },
                ],
                item: [
                    {
                        package_code: 'PKT62',
                        package_name: 'Paket Test',
                        price: '1500000.00',
                        start_periode: '2023-03-01 09:39:35',
                        end_periode: '2024-03-28 09:39:37',
                    },
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
            tableData: [
                { action_type: '', action: '', qty: '', },
            ],
            newRow: {
                action_type: '',
                action: '',
                qty: '',
            }
        }
    },
    mounted() {
        this.PackageTable.totalRows = this.PackageTable.item.length
    },
    computed: {
        firstRow() {
            return (this.PackageTable.currentPage - 1) * this.PackageTable.perPage + 1
        },
        lastRow() {
            return Math.min(this.PackageTable.currentPage * this.PackageTable.perPage, this.PackageTable.totalRows)
        },
    },
    methods: {
        TableFilter(Items) {
            this.PackageTable.totalRows = Items.length
            this.PackageTable.currentPage = 1
        },
        addRow() {
            this.tableData.push(this.newRow);
        },
        deleteRow(index) {
            this.tableData.splice(index, 1);
        }
    },
}

</script>

<style>
.label-head::before {
    content: '';
    border-left: 6px solid #648DE1;
}
</style>