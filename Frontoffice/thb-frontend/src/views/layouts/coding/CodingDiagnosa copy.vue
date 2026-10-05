<template>
    <div>
        <div class="col">
            <section>
                <div class="card">
                    <div class="card-header font-weight-bold">Filter Data</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <div class="col-lg-9">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div>
                                            <label class="c-form-label">Unit Kerja</label>
                                            <b-form-select />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Status Klaim</label>
                                            <b-form-select />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Cara Bayar</label>
                                            <b-form-select />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">No Kartu</label>
                                            <b-form-input />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Kode Pendaftaran</label>
                                            <b-form-input />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mt-12 mt-lg-0">
                                            <label class="c-form-label">Nama Pasien</label>
                                            <b-form-input />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">No SEP</label>
                                            <b-form-input />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">No RM</label>
                                            <b-form-input />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">No RM</label>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <b-input-group prepend="Start">
                                                        <b-form-input type="date"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="col-lg-6">
                                                    <b-input-group prepend="End">
                                                        <b-form-input type="date"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Dokter DPJP</label>
                                            <b-form-select />
                                        </div>
                                    </div>
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
                    <select id="per-page-select" v-model="CodingTable.perPage"
                        class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                        <option value="5" selected>5</option>
                        <option value="10" selected>10</option>
                        <option value="15" selected>15</option>
                    </select>
                    <span class="text-regular-14">Entri</span>
                </div>
                <b-table bordered responsive hover small head-variant="light" :fields="CodingTable.field"
                    :items="CodingTable.item" :current-page="CodingTable.currentPage" @filtered="TableFilter"
                    :per-page="CodingTable.perPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12"
                    class="mt-12 text-center">
                    <template #cell(action)="data">
                        <template v-if="data.value == 'result'">
                            <router-link :to="{ name: 'CodingResultView' }" target="_blank">
                                <b-button size="sm" variant="success-600" class="mr-1">
                                    <feather-icon icon="FileIcon" /> Result
                                </b-button>
                            </router-link>
                        </template>
                        <b-button v-else-if="data.value == 'create'" size="sm" variant="info-600" class="mr-1">
                            <feather-icon icon="FileIcon" /> Create
                        </b-button>
                    </template>
                </b-table>
                <div class="col px-0 mt-20">
                    <div class="d-flex">
                        <span class="text-regular-14">Menampilkan {{ firstRow }} hingga {{ lastRow }} dari {{
                            CodingTable.totalRows }} entri</span>
                        <div class="ml-auto">
                            <b-pagination v-model="CodingTable.currentPage" :total-rows="CodingTable.totalRows"
                                :per-page="CodingTable.perPage" :current-page="CodingTable.currentPage" align="fill"
                                size="sm" class="my-0"></b-pagination>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<script>

export default {
    data() {
        return {
            active: {
                ModalOpen: false,
            },
            CodingTable: {
                field: [
                    { key: 'registration_code', label: 'Kode Pendaftaran' },
                    { key: 'card_number', label: 'No Kartu' },
                    { key: 'rm_number', label: 'No RM' },
                    { key: 'sep_number', label: 'No SEP' },
                    { key: 'sep_date', label: 'Tanggal SEP' },
                    { key: 'patient_name', label: 'Nama Pasien' },
                    { key: 'birthdate', label: 'Tanggal Lahir' },
                    { key: 'payment_method', label: 'Cara Bayar' },
                    { key: 'poli', label: 'Poli' },
                    { key: 'dpjp', label: 'DPJP' },
                    { key: 'registration_date', label: 'Tanggal Pendaftaran' },
                    { key: 'action', label: 'Action' },
                ],
                item: [
                    {
                        registration_code: '202306190600004',
                        card_number: '0003502569857',
                        rm_number: '00610992',
                        sep_number: '0301R01687148607',
                        sep_date: '19-06-2023',
                        patient_name: 'BINTANG',
                        birthdate: '1989-11-15',
                        payment_method: 'JASA RAHARJA',
                        poli: 'Instalasi Rawat Jalan',
                        dpjp: 'dr. I Gusti Ayu Ira, Sp.PD',
                        registration_date: '2023-06-19',
                        action: 'result',
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
        this.CodingTable.totalRows = this.CodingTable.item.length
    },
    computed: {
        firstRow() {
            return (this.CodingTable.currentPage - 1) * this.CodingTable.perPage + 1
        },
        lastRow() {
            return Math.min(this.CodingTable.currentPage * this.CodingTable.perPage, this.CodingTable.totalRows)
        },
    },
    methods: {
        TableFilter(Items) {
            this.CodingTable.totalRows = Items.length
            this.CodingTable.currentPage = 1
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