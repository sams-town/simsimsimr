<template>
    <div>
        <TopLinenMenu selectedMenu="menu1" />
        <div class="col mt-32">
            <section class="card">
                <div class="card-header font-weight-bold bg-primary-200">Tambah Data</div>
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div class="col-lg-10">
                            <div class="row">
                                <div class="col-lg-4">
                                    <label class="c-form-label">Nomor</label>
                                    <b-form-input />
                                </div>
                                <div class="col-lg-4 mt-12 mt-lg-0">
                                    <label class="c-form-label">Unit</label>
                                    <b-form-select />
                                </div>
                                <div class="col-lg-4 mt-12 mt-lg-0">
                                    <label class="c-form-label">Keterangan Linen</label>
                                    <b-form-select />
                                </div>
                                <div class="col-lg-4 mt-12">
                                    <label class="c-form-label">Tanggal</label>
                                    <b-form-input type="datetime-local" />
                                </div>
                                <div class="col-lg-4 mt-12">
                                    <label class="c-form-label">Petugas Linen</label>
                                    <b-form-select />
                                </div>
                                <div class="col-lg-4 mt-12">
                                    <label class="c-form-label">Tipe Linen</label>
                                    <b-form-select />
                                </div>
                                <!-- <div class="col-lg-4 mt-12">
                                    <label class="c-form-label">Jam</label>
                                    <b-form-input />
                                </div> -->
                                <div class="col-lg-4 mt-12">
                                    <label class="c-form-label">Petugas Penerima Linen</label>
                                    <b-form-select />
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-24">
                                <b-button pill variant="outline-primary" class="px-3 mr-2">
                                    <feather-icon icon="PlusIcon" class="mr-8" /> Tambah
                                </b-button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- table here -->
            <div class="mt-32">
                <div class="row mx-0 mt-8">
                    <span class="text-regular-14">Tampilkan</span>
                    <select id="per-page-select" v-model="LinenTable.perPage"
                        class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                        <option value="5" selected>5</option>
                        <option value="10" selected>10</option>
                        <option value="15" selected>15</option>
                    </select>
                    <span class="text-regular-14">Entri</span>
                </div>
                <b-table bordered responsive hover small head-variant="light" :fields="LinenTable.field"
                    :items="LinenTable.item" :current-page="LinenTable.currentPage" @filtered="LinenFiltered"
                    :per-page="LinenTable.perPage" thead-class="text-bold-14" tbody-tr-class="text-regular-14"
                    class="mt-12 text-center">
                    <template #cell(action)>
                        <div class="row mx-0">
                            <b-button size="sm" variant="neutral-200">
                                <feather-icon icon="PrinterIcon" /> Initial
                            </b-button>
                        </div>
                    </template>
                </b-table>
                <div class="col px-0 mt-20">
                    <div class="d-flex">
                        <span class="text-regular-14">Menampilkan {{ firstRow }} hingga {{ lastRow }} dari {{
                            LinenTable.totalRows }} entri</span>
                        <div class="ml-auto">
                            <b-pagination v-model="LinenTable.currentPage" :total-rows="LinenTable.totalRows"
                                :per-page="LinenTable.perPage" :current-page="LinenTable.currentPage" align="fill"
                                size="sm" class="my-0"></b-pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import TopLinenMenu from '@/@core/components/top-menu/TopLinenMenu.vue';

export default {
    name: 'PatientPayment',
    components: {
        TopLinenMenu
    },
    data() {
        return {
            LinenTable: {
                field: [
                    { key: 'number', label: 'Nomor' },
                    { key: 'item_code', label: 'Kode Item' },
                    { key: 'linen_type', label: 'Jenis Linen' },
                    { key: 'total', label: 'Jumlah' },
                    { key: 'action', label: 'Action', },
                ],
                item: [
                    {
                        number: '-',
                        item_code: '-',
                        linen_type: '-',
                        total: '-',
                    },
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
        }
    },
    mounted() {
        this.LinenTable.totalRows = this.LinenTable.item.length
    },
    computed: {
        firstRow() {
            return (this.LinenTable.currentPage - 1) * this.LinenTable.perPage + 1
        },
        lastRow() {
            return Math.min(this.LinenTable.currentPage * this.LinenTable.perPage, this.LinenTable.totalRows)
        },
    },
    methods: {
        LinenFiltered(items) {
            this.LinenTable.totalRows = items.length
            this.LinenTable.currentPage = 1
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