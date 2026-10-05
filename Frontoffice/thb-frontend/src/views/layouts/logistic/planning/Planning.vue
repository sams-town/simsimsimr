<template>
    <div>
        <TopLogisticMenu selectedMenu="menu1" />
        <div class="col mt-32">
            <section class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="label-head">
                    <span class="px-16 text-bold-24 text-neutral-700">Transaksi Perencanaan Logistik</span>
                </div>
                <div class="d-flex flex-wrap">
                    <b-button @click="active.ModalOpen = !active.ModalOpen" pill variant="primary" class="px-3">
                        <feather-icon icon="PlusIcon" class="mr-8" /> Data Perencanaan
                    </b-button>
                </div>
            </section>
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
                                        <label class="c-form-label">Status Perencanaan</label>
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
                        <select id="per-page-select" v-model="PlanningTable.perPage"
                            class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                            <option value="5" selected>5</option>
                            <option value="10" selected>10</option>
                            <option value="15" selected>15</option>
                        </select>
                        <span class="text-regular-14">Entri</span>
                    </div>
                    <b-table bordered responsive hover small head-variant="light" :fields="PlanningTable.field"
                        :items="PlanningTable.item" :current-page="PlanningTable.currentPage" @filtered="patientFiltered"
                        :per-page="PlanningTable.perPage" thead-class="text-bold-14" tbody-tr-class="text-regular-14"
                        class="mt-12 text-center">
                        <template #cell(status)>
                            <div class="row mx-0">
                                <b-badge size="sm" variant="success-500" class="mr-1 p-1 font-weight-normal">Perencanaan
                                    gudang Umum</b-badge>
                                <b-badge size="sm" variant="success-500" class="mr-1 p-1 font-weight-normal">Manajer
                                    Umum</b-badge>
                                <b-badge size="sm" variant="success-500" class="mr-1 p-1 font-weight-normal">Direktur
                                    Keuangan</b-badge>
                                <b-badge size="sm" variant="success-500" class="mr-1 p-1 font-weight-normal">Direktur
                                    Utama</b-badge>
                            </div>
                        </template>
                        <template #cell(action)>
                            <div class="row mx-0">
                                <b-button size="sm" variant="info-700" class="mr-1">
                                    <feather-icon icon="EyeIcon" /> View
                                </b-button>
                                <router-link :to="{ name: 'LogisticPlanningPrintView'}" target="_blank">
                                    <b-button size="sm" variant="neutral-200" class="mr-1">
                                        <feather-icon icon="PrinterIcon" /> Print
                                    </b-button>
                                </router-link>
                                <b-button size="sm" variant="neutral-200">
                                    <feather-icon icon="FileTextIcon" /> Excel
                                </b-button>
                            </div>
                        </template>
                    </b-table>
                    <div class="col px-0 mt-20">
                        <div class="d-flex">
                            <span class="text-regular-14">Menampilkan {{ firstRow }} hingga {{ lastRow }} dari {{
                                PlanningTable.totalRows }} entri</span>
                            <div class="ml-auto">
                                <b-pagination v-model="PlanningTable.currentPage" :total-rows="PlanningTable.totalRows"
                                    :per-page="PlanningTable.perPage" :current-page="PlanningTable.currentPage" align="fill"
                                    size="sm" class="my-0"></b-pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <b-modal scrollable v-model="active.ModalOpen" title="Perencanaan Logistik " size="xl" hide-footer>
                <div class="card">
                    <div class="card-header bg-primary-200 font-weight-bold">Data Perencanaan</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <div class="col-lg-10">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div>
                                            <label class="c-form-label">Nomor Dokumen</label>
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <b-form-input disabled />
                                                </div>
                                                <div class="col-lg">
                                                    <b-form-select />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Tanggal</label>
                                            <b-form-datepicker />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mt-12 mt-lg-0">
                                            <label class="c-form-label">Jenis Permintaan</label>
                                            <b-form-select />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Tanggal</label>
                                            <b-form-radio-group>
                                                <b-form-radio>Gudang</b-form-radio>
                                                <b-form-radio>Gudang Umum</b-form-radio>
                                            </b-form-radio-group>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-24">
                    <div class="card-header bg-primary-200 font-weight-bold">Total : Rp. 0,00</div>
                    <div class="card-body">
                        <b-table-simple bordered responsive small>
                            <b-thead class="text-center text-bold-12">
                                <b-tr>
                                    <b-th><feather-icon icon="TrashIcon" /></b-th>
                                    <b-th rowspan="2">No</b-th>
                                    <b-th rowspan="2">Kode</b-th>
                                    <b-th rowspan="2" style="min-width:200px;">Nama Barang</b-th>
                                    <b-th rowspan="2" style="min-width:100px;">Stok Akhir</b-th>
                                    <b-th colspan="4">Pemakaian</b-th>
                                    <b-th rowspan="2" style="min-width:100px;">Kemasan</b-th>
                                    <b-th colspan="2">Jumlah</b-th>
                                    <b-th colspan="2">Jumlah</b-th>
                                    <b-th colspan="2">Harga</b-th>
                                    <b-th style="min-width:100px;">Diskon</b-th>
                                    <b-th colspan="3">Total</b-th>
                                </b-tr>
                                <b-tr>
                                    <b-th><b-form-checkbox /></b-th>
                                    <b-th style="min-width:100px;">Per 3 Bulan</b-th>
                                    <b-th style="min-width:100px;">Per 2 Bulan</b-th>
                                    <b-th style="min-width:100px;">Per 1 Bulan</b-th>
                                    <b-th style="min-width:100px;">Per 2 Minggu</b-th>
                                    <b-th style="min-width:100px;">Order</b-th>
                                    <b-th style="min-width:100px;">Opprove</b-th>
                                    <b-th style="min-width:100px;">Kemasan</b-th>
                                    <b-th style="min-width:100px;">Satuan</b-th>
                                    <b-th style="min-width:100px;">Kemasan</b-th>
                                    <b-th style="min-width:100px;">Satuan</b-th>
                                    <b-th style="min-width:100px;">Sebelum Disc</b-th>
                                    <b-th style="min-width:100px;">Disc</b-th>
                                    <b-th style="min-width:100px;">Rp</b-th>
                                </b-tr>
                            </b-thead>
                            <b-tbody class="text-regular-12">
                                <!-- loop this line -->
                                <b-tr>
                                    <b-td></b-td>
                                    <b-td>1</b-td>
                                    <b-td></b-td>
                                    <b-td>Stiker AsturoWarna Biru <b-badge variant="warning-500" class="p-1 font-weight-normal">Stock</b-badge></b-td>
                                    <b-td></b-td>
                                    <b-td></b-td>
                                    <b-td></b-td>
                                    <b-td></b-td>
                                    <b-td></b-td>
                                    <b-td><b-form-select size="sm" /></b-td>
                                    <b-td><b-form-input size="sm" value="1" /></b-td>
                                    <b-td><b-form-input size="sm" value="1" /></b-td>
                                    <b-td><b-form-input size="sm" value="0 Box" /></b-td>
                                    <b-td><b-form-input size="sm" value="0,00" /></b-td>
                                    <b-td><b-form-input size="sm" value="0,00" /></b-td>
                                    <b-td><b-form-input size="sm" value="0,00" /></b-td>
                                    <b-td><b-form-input size="sm" value="0,00" /></b-td>
                                    <b-td><b-form-input size="sm" value="0,00" /></b-td>
                                    <b-td><b-form-input size="sm" value="0,00" /></b-td>
                                </b-tr>
                                <b-tr>
                                    <b-td></b-td>
                                    <b-td>2</b-td>
                                    <b-td></b-td>
                                    <b-td><b-form-select size="sm" /></b-td>
                                    <b-td></b-td>
                                    <b-td></b-td>
                                    <b-td></b-td>
                                    <b-td></b-td>
                                    <b-td></b-td>
                                    <b-td><b-form-select size="sm" /></b-td>
                                    <b-td><b-form-input size="sm" value="1" /></b-td>
                                    <b-td><b-form-input size="sm" value="1" /></b-td>
                                    <b-td><b-form-input size="sm" value="0 Box" /></b-td>
                                    <b-td><b-form-input size="sm" value="0,00" /></b-td>
                                    <b-td><b-form-input size="sm" value="0,00" /></b-td>
                                    <b-td><b-form-input size="sm" value="0,00" /></b-td>
                                    <b-td><b-form-input size="sm" value="0,00" /></b-td>
                                    <b-td><b-form-input size="sm" value="0,00" /></b-td>
                                    <b-td><b-form-input size="sm" value="0,00" /></b-td>
                                </b-tr>
                                <b-tr>
                                    <b-th colspan="16" class="text-right">Total</b-th>
                                    <b-td><b-form-input size="sm" value="0,00" /></b-td>
                                    <b-td><b-form-input size="sm" value="0,00" /></b-td>
                                    <b-td><b-form-input size="sm" value="0,00" /></b-td>
                                </b-tr>
                                <b-tr>
                                    <b-th colspan="16" class="text-right">Nilai</b-th>
                                    <b-td colspan="3"><b-form-input size="sm" value="0,00" /></b-td>
                                </b-tr>
                                <b-tr>
                                    <b-th colspan="16" class="text-right">Pembulatan</b-th>
                                    <b-td colspan="3">
                                        <b-input-group>
                                            <b-input-group-prepend is-text>
                                                <b-form-checkbox class="mr-n2">
                                                    <span class="sr-only">Checked</span>
                                                </b-form-checkbox>
                                            </b-input-group-prepend>
                                            <b-form-input value="0,00" />
                                        </b-input-group>
                                    </b-td>
                                </b-tr>
                                <b-tr>
                                    <b-th colspan="16" class="text-right">Akhri</b-th>
                                    <b-td colspan="3"><b-form-input size="sm" value="0,00" /></b-td>
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
                        <b-table bordered responsive hover small head-variant="light" :fields="PlanningModalTable.field"
                            :items="PlanningModalTable.item" thead-class="text-bold-14" tbody-tr-class="text-regular-14"
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
            PlanningTable: {
                field: [
                    { key: 'plan_number', label: 'No.Perencanaan' },
                    { key: 'date', label: 'Tanggal' },
                    { key: 'values', label: 'Nilai' },
                    { key: 'user_updt', label: 'User Update' },
                    { key: 'last_updt', label: 'Last Update' },
                    { key: 'status', label: 'Status' },
                    { key: 'action', label: 'Action' },
                ],
                item: [
                    {
                        plan_number: '20220929090001',
                        date: '29/09/2022',
                        values: '156.000,00',
                        user_updt: 'Yunandar',
                        last_updt: '29/09/2022 16:13:29',
                    },
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
            PlanningModalTable: {
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
        this.PlanningTable.totalRows = this.PlanningTable.item.length
    },
    computed: {
        firstRow() {
            return (this.PlanningTable.currentPage - 1) * this.PlanningTable.perPage + 1
        },
        lastRow() {
            return Math.min(this.PlanningTable.currentPage * this.PlanningTable.perPage, this.PlanningTable.totalRows)
        },
    },
    methods: {
        // goToTab(tab) {
        //     this.active.tab = tab;
        // },
        patientFiltered(patientItems) {
            this.PlanningTable.totalRows = patientItems.length
            this.PlanningTable.currentPage = 1
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