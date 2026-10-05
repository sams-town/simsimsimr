<template>
    <div>
        <TopLogisticMenu selectedMenu="menu8" />
        <div class="col mt-32">
            <section>
                <div class="label-head">
                    <span class="px-16 text-bold-24 text-neutral-700">Master Data Barang</span>
                </div>
            </section>
            <section class="mt-24">
                <section class="card">
                    <div class="card-header bg-primary-200 font-weight-bold">Tambah Data</div>
                    <div class="card-body d-flex justify-content-center">
                        <div class="col-lg-10">
                            <div class="row">
                                <div class="col-lg-6 px-lg-4">
                                    <div>
                                        <label class="c-form-label">Nama Barang</label>
                                        <b-form-input />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Kelompok Barang</label>
                                        <b-input-group>
                                            <b-form-select />
                                            <b-input-group-append>
                                                <b-button @click="openModal.item_group = !openModal.item_group">
                                                    <feather-icon icon="PlusIcon" />
                                                </b-button>
                                            </b-input-group-append>
                                        </b-input-group>
                                        <b-modal v-model="openModal.item_group" title="Master Kelompok Barang" hide-footer>
                                            <div>
                                                <label class="c-form-label">Kelompok Barang</label>
                                                <b-form-input />
                                            </div>
                                            <div class="d-flex justify-content-end mt-16">
                                                <b-button variant="primary">
                                                    <feather-icon icon="SaveIcon" /> Simpan
                                                </b-button>
                                            </div>
                                        </b-modal>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Pabrik</label>
                                        <b-input-group>
                                            <b-form-select />
                                            <b-input-group-append>
                                                <b-button @click="openModal.factory = !openModal.factory">
                                                    <feather-icon icon="PlusIcon" />
                                                </b-button>
                                            </b-input-group-append>
                                        </b-input-group>
                                        <b-modal v-model="openModal.factory" size="lg" title="MASTER PABRIK" hide-footer
                                            scrollable>
                                            <div class="row mx-0">
                                                <div class="col-lg-6">
                                                    <div>
                                                        <label class="c-form-label">Singkatan</label>
                                                        <b-form-input />
                                                    </div>
                                                    <div class="mt-12">
                                                        <label class="c-form-label">Nama pabrik</label>
                                                        <b-form-input />
                                                    </div>
                                                    <div class="mt-12">
                                                        <label class="c-form-label">Telp / Fax</label>
                                                        <b-form-input placeholder="Telp Pabrik" />
                                                        <b-form-input placeholder="Fax Pabrik" />
                                                    </div>
                                                    <div class="mt-12">
                                                        <label class="c-form-label">NPWP / Email</label>
                                                        <b-form-input placeholder="NPWP Pabrik" />
                                                        <b-form-input placeholder="Email Pabrik" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mt-12 mt-lg-0">
                                                        <label class="c-form-label">Alamat</label>
                                                        <b-form-input placeholder="Alamat:: Jalan-No-RT/RW" />
                                                        <b-form-select />
                                                        <b-form-select />
                                                        <b-form-select />
                                                        <b-form-select />
                                                        <b-form-input placeholder="Kode Pos" />
                                                    </div>
                                                    <div class="mt-12">
                                                        <label class="c-form-label">Nama CP / Telp CP</label>
                                                        <b-form-input placeholder="Nama contact person" />
                                                        <b-form-input placeholder="Telp contact person" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end mt-16">
                                                <b-button variant="primary">
                                                    <feather-icon icon="SaveIcon" /> Simpan
                                                </b-button>
                                            </div>
                                        </b-modal>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Kemasan</label>
                                        <div>
                                            <b-form-select value="1">
                                                <b-form-select-option value="1">0</b-form-select-option>
                                            </b-form-select>
                                            <b-form-select value="1" class="mt-1">
                                                <b-form-select-option value="1">Kem Besar</b-form-select-option>
                                            </b-form-select>
                                            <b-form-select value="1" class="mt-1">
                                                <b-form-select-option value="1">Kem Kecil</b-form-select-option>
                                            </b-form-select>
                                            <b-form-input value="Kemasan Depo" disabled class="mt-1" />
                                            <b-form-select value="1" class="mt-1">
                                                <b-form-select-option value="1">0</b-form-select-option>
                                            </b-form-select>
                                            <b-form-select value="1" class="mt-1">
                                                <b-form-select-option value="1">Kemasan Depo</b-form-select-option>
                                            </b-form-select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 px-lg-4">
                                    <div class="mt-12 mt-lg-0">
                                        <label class="c-form-label">Kode</label>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <b-input-group>
                                                    <b-form-input disabled placeholder="Kode Katalog Generate" />
                                                    <b-input-group-append>
                                                        <b-button variant="neutral-300">
                                                            <feather-icon icon="RefreshCwIcon" />
                                                        </b-button>
                                                        <b-button>
                                                            <feather-icon icon="HelpCircleIcon" />
                                                        </b-button>
                                                    </b-input-group-append>
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-6">
                                                <b-input-group>
                                                    <b-form-select value="1">
                                                        <b-form-select-option value="1">Kode barang</b-form-select-option>
                                                    </b-form-select>
                                                    <b-input-group-append>
                                                        <b-button @click="openModal.catalog_code = !openModal.catalog_code">
                                                            <feather-icon icon="PlusIcon" />
                                                        </b-button>
                                                    </b-input-group-append>
                                                </b-input-group>
                                                <b-modal v-model="openModal.catalog_code" title="MASTER KODE" hide-footer>
                                                    <div>
                                                        <label class="c-form-label">Nama Kode</label>
                                                        <b-form-input />
                                                    </div>
                                                    <div class="d-flex justify-content-end mt-16">
                                                        <b-button variant="primary">
                                                            <feather-icon icon="SaveIcon" /> Simpan
                                                        </b-button>
                                                    </div>
                                                </b-modal>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Min Barang</label>
                                        <b-form-input />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Max Barang</label>
                                        <b-form-input value="100" />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Keterangan</label>
                                        <b-textarea rows="3" />
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-24">
                                <b-button pill variant="outline-primary" class="px-3 mr-2">
                                    <feather-icon icon="XIcon" /> Reset
                                </b-button>
                                <b-button pill variant="primary" class="px-3">Simpan</b-button>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- table here -->
                <div class="mt-32 mt-lg-40">
                    <p class="text-bold-24">List Data</p>
                    <div class="row mx-0 mt-8">
                        <span class="text-regular-14">Tampilkan</span>
                        <select id="per-page-select" v-model="ItemMasterTable.perPage"
                            class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                            <option value="5" selected>5</option>
                            <option value="10" selected>10</option>
                            <option value="15" selected>15</option>
                        </select>
                        <span class="text-regular-14">Entri</span>
                    </div>
                    <b-table bordered responsive small head-variant="light" :fields="ItemMasterTable.field"
                        :items="ItemMasterTable.item" :current-page="ItemMasterTable.currentPage" @filtered="patientFiltered"
                        :per-page="ItemMasterTable.perPage" thead-class="text-bold-14" tbody-tr-class="text-regular-14"
                        class="mt-12 text-center">
                        <template #cell(action)>
                            <div class="row mx-0">
                                <router-link :to="{ name: 'LogisticUDemandPrintView' }" target="_blank">
                                    <b-button size="sm" variant="warning-500" class="mr-1">
                                        <feather-icon icon="Edit2Icon" />
                                    </b-button>
                                </router-link>
                                <b-button @click="openModal.deactive = !openModal.deactive" size="sm" variant="info-500">
                                    <feather-icon icon="CheckIcon" />
                                </b-button>
                                <b-modal v-model="openModal.deactive" title="Deactive Data Katalog">
                                    <span class="text-regular-14">Are you sure to Deactivate Data Surat Pengantar Rawat Inap ?</span>
                                </b-modal>
                            </div>
                        </template>
                    </b-table>
                    <div class="col px-0 mt-20">
                        <div class="d-flex">
                            <span class="text-regular-14">Menampilkan {{ firstRow }} hingga {{ lastRow }} dari {{
                                ItemMasterTable.totalRows }} entri</span>
                            <div class="ml-auto">
                                <b-pagination v-model="ItemMasterTable.currentPage" :total-rows="ItemMasterTable.totalRows"
                                    :per-page="ItemMasterTable.perPage" :current-page="ItemMasterTable.currentPage" align="fill"
                                    size="sm" class="my-0"></b-pagination>
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
            openModal: {
                item_group: false,
                factory: false,
                catalog_code: false,
                deactive: false,
            },
            ItemMasterTable: {
                field: [
                    { key: 'action', label: 'Aksi', thStyle: { minWidth: '100px' } },
                    { key: 'code', label: 'Kode' },
                    { key: 'code_kel', label: 'Kode Kel' },
                    { key: 'preparation_name', label: 'Nama Sediaan' },
                    { key: 'package', label: 'Kemasan' },
                    { key: 'package_depo', label: 'Kemasan Depo' },
                    { key: 'factory', label: 'Pabrik' },
                    { key: 'generic', label: 'Generik' },
                    { key: 'item_group', label: 'Kelompok Barang' },
                    { key: 'user_updt', label: 'User Update' },
                    { key: 'last_updt', label: 'Last Update' },
                ],
                item: [
                    {
                        code: 'PCS0000000001',
                        code_kel: 'PCS',
                        preparation_name: 'Asus A455L_05_07',
                        package: 'Gulung 1 Gulung',
                        package_depo: 'Gulung',
                        factory: '',
                        generic: 'Barang',
                        item_group: 'Barang',
                        user_updt: 'TOTO',
                        last_updt: '2021-10-26 14:03:19',
                    },
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
        }
    },
    mounted() {
        this.ItemMasterTable.totalRows = this.ItemMasterTable.item.length
    },
    computed: {
        firstRow() {
            return (this.ItemMasterTable.currentPage - 1) * this.ItemMasterTable.perPage + 1
        },
        lastRow() {
            return Math.min(this.ItemMasterTable.currentPage * this.ItemMasterTable.perPage, this.ItemMasterTable.totalRows)
        },
    },
    methods: {
        patientFiltered(patientItems) {
            this.ItemMasterTable.totalRows = patientItems.length
            this.ItemMasterTable.currentPage = 1
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