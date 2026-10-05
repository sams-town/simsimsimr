<template>
    <div>
        <TopRatesMenu selectedMenu="menu3" />
        <div class="col mt-32">
            <section class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="label-head">
                    <span class="px-16 text-bold-24 text-neutral-700">Master Data Jenis Tarif</span>
                </div>
                <div class="d-flex flex-wrap">
                    <b-button @click="active.ModalOpen = !active.ModalOpen" pill variant="primary" class="px-3">
                        <feather-icon icon="PlusIcon" class="mr-8" /> Tambah Jenis Tarif
                    </b-button>
                </div>
                <b-modal v-model="active.ModalOpen" title="Tambah Data" hide-footer>
                    <div>
                        <div>
                            <label class="c-form-label">Nama Jenis Tarif</label>
                            <b-form-input />
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Keterangan</label>
                            <b-form-input />
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
                                    <label class="c-form-label">Nama Jenis Tarif</label>
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
                    <select id="per-page-select" v-model="RatesTypesTable.perPage"
                        class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                        <option value="5" selected>5</option>
                        <option value="10" selected>10</option>
                        <option value="15" selected>15</option>
                    </select>
                    <span class="text-regular-14">Entri</span>
                </div>
                <b-table bordered responsive hover small head-variant="light" :fields="RatesTypesTable.field"
                    :items="RatesTypesTable.item" :current-page="RatesTypesTable.currentPage" @filtered="TableFilter"
                    :per-page="RatesTypesTable.perPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12"
                    class="mt-12 text-center">
                    <template #cell(action)>
                        <div class="row mx-0">
                            <b-button @click="active.ModalOpen = true" size="sm" variant="warning-500" class="mr-1">
                                <feather-icon icon="Edit2Icon" />
                            </b-button>
                            <b-button @click="active.delete_modal = true" size="sm" variant="danger-600">
                                <feather-icon icon="TrashIcon" />
                            </b-button>
                            <b-modal v-model="active.delete_modal" title="Delete data jenis tarif" header-bg-variant="danger-500"
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
                            RatesTypesTable.totalRows }} entri</span>
                        <div class="ml-auto">
                            <b-pagination v-model="RatesTypesTable.currentPage" :total-rows="RatesTypesTable.totalRows"
                                :per-page="RatesTypesTable.perPage" :current-page="RatesTypesTable.currentPage" align="fill"
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
            RatesTypesTable: {
                field: [
                    { key: 'type_rates', label: 'Nama Jenis Tarif' },
                    { key: 'notes', label: 'Keterangan' },
                    { key: 'user_added', label: 'User Added' },
                    { key: 'date_added', label: 'Date Added' },
                    { key: 'user_updt', label: 'User Update' },
                    { key: 'last_updt', label: 'Last Update' },
                    { key: 'action', label: 'Action' },
                ],
                item: [
                    {
                        type_rates: 'Administrasi',
                        notes: 'Administrasi',
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
        }
    },
    mounted() {
        this.RatesTypesTable.totalRows = this.RatesTypesTable.item.length
    },
    computed: {
        firstRow() {
            return (this.RatesTypesTable.currentPage - 1) * this.RatesTypesTable.perPage + 1
        },
        lastRow() {
            return Math.min(this.RatesTypesTable.currentPage * this.RatesTypesTable.perPage, this.RatesTypesTable.totalRows)
        },
    },
    methods: {
        TableFilter(Items) {
            this.RatesTypesTable.totalRows = Items.length
            this.RatesTypesTable.currentPage = 1
        },
    },
}

</script>

<style>
.label-head::before {
    content: '';
    border-left: 6px solid #648DE1;
}
</style>