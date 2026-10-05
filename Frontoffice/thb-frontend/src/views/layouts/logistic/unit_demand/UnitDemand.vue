<template>
    <div>
        <TopLogisticMenu selectedMenu="menu6" />
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
                                        <label class="c-form-label">No Mutasi</label>
                                        <b-form-input />
                                    </div>
                                    <div class="col-lg-4 mt-12 mt-lg-0">
                                        <label class="c-form-label">Gudang/Buffer</label>
                                        <b-form-select />
                                    </div>
                                    <div class="col-lg-4 mt-12 mt-lg-0">
                                        <label class="c-form-label">Jenis Mutasi</label>
                                        <b-form-select />
                                    </div>
                                    <div class="col-lg-4 mt-12">
                                        <label class="c-form-label">Status Mutasi</label>
                                        <b-form-select />
                                    </div>
                                    <div class="col-lg-4 mt-12">
                                        <label class="c-form-label">Tanggal Mutasi</label>
                                        <b-form-datepicker />
                                    </div>
                                    <div class="col-lg-4 mt-12">
                                        <label class="c-form-label">Sampai Dengan</label>
                                        <b-form-datepicker />
                                    </div>
                                </div>
                                <div class="d-flex mt-24">
                                    <b-button pill variant="outline-primary" class="px-3 ml-auto">
                                        <feather-icon icon="XIcon" class="mr-8" /> Clear Filter
                                    </b-button>
                                    <!-- <b-button pill variant="primary" class="px-3">
                                        <feather-icon icon="FileTextIcon" class="mr-8" /> Export Excel
                                    </b-button> -->
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
                        <select id="per-page-select" v-model="UDemandTable.perPage"
                            class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                            <option value="5" selected>5</option>
                            <option value="10" selected>10</option>
                            <option value="15" selected>15</option>
                        </select>
                        <span class="text-regular-14">Entri</span>
                    </div>
                    <b-table bordered responsive hover small head-variant="light" :fields="UDemandTable.field"
                        :items="UDemandTable.item" :current-page="UDemandTable.currentPage" @filtered="patientFiltered"
                        :per-page="UDemandTable.perPage" thead-class="text-bold-14" tbody-tr-class="text-regular-14"
                        class="mt-12 text-center">
                        <template #cell(action)>
                            <div class="row mx-0">
                                <router-link :to="{ name: 'LogisticUDemandPrintView' }" target="_blank">
                                    <b-button size="sm" variant="neutral-200" class="mr-1">
                                        <feather-icon icon="PrinterIcon" />
                                    </b-button>
                                </router-link>
                                <b-button size="sm" variant="neutral-200">
                                    <feather-icon icon="FileTextIcon" />
                                </b-button>
                            </div>
                        </template>
                        <template #cell(send_verif)="data">
                            <div class="row mx-0">
                                <b-badge v-if="data.value === 'Pending'" variant="danger-500"
                                    class="mr-1 p-1 font-weight-normal">
                                    <feather-icon icon="AlertTriangleIcon" /> {{ data.value }}
                                </b-badge>
                                <b-badge v-else variant="success-500" class="mr-1 p-1 font-weight-normal">
                                    <feather-icon icon="CheckSquareIcon" /> {{ data.value }}
                                </b-badge>
                            </div>
                        </template>
                        <template #cell(sender_verif)="data">
                            <div class="row mx-0">
                                <b-badge v-if="data.value === 'Pending'" variant="danger-500"
                                    class="mr-1 p-1 font-weight-normal">
                                    <feather-icon icon="AlertTriangleIcon" /> {{ data.value }}
                                </b-badge>
                                <b-badge v-else variant="success-500" class="mr-1 p-1 font-weight-normal">
                                    <feather-icon icon="CheckSquareIcon" /> {{ data.value }}
                                </b-badge>
                            </div>
                        </template>
                        <template #cell(transaction_status)="data">
                            <div class="row mx-0">
                                <b-badge v-if="data.value === 'Pending'" variant="warning-500"
                                    class="mr-1 p-1 font-weight-normal">
                                    {{ data.value }}
                                </b-badge>
                                <b-badge v-else-if="data.value === 'Terpenuhi'" variant="success-500"
                                    class="mr-1 p-1 font-weight-normal">
                                    {{ data.value }}
                                </b-badge>
                                <b-badge v-else variant="danger-500" class="mr-1 p-1 font-weight-normal">
                                    {{ data.value }}
                                </b-badge>
                            </div>
                        </template>
                        <template #cell(document_type)="data">
                            <div class="row mx-0">
                                <b-badge variant="info-600" class="mr-1 p-1 font-weight-normal">
                                    {{ data.value }}
                                </b-badge>
                            </div>
                        </template>
                    </b-table>
                    <div class="col px-0 mt-20">
                        <div class="d-flex">
                            <span class="text-regular-14">Menampilkan {{ firstRow }} hingga {{ lastRow }} dari {{
                                UDemandTable.totalRows }} entri</span>
                            <div class="ml-auto">
                                <b-pagination v-model="UDemandTable.currentPage" :total-rows="UDemandTable.totalRows"
                                    :per-page="UDemandTable.perPage" :current-page="UDemandTable.currentPage" align="fill"
                                    size="sm" class="my-0"></b-pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <b-modal scrollable v-model="active.ModalOpen" title="Perencanaan Logistik " size="xl" hide-footer>
                <div class="card">
                    <div class="card-header bg-primary-200 font-weight-bold">Transaksi Mutasi</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <div class="col-lg-10">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div>
                                            <label class="c-form-label">No Transaksi</label>
                                            <b-form-input diabled />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Jenis Transaksi</label>
                                            <b-form-select />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Nomor Dokumen</label>
                                            <div class="row">
                                                <div class="col-lg-7">
                                                    <b-input-group>
                                                        <b-form-input></b-form-input>
                                                        <b-input-group-append>
                                                            <b-button><feather-icon icon="RefreshCwIcon" /></b-button>
                                                        </b-input-group-append>
                                                    </b-input-group>
                                                </div>
                                                <div class="col-lg">
                                                    <b-input-group>
                                                        <b-form-select></b-form-select>
                                                        <b-input-group-append>
                                                            <b-button><feather-icon icon="PlusIcon" /></b-button>
                                                        </b-input-group-append>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Tanggal Mutasi</label>
                                            <b-form-datepicker />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Keterangan</label>
                                            <b-textarea />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mt-12 mt-lg-0">
                                            <label class="c-form-label">Gudang/Buffer Pengirim</label>
                                            <b-form-select />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Gudang/Buffer Tujuan</label>
                                            <b-form-select />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Verif Permintaan Mutasi</label>
                                            <b-input-group>
                                                <b-input-group-prepend is-text>
                                                    <input type="checkbox">
                                                </b-input-group-prepend>
                                                <div class="col px-0">
                                                    <b-form-input />
                                                    <b-form-input />
                                                </div>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Verif Mutasi Diberi</label>
                                            <b-input-group>
                                                <b-input-group-prepend is-text>
                                                    <input type="checkbox">
                                                </b-input-group-prepend>
                                                <div class="col px-0">
                                                    <b-form-input />
                                                    <b-form-input />
                                                </div>
                                            </b-input-group>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-24">
                    <div class="card-header bg-primary-200 font-weight-bold">Jumlah Item Barang :  0</div>
                    <div class="card-body">
                        <b-table-simple bordered responsive small>
                            <b-thead class="text-center text-bold-12">
                                <b-tr>
                                    <b-th>No</b-th>
                                    <b-th>Kode</b-th>
                                    <b-th style="min-width:200px;">Nama Barang</b-th>
                                    <b-th>Stok</b-th>
                                    <b-th>Qty Minta</b-th>
                                    <b-th>Qty Diberi</b-th>
                                    <b-th>Qty Dikirim</b-th>
                                </b-tr>
                            </b-thead>
                            <b-tbody class="text-regular-12">
                                <b-tr>
                                    <b-td>1</b-td>
                                    <b-td></b-td>
                                    <b-td><b-form-select size="sm" /></b-td>
                                    <b-td><b-form-input size="sm" value="0,00" /></b-td>
                                    <b-td><b-form-input size="sm" value="0,00" /></b-td>
                                    <b-td><b-form-input size="sm" value="0,00" /></b-td>
                                    <b-td><b-form-input size="sm" value="0 TAB" /></b-td>
                                </b-tr>
                            </b-tbody>
                        </b-table-simple>
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
            UDemandTable: {
                field: [
                    { key: 'no', label: 'No' },
                    { key: 'action', label: 'Action', thStyle: { minWidth: '100px' } },
                    { key: 'mutation_number', label: 'No Mutasi' },
                    { key: 'date', label: 'Tanggal' },
                    { key: 'sender', label: 'Pengirim' },
                    { key: 'receiver', label: 'Satelit Penerima' },
                    { key: 'send_verif', label: 'Verif Pengiriman' },
                    { key: 'sender_verif', label: 'Verif Penerima' },
                    { key: 'transaction_status', label: 'Status Transaksi' },
                    { key: 'document_type', label: 'Jenis Dokumen' },
                    { key: 'user_updt', label: 'User Update' },
                    { key: 'last_updt', label: 'Last Update' },
                ],
                item: [
                    {
                        no: '1',
                        mutation_number: 'PMT/LGU/11/2021/00032',
                        date: '09-11-2021 14:12:583757',
                        sender: 'GUDANG UMUM',
                        receiver: 'ADMISI',
                        send_verif: 'Pending',
                        sender_verif: 'Pending',
                        transaction_status: 'Expired',
                        document_type: 'Permintaan',
                        user_updt: 'Yunandar',
                        last_updt: '29/09/2022 16:13:29',
                    },
                    {
                        no: '2',
                        mutation_number: 'PMT/LGU/11/2021/00032',
                        date: '09-11-2021 14:12:583757',
                        sender: 'GUDANG UMUM',
                        receiver: 'ADMISI',
                        send_verif: 'Terkirim',
                        sender_verif: 'Diterima',
                        transaction_status: 'Terpenuhi',
                        document_type: 'Permintaan',
                        user_updt: 'Toto',
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
        this.UDemandTable.totalRows = this.UDemandTable.item.length
    },
    computed: {
        firstRow() {
            return (this.UDemandTable.currentPage - 1) * this.UDemandTable.perPage + 1
        },
        lastRow() {
            return Math.min(this.UDemandTable.currentPage * this.UDemandTable.perPage, this.UDemandTable.totalRows)
        },
    },
    methods: {
        // goToTab(tab) {
        //     this.active.tab = tab;
        // },
        patientFiltered(patientItems) {
            this.UDemandTable.totalRows = patientItems.length
            this.UDemandTable.currentPage = 1
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