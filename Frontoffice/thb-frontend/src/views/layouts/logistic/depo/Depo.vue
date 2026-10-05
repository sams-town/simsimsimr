<template>
    <div>
        <TopLogisticMenu selectedMenu="menu9" />
        <div class="col mt-32">
            <section>
                <div class="label-head">
                    <span class="px-16 text-bold-24 text-neutral-700">Master Data Depo</span>
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
                                        <label class="c-form-label">Kode</label>
                                        <b-form-input />
                                        <small class="text-muted">* 2 angka - untuk kode Transaksi</small>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Singkatan</label>
                                        <b-form-input />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Nama Depo</label>
                                        <b-form-input />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Nama Apotik/Ruangan Buffer</label>
                                        <b-form-input />
                                    </div>
                                </div>
                                <div class="col-lg-6 px-lg-4">
                                    <div class="mt-12 mt-lg-0">
                                        <label class="c-form-label">Margin BPJS / Umum</label>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <b-form-input placeholder="BPSJ"></b-form-input>
                                            </div>
                                            <div class="col-lg-6">
                                                <b-form-input placeholder="UMUM"></b-form-input>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Jenis Depo</label>
                                        <b-form-radio-group disabled>
                                            <b-form-radio>Gudang</b-form-radio>
                                            <b-form-radio>Gudang/Buffer</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Update</label>
                                        <b-form-input value="simrs, Adm" disabled />
                                        <b-form-input value="23-06-2023 15:34:16" disabled />
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-24">
                                <b-button pill variant="outline-primary" class="px-3 mr-2">
                                    <feather-icon icon="XIcon" /> Reset
                                </b-button>
                                <b-button pill variant="primary" class="px-3">Tambah</b-button>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- table here -->
                <div class="mt-32 mt-lg-40">
                    <p class="text-bold-24">List Data</p>
                    <div class="row mx-0 mt-8">
                        <span class="text-regular-14">Tampilkan</span>
                        <select id="per-page-select" v-model="DepoTable.perPage"
                            class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                            <option value="5" selected>5</option>
                            <option value="10" selected>10</option>
                            <option value="15" selected>15</option>
                        </select>
                        <span class="text-regular-14">Entri</span>
                    </div>
                    <b-table bordered responsive small head-variant="light" :fields="DepoTable.field"
                        :items="DepoTable.item" :current-page="DepoTable.currentPage" @filtered="patientFiltered"
                        :per-page="DepoTable.perPage" thead-class="text-bold-14" tbody-tr-class="text-regular-14"
                        class="mt-12 text-center">
                        <template #cell(action)>
                            <div class="row mx-0">
                                <router-link :to="{ name: 'LogisticUDemandPrintView' }" target="_blank">
                                    <b-button size="sm" variant="info-500" class="mr-1">Ubah</b-button>
                                </router-link>
                                <b-button @click="openModal.deactive = !openModal.deactive" size="sm" variant="danger-500">Deactivation</b-button>
                                <b-modal v-model="openModal.deactive" title="Deactivate Data Depo">
                                    <span class="text-regular-14">Are you sure to Deactivate Data DEPO RAWAT JALANDEPO RAWAT JALAN ?</span>
                                </b-modal>
                            </div>
                        </template>
                    </b-table>
                    <div class="col px-0 mt-20">
                        <div class="d-flex">
                            <span class="text-regular-14">Menampilkan {{ firstRow }} hingga {{ lastRow }} dari {{
                                DepoTable.totalRows }} entri</span>
                            <div class="ml-auto">
                                <b-pagination v-model="DepoTable.currentPage" :total-rows="DepoTable.totalRows"
                                    :per-page="DepoTable.perPage" :current-page="DepoTable.currentPage" align="fill"
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
                deactive: false,
            },
            DepoTable: {
                field: [
                    { key: 'code', label: 'Kode' },
                    { key: 'abbreviation', label: 'Singkatan' },
                    { key: 'depo_name', label: 'Nama Depo' },
                    { key: 'buffer_name', label: 'Nama Apotik/ Ruangan buffer' },
                    { key: 'bpjs_margin', label: 'Margin BPJS' },
                    { key: 'general_margin', label: 'Margin Umum' },
                    { key: 'user_updt', label: 'User Update' },
                    { key: 'last_updt', label: 'Last Update' },
                    { key: 'action', label: 'Aksi', thStyle: { minWidth: '100px' } },
                ],
                item: [
                    {
                        code: '01',
                        abbreviation: 'DEPO IRJ',
                        depo_name: 'DEPO RAWAT JALAN',
                        buffer_name: 'DEPO RAWAT JALAN',
                        bpjs_margin: '31',
                        general_margin: '30',
                        user_updt: 'Giga, Adm',
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
        this.DepoTable.totalRows = this.DepoTable.item.length
    },
    computed: {
        firstRow() {
            return (this.DepoTable.currentPage - 1) * this.DepoTable.perPage + 1
        },
        lastRow() {
            return Math.min(this.DepoTable.currentPage * this.DepoTable.perPage, this.DepoTable.totalRows)
        },
    },
    methods: {
        patientFiltered(patientItems) {
            this.DepoTable.totalRows = patientItems.length
            this.DepoTable.currentPage = 1
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