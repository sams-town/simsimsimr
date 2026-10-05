<template>
    <div>
        <TopRatesMenu selectedMenu="menu5" />
        <div class="col mt-32">
            <section class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="label-head">
                    <span class="px-16 text-bold-24 text-neutral-700">Master Promo</span>
                </div>
                <div class="d-flex flex-wrap">
                    <b-button @click="active.ModalOpen = !active.ModalOpen" pill variant="primary" class="px-3">
                        <feather-icon icon="PlusIcon" class="mr-8" /> Tambah Promo
                    </b-button>
                </div>
                <b-modal v-model="active.ModalOpen" size="lg" title="Tambah Data" hide-footer>
                    <div class="row">
                        <div class="col-lg-6">
                            <div>
                                <label class="c-form-label">Nama Promo</label>
                                <b-form-input />
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Keterangan Promo</label>
                                <b-form-input />
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Jenis Promo</label>
                                <b-form-select />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mt-12 mt-lg-0">
                                <label class="c-form-label">Promo Mulai</label>
                                <b-form-input type="date" />
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Promo Berakhir</label>
                                <b-form-input type="date" />
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Type Potongan</label>
                                <b-form-select />
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Total Promo</label>
                                <b-input-group append="%">
                                    <b-form-input></b-form-input>
                                </b-input-group>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-lg-end mt-32">
                        <b-button size="sm" variant="outline-primary-500" class="mr-2">
                            <feather-icon icon="XIcon" /> Reset
                        </b-button>
                        <b-button size="sm" variant="primary">
                            <feather-icon icon="SaveIcon" /> Simpan
                        </b-button>
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
                                    <label class="c-form-label">Nama Promo</label>
                                    <b-form-input />
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Keterangan Promo</label>
                                    <b-form-input />
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Promo Mulai</label>
                                    <b-form-input type="date" />
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
                    <select id="per-page-select" v-model="RatesPromoTable.perPage"
                        class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                        <option value="5" selected>5</option>
                        <option value="10" selected>10</option>
                        <option value="15" selected>15</option>
                    </select>
                    <span class="text-regular-14">Entri</span>
                </div>
                <b-table bordered responsive hover small head-variant="light" :fields="RatesPromoTable.field"
                    :items="RatesPromoTable.item" :current-page="RatesPromoTable.currentPage" @filtered="TableFilter"
                    :per-page="RatesPromoTable.perPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12"
                    class="mt-12 text-center">
                    <template #cell(action)>
                        <div class="d-flex flex-wrap">
                            <b-button @click="active.delete_modal = true" size="sm" variant="danger-600">
                                <feather-icon icon="TrashIcon" />
                            </b-button>
                            <router-link :to="{ name: 'RatesPromoDetailView' }">
                                <b-button size="sm" variant="success-400">
                                    <feather-icon icon="EyeIcon" />
                                </b-button>
                            </router-link>
                            <b-modal v-model="active.delete_modal" title="Delete data promo"
                                header-bg-variant="danger-500" header-text-variant="neutral-100">
                                Are you sure to Delete Data VOUCHER GRATIS ?
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
                            RatesPromoTable.totalRows }} entri</span>
                        <div class="ml-auto">
                            <b-pagination v-model="RatesPromoTable.currentPage" :total-rows="RatesPromoTable.totalRows"
                                :per-page="RatesPromoTable.perPage" :current-page="RatesPromoTable.currentPage" align="fill"
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
            RatesPromoTable: {
                field: [
                    { key: 'promo_code', label: 'Kode Promo' },
                    { key: 'promo_name', label: 'Nama Promo' },
                    { key: 'promo_notes', label: 'Keterangan Promo' },
                    { key: 'promo_start', label: 'Promo Mulai' },
                    { key: 'promo_end', label: 'Promo Berakhir' },
                    { key: 'promo_type', label: 'Type Promo' },
                    { key: 'promo_total', label: 'Total Promo' },
                    { key: 'user_added', label: 'User Added' },
                    { key: 'date_added', label: 'Date Added' },
                    { key: 'user_updt', label: 'User Update' },
                    { key: 'last_updt', label: 'Last Update' },
                    { key: 'action', label: 'Action' },
                ],
                item: [
                    {
                        promo_code: 'VCH2021052000015',
                        promo_name: 'VOUCHER GRATIS',
                        promo_notes: 'KONSULTASI DOKTER SPESIALIS',
                        promo_start: '2021-05-20 00:26:05',
                        promo_end: '2021-05-20 23:26:12',
                        promo_type: 'Vouchers',
                        promo_total: '100.00',
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
        this.RatesPromoTable.totalRows = this.RatesPromoTable.item.length
    },
    computed: {
        firstRow() {
            return (this.RatesPromoTable.currentPage - 1) * this.RatesPromoTable.perPage + 1
        },
        lastRow() {
            return Math.min(this.RatesPromoTable.currentPage * this.RatesPromoTable.perPage, this.RatesPromoTable.totalRows)
        },
    },
    methods: {
        TableFilter(Items) {
            this.RatesPromoTable.totalRows = Items.length
            this.RatesPromoTable.currentPage = 1
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