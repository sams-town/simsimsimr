<template>
    <div>
        <TopPhysiotherapyMenu selectedMenu="menu2" />
        <b-modal v-model="active.modal_rm" size="xl" scrollable title="Cari Pasien" hide-footer>
            <section class="card">
                <div class="card-header">Pencarian pasien</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="c-form-label">No RM</label>
                            <b-form-input />
                        </div>
                        <div class="col-lg-6 mt-12 mt-lg-0">
                            <label class="c-form-label">Nama Pasien</label>
                            <b-form-input />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mt-12">
                            <label class="c-form-label">No Identitas</label>
                            <b-form-input />
                        </div>
                        <div class="col-lg-6 mt-12">
                            <label class="c-form-label">Tempat, Tanggal Lahir</label>
                            <div class="row">
                                <div class="col-lg-6">
                                    <b-form-input />
                                </div>
                                <div class="col-lg-6 mt-2 mt-lg-0">
                                    <b-form-input type="date" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Alamat</label>
                        <div class="row">
                            <div class="col-lg-8">
                                <b-form-input placeholder="Alamat" />
                            </div>
                            <div class="col-lg mt-2 mt-lg-0">
                                <b-form-input placeholder="RT" />
                            </div>
                            <div class="col-lg mt-2 mt-lg-0">
                                <b-form-input placeholder="RW" />
                            </div>
                        </div>
                        <div class="mt-2">
                            <b-form-select value="0">
                                <b-form-select-option value="0">Kelurahan</b-form-select-option>
                            </b-form-select>
                        </div>
                        <div class="mt-2">
                            <b-form-input placeholder="Kecamatan" />
                        </div>
                        <div class="mt-2">
                            <b-form-input placeholder="Kodya" />
                        </div>
                        <div class="mt-2">
                            <b-form-input placeholder="Propinsi" />
                        </div>
                        <div class="mt-2">
                            <b-form-input placeholder="Kode Pos" />
                        </div>
                    </div>
                    <div class="mt-24">
                        <b-button variant="outline-primary">
                            <feather-icon icon="XIcon" /> Clear Filter
                        </b-button>
                    </div>
                </div>
            </section>
            <section class="card mt-24">
                <div class="card-body">
                    <div class="row mx-0 mt-8">
                        <span class="text-regular-14">Tampilkan</span>
                        <select id="per-page-select" v-model="SearchRMTable.perPage"
                            class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                            <option value="5" selected>5</option>
                            <option value="10" selected>10</option>
                            <option value="15" selected>15</option>
                        </select>
                        <span class="text-regular-14">Entri</span>
                    </div>
                    <b-table bordered responsive hover small head-variant="light" :fields="SearchRMTable.field"
                        :items="SearchRMTable.item" :current-page="SearchRMTable.currentPage" @filtered="TableRMFilter"
                        :per-page="SearchRMTable.perPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12"
                        class="mt-12 text-center">
                        <template #cell(action)>
                            <b-button size="sm" variant="info-600">Ambil No RM</b-button>
                        </template>
                    </b-table>
                    <div class="col px-0 mt-20">
                        <div class="d-flex">
                            <span class="text-regular-14">Menampilkan {{ firstRMRow }} hingga {{ lastRMRow }} dari {{
                                SearchRMTable.totalRows }} entri</span>
                            <div class="ml-auto">
                                <b-pagination v-model="SearchRMTable.currentPage" :total-rows="SearchRMTable.totalRows"
                                    :per-page="SearchRMTable.perPage" :current-page="SearchRMTable.currentPage" align="fill"
                                    size="sm" class="my-0"></b-pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </b-modal>
        <div class="mt-32">
            <section class="card">
                <div class="card-header">Filter Data</div>
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div class="col-lg-10">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div>
                                        <label class="c-form-label">No Pendaftaran</label>
                                        <b-form-input />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">No RM</label>
                                        <b-input-group>
                                            <b-form-input></b-form-input>
                                            <b-input-group-append>
                                                <b-button @click="active.modal_rm = true"><feather-icon
                                                        icon="SearchIcon" /></b-button>
                                            </b-input-group-append>
                                        </b-input-group>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Jenis Pasien</label>
                                        <b-form-select />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Kelamin</label>
                                        <b-form-select />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Instalasi</label>
                                        <b-form-select />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mt-12 mt-lg-0">
                                        <label class="c-form-label">Instalasi Sub</label>
                                        <b-form-select />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Dokter</label>
                                        <b-form-select />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Cara Masuk</label>
                                        <b-form-select />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Cara Bayar</label>
                                        <b-form-select />
                                    </div>
                                    <div class="row mt-12">
                                        <div class="col-lg-6">
                                            <label class="c-form-label">Tanggal Pendaftaran</label>
                                            <b-form-input type="date" />
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="c-form-label">S.D</label>
                                            <b-form-input type="date" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-lg-end mt-24">
                                <b-button variant="outline-primary">
                                    <feather-icon icon="XIcon" /> Clear Filter
                                </b-button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- table here -->
            <section class="mt-32 mt-lg-40">
                <p class="text-bold-24">List Data</p>
                <div class="row mx-0 mt-8">
                    <span class="text-regular-14">Tampilkan</span>
                    <select id="per-page-select" v-model="CheckupReqTable.perPage"
                        class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                        <option value="5" selected>5</option>
                        <option value="10" selected>10</option>
                        <option value="15" selected>15</option>
                    </select>
                    <span class="text-regular-14">Entri</span>
                </div>
                <b-table bordered responsive hover small head-variant="light" :fields="CheckupReqTable.field"
                    :items="CheckupReqTable.item" :current-page="CheckupReqTable.currentPage" @filtered="TableFilter"
                    :per-page="CheckupReqTable.perPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12"
                    class="mt-12 text-center">
                    <template #cell(action)="row">
                        <b-button size="sm" @click="row.toggleDetails" variant="success-500" class="mr-2"><b-icon
                                :icon="row.detailsShowing ? 'eye-fill' : 'eye-slash-fill'" /></b-button>
                    </template>
                    <template #row-details="row">
                        <b-card>
                            <div class="d-flex justify-content-lg-center">
                                <div class="w-100">
                                    <b-table-simple class="text-center">
                                        <b-thead class="text-bold-14">
                                            <b-tr>
                                                <b-th>No</b-th>
                                                <b-th>Tindakan</b-th>
                                                <b-th>Harga</b-th>
                                                <b-th>Tanggal
                                                    Request</b-th>
                                                <b-th>
                                                    <router-link :to="{ name: 'PhysiotherapyCheckupRequestProcessView' }">
                                                        <b-button size="sm" variant="info-700">
                                                            <feather-icon icon="FileIcon" /> Process Request
                                                        </b-button>
                                                    </router-link>
                                                </b-th>
                                                <b-th>Status Billing</b-th>
                                            </b-tr>
                                        </b-thead>
                                        <b-tbody class="text-regular-14">
                                            <b-tr>
                                                <b-td>1</b-td>
                                                <b-td>Seri DHF (Hb, L, Ht, Tr)</b-td>
                                                <b-td class="text-right">Rp104,500.00</b-td>
                                                <b-td>05-01-2023 11:18:13</b-td>
                                                <b-td> Order Baru </b-td>
                                                <b-td> Order Baru </b-td>
                                            </b-tr>
                                        </b-tbody>
                                    </b-table-simple>
                                </div>
                            </div>
                            <!-- <b-button size="sm" @click="row.toggleDetails">Hide Details</b-button> -->
                        </b-card>
                    </template>

                </b-table>
                <div class="col px-0 mt-20">
                    <div class="d-flex">
                        <span class="text-regular-14">Menampilkan {{ firstRow }} hingga {{ lastRow }} dari {{
                            CheckupReqTable.totalRows }} entri</span>
                        <div class="ml-auto">
                            <b-pagination v-model="CheckupReqTable.currentPage" :total-rows="CheckupReqTable.totalRows"
                                :per-page="CheckupReqTable.perPage" :current-page="CheckupReqTable.currentPage" align="fill"
                                size="sm" class="my-0"></b-pagination>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</template>
<script>
import TopPhysiotherapyMenu from '@/@core/components/top-menu/TopPhysiotherapyMenu.vue';

export default {
    components: {
        TopPhysiotherapyMenu
    },
    data() {
        return {
            active: {
                modal_rm: false,
            },
            CheckupReqTable: {
                field: [
                    { key: 'registration_code', label: 'Kode Pendaftaran' },
                    { key: 'payment_method', label: 'Cara Bayar' },
                    { key: 'entry_method', label: 'Cara Masuk' },
                    { key: 'rm_number', label: 'No RM' },
                    { key: 'patient_name', label: 'Nama Pasien' },
                    { key: 'patient_type', label: 'Jenis Pasien' },
                    { key: 'birthdate', label: 'Tanggal Lahir' },
                    { key: 'age', label: 'Umur Pasien' },
                    { key: 'gender', label: 'Kelamin' },
                    { key: 'installation', label: 'Instalasi Asal' },
                    { key: 'poli', label: 'Poli / Ruangan Asal' },
                    { key: 'doctor', label: 'Dokter' },
                    { key: 'request_date', label: 'Tanggal Request' },
                    { key: 'action', label: 'Action' },
                ],
                item: [
                    {
                        registration_code: '202306230600001',
                        date: '23-06-2023 10:57:06',
                        payment_method: 'By Invoice',
                        entry_method: 'External',
                        rm_number: '00611006',
                        patient_name: 'WIDYA [Tn]',
                        patient_type: 'Pasien Umum',
                        birthdate: '1989-06-23',
                        age: '34 Tahun 0 Bulan 17 Hari',
                        gender: 'Laki - Laki',
                        installation: 'Instalasi Rawat Inap',
                        poli: 'Magnolia 308',
                        doctor: 'dr. Ahmad Arief, Sp.OG',
                        request_date: '20-06-2023 12:47:15',
                    },
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
            SearchRMTable: {
                field: [
                    { key: 'rm_number', label: 'No RM' },
                    { key: 'name', label: 'Nama' },
                    { key: 'birthdate', label: 'Tanggal Lahir' },
                    { key: 'birthplace', label: 'Tempat Lahir' },
                    { key: 'identity_number', label: 'No KTP' },
                    { key: 'address', label: 'Alamat' },
                    { key: 'last_updt', label: 'Last Update' },
                    { key: 'action', label: 'Action' },
                ],
                item: [
                    {
                        rm_number: '00004048',
                        name: 'ALDRIK EVENCIO NAINGGOLAN',
                        birthdate: '2018-01-31',
                        birthplace: 'JAKARTA',
                        identity_number: '3173083101180009',
                        address: 'JL MERUYA UTARA NO.24, RT: -, RW: -, Kode Pos:',
                        last_updt: '2020-12-19 10:22:22',
                    },
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
        }
    },
    mounted() {
        this.CheckupReqTable.totalRows = this.CheckupReqTable.item.length
        this.SearchRMTable.totalRows = this.SearchRMTable.item.length
    },
    computed: {
        firstRow() {
            return (this.CheckupReqTable.currentPage - 1) * this.CheckupReqTable.perPage + 1
        },
        lastRow() {
            return Math.min(this.CheckupReqTable.currentPage * this.CheckupReqTable.perPage, this.CheckupReqTable.totalRows)
        },

        // Search RM
        firstRMRow() {
            return (this.SearchRMTable.currentPage - 1) * this.SearchRMTable.perPage + 1
        },
        lastRMRow() {
            return Math.min(this.SearchRMTable.currentPage * this.SearchRMTable.perPage, this.SearchRMTable.totalRows)
        },
    },
    methods: {
        TableFilter(Items) {
            this.CheckupReqTable.totalRows = Items.length
            this.CheckupReqTable.currentPage = 1
        },
        TableRMFilter(Items) {
            this.SearchRMTable.totalRows = Items.length
            this.SearchRMTable.currentPage = 1
        },
    },
}
</script>