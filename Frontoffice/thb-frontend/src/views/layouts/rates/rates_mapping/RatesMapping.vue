<template>
    <div>
        <TopRatesMenu selectedMenu="menu2" />
        <div class="col mt-32">
            <section class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="label-head">
                    <span class="px-16 text-bold-24 text-neutral-700">Mapping Tarif Perusahaan</span>
                </div>
                <div class="d-flex flex-wrap">
                    <b-button @click="active.ModalOpen = !active.ModalOpen" pill variant="primary" class="px-3">
                        <feather-icon icon="PlusIcon" class="mr-8" /> Tambah Mapping
                    </b-button>
                </div>
                <b-modal v-model="active.ModalOpen" size="lg" title="Mapping Data" hide-footer>
                    <div class="card-body">
                        <div>
                            <label class="c-form-label">Perusahaan</label>
                            <b-input-group>
                                <b-form-select></b-form-select>
                                <b-input-group-append>
                                    <b-button @click="active.add_company = true"><feather-icon icon="PlusIcon" /></b-button>
                                </b-input-group-append>
                            </b-input-group>
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Tarif</label>
                            <b-table-simple small bordered>
                                <b-thead>
                                    <b-tr>
                                        <b-th style="width: 10%;">No</b-th>
                                        <b-th>Tarif</b-th>
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
                <b-modal v-model="active.add_company" title="Master Perusahaan" header-bg-variant="primary-500"
                    header-text-variant="neutral-100" hide-footer>
                    <div class="card">
                        <div class="card-header font-weight-bold">Tambah Data</div>
                        <div class="card-body">
                            <div>
                                <label class="c-form-label">Nama Perusahaan</label>
                                <b-form-input></b-form-input>
                            </div>
                            <div class="d-flex justify-content-lg-end mt-24">
                                <b-button size="sm" variant="outline-primary-500" class="mr-2">
                                    <feather-icon icon="XIcon" /> Reset
                                </b-button>
                                <b-button size="sm" variant="primary">
                                    <feather-icon icon="SaveIcon" /> Simpan
                                </b-button>
                            </div>
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
                                    <label class="c-form-label">Nama Perusahaan</label>
                                    <b-form-select />
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
                    <select id="per-page-select" v-model="MappingTable.perPage"
                        class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                        <option value="5" selected>5</option>
                        <option value="10" selected>10</option>
                        <option value="15" selected>15</option>
                    </select>
                    <span class="text-regular-14">Entri</span>
                </div>
                <b-table bordered responsive hover small head-variant="light" :fields="MappingTable.field"
                    :items="MappingTable.item" :current-page="MappingTable.currentPage" @filtered="TableFilter"
                    :per-page="MappingTable.perPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12"
                    class="mt-12 text-center">
                    <template #cell(action)>
                        <div class="row mx-0">
                            <b-button @click="active.ModalOpen = true" size="sm" variant="warning-500" class="mr-1">
                                <feather-icon icon="Edit2Icon" />
                            </b-button>
                            <b-button @click="active.delete_modal = true" size="sm" variant="danger-600">
                                <feather-icon icon="TrashIcon" />
                            </b-button>
                            <b-modal v-model="active.delete_modal" title="Delete Data Tarif" header-bg-variant="danger-500"
                                header-text-variant="neutral-100">
                                Are you sure to Delete Data 184
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
                            MappingTable.totalRows }} entri</span>
                        <div class="ml-auto">
                            <b-pagination v-model="MappingTable.currentPage" :total-rows="MappingTable.totalRows"
                                :per-page="MappingTable.perPage" :current-page="MappingTable.currentPage" align="fill"
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
                add_company: false,
            },
            MappingTable: {
                field: [
                    { key: 'company_name', label: 'Nama Perusahaan' },
                    { key: 'rates', label: 'Tarif' },
                    { key: 'user_added', label: 'User Added' },
                    { key: 'date_added', label: 'Date Added' },
                    { key: 'user_updt', label: 'User Update' },
                    { key: 'last_updt', label: 'Last Update' },
                    { key: 'action', label: 'Action' },
                ],
                item: [
                    {
                        company_name: 'PT. Fuse Usaha Nasional Digital',
                        rates: 'SWAB AG (SP.140)',
                        user_added: 'Giga, Adm',
                        date_added: '2021-06-30 10:42:06',
                        user_updt: 'Albert',
                        last_updt: '2021-06-28 08:44:01',
                    },
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
            tableData: [
                { rates: '', },
            ],
            newRow: {
                rates: '',
            }
        }
    },
    mounted() {
        this.MappingTable.totalRows = this.MappingTable.item.length
    },
    computed: {
        firstRow() {
            return (this.MappingTable.currentPage - 1) * this.MappingTable.perPage + 1
        },
        lastRow() {
            return Math.min(this.MappingTable.currentPage * this.MappingTable.perPage, this.MappingTable.totalRows)
        },
    },
    methods: {
        TableFilter(Items) {
            this.MappingTable.totalRows = Items.length
            this.MappingTable.currentPage = 1
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