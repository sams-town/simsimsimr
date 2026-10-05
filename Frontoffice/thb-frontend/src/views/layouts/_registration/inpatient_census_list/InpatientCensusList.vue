<template>
    <div>
        <topMenu selectedMenu="menu4" />
        <section class="card mt-32">
            <div class="card-header font-weight-bold bg-primary-200">Filter Data</div>
            <div class="card-body">
                <div class="d-flex justify-content-center">
                    <b-form class="col-lg-10">
                        <div class="row">
                            <div class="col-lg-6">
                                <div>
                                    <label class="c-form-label">No Pendaftaran</label>
                                    <b-form-input type="number"></b-form-input>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">No RM</label>
                                    <b-form-input type="number"></b-form-input>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Jenis Pasien</label>
                                    <b-form-select />
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Jenis Kelamin</label>
                                    <b-form-select />
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Instalasi</label>
                                    <b-form-select value="0" disabled>
                                        <b-form-select-option value="0">Instalasi Rawat Inap</b-form-select-option>
                                    </b-form-select>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Instalasi Sub</label>
                                    <b-form-input type="text"></b-form-input>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mt-12 mt-lg-0">
                                    <label class="c-form-label">Dokter</label>
                                    <b-form-select></b-form-select>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Kota</label>
                                    <b-form-select></b-form-select>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Status Pasien</label>
                                    <b-form-select></b-form-select>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Cara Pulang</label>
                                    <b-form-select></b-form-select>
                                </div>
                                <div class="row mt-12">
                                    <div class="col-lg-6">
                                        <label class="c-form-label">Tanggal Pendaftaran</label>
                                        <b-form-input type="date"></b-form-input>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="c-form-label">S.d.</label>
                                        <b-form-input type="date"></b-form-input>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-24">
                            <b-button variant="outline-primary">
                                <feather-icon icon="XIcon" /> Clear Filter
                            </b-button>
                        </div>
                    </b-form>
                </div>
            </div>
        </section>
        <section class="mt-32 mt-lg-40">
            <div class="row mx-0">
                <span class="text-regular-14">Tampilkan</span>
                <select id="per-page-select" v-model="CencusListTable.perPage"
                    class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                    <option value="5" selected>5</option>
                    <option value="10" selected>10</option>
                    <option value="15" selected>15</option>
                </select>
                <span class="text-regular-14">Entri</span>
            </div>
            <b-table bordered responsive head-variant="light" :fields="CencusListTable.fields"
                :items="CencusListTable.items" :current-page="CencusListTable.currentPage" @filtered="TableFilter"
                :per-page="CencusListTable.perPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12"
                class="mt-12 text-center">
                <template #thead-top="data">
                    <b-tr>
                        <b-th colspan="6">Data Pendaftaran</b-th>
                        <b-th colspan="6">Data Pasien</b-th>
                        <b-th colspan="6">Data Tujuan</b-th>
                        <b-th></b-th>
                    </b-tr>
                </template>
                
                <template #cell(code)="data">
                    <div class="text-regular-16">
                        <b-badge size="sm" variant="success-500" class="p-2 font-weight-normal">
                            <feather-icon icon="TagIcon" /> {{ data.value }}
                        </b-badge>
                    </div>
                </template>
                
                <template #cell(patient_status)="data">
                    <div class="text-regular-16">
                        <b-badge size="sm" :variant=" data.value === 'Pulang' ? 'success-600' : 'info-600'" class="p-2 font-weight-normal">
                            {{ data.value }}
                        </b-badge>
                    </div>
                </template>
                
                <template #cell(document_status)="data">
                    <div class="text-regular-16">
                        <b-badge size="sm" :variant=" data.value === 'Belum terima dokumen rekam medis' ? 'warning-500' : 'info-600'" class="p-2 font-weight-normal">
                            {{ data.value }}
                        </b-badge>
                    </div>
                </template>
                
                <template #cell(rm_number)="data">
                    <div class="text-regular-16">
                        <b-badge size="sm" variant="info-600" class="p-2 font-weight-normal">
                            {{ data.value }}
                        </b-badge>
                    </div>
                </template>

                <template #cell(patient_type)="data">
                    <div class="text-regular-16">
                        <b-badge size="sm" :variant=" data.value === 'Pasien Baru' ? 'info-600' : 'success-600'" class="p-2 font-weight-normal">
                            {{ data.value }}
                        </b-badge>
                    </div>
                </template>

                <template #cell(action)>
                    <div class="d-flex mx-0">
                        <b-button size="sm" variant="info-600" class="mr-1">
                            <feather-icon icon="FileIcon" />
                        </b-button>
                        <b-button size="sm" variant="success-500" class="mr-1">
                            <feather-icon icon="TagIcon" />
                        </b-button>
                        <b-button size="sm" variant="primary-500">
                            <feather-icon icon="PaperclipIcon" />
                        </b-button>
                    </div>
                </template>
            </b-table>
            <div class="col px-0 mt-20">
                <div class="d-flex">
                    <span class="text-regular-14">Menampilkan {{ firstRow }} hingga {{ lastRow }} dari {{
                        CencusListTable.totalRows }} entri</span>
                    <div class="ml-auto">
                        <b-pagination v-model="CencusListTable.currentPage" :total-rows="CencusListTable.totalRows"
                            :per-page="CencusListTable.perPage" :current-page="CencusListTable.currentPage" align="fill"
                            size="sm" class="my-0"></b-pagination>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
<script>
import topMenu from '@/@core/components/top-menu/topMenu.vue'

export default {
    components: { topMenu, },
    data() {
        return {
            CencusListTable: {
                fields: [
                    { key: 'code', label: 'Kode', thStyle: { minWidth: '140px' } },
                    { key: 'date', label: 'Tanggal', thStyle: { minWidth: '140px' } },
                    { key: 'payment_method', label: 'Cara Bayar', thStyle: { minWidth: '100px' } },
                    { key: 'entry_method', label: 'Cara Masuk', thStyle: { minWidth: '140px' } },
                    { key: 'patient_status', label: 'Status Pasien', thStyle: { minWidth: '140px' } },
                    { key: 'document_status', label: 'Status Dokumen', thStyle: { minWidth: '140px' } },
                    { key: 'rm_number', label: 'No RM' },
                    { key: 'name', label: 'Nama', thStyle: { minWidth: '140px' } },
                    { key: 'patient_type', label: 'Jenis Pasien', thStyle: { minWidth: '120px' } },
                    { key: 'birth_date', label: 'Tanggal Lahir', thStyle: { minWidth: '120px' } },
                    { key: 'age', label: 'Umur', thStyle: { minWidth: '140px' } },
                    { key: 'gender', label: 'Kelamin', thStyle: { minWidth: '140px' } },
                    { key: 'installation', label: 'Instalasi', thStyle: { minWidth: '140px' } },
                    { key: 'poli', label: 'Poli/Ruangan', thStyle: { minWidth: '140px' } },
                    { key: 'doctor', label: 'Dokter', thStyle: { minWidth: '140px' } },
                    { key: 'checkin_date', label: 'Tgl Checkin', thStyle: { minWidth: '140px' } },
                    { key: 'checkout_date', label: 'Tgl Checkout', thStyle: { minWidth: '140px' } },
                    { key: 'length_of_treatment', label: 'Lama Perawatan', thStyle: { minWidth: '140px' } },
                    { key: 'action', label: 'Aksi' },
                ],
                items: [
                    {
                        code: '20490348797002',
                        date: '23-06-2023 07:55:03',
                        payment_method: 'Pribadi',
                        entry_method: 'External (Klinik Pratama Dharma Bakti)',
                        patient_status: 'Dalam penanganan medis',
                        document_status: 'Belum terima dokumen rekam medis',
                        rm_number: '00611007',
                        name: 'Lala [Nn]',
                        patient_type: 'Pasien Baru',
                        birth_date: '1990-07-21',
                        age: '32 Tahun 11 Bulan 23 Hari	',
                        gender: ' Perempuan',
                        installation: 'Instalasi Rawat Inap',
                        poli: 'Ruangan : Lotus 237 BED : 1',
                        doctor: 'dr. Siti Fauziah',
                        checkin_date: '23-06-2023 14:55:03',
                        checkout_date: '27-06-2023 09:56:19',
                        length_of_treatment: '3 Hari',
                    }
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            }
        }
    },
    mounted() {
        this.CencusListTable.totalRows = this.CencusListTable.items.length
    },
    computed: {
        firstRow() {
            return (this.CencusListTable.currentPage - 1) * this.CencusListTable.perPage + 1
        },
        lastRow() {
            return Math.min(this.CencusListTable.currentPage * this.CencusListTable.perPage, this.CencusListTable.totalRows)
        },
    },
    methods: {
        TableFilter(Items) {
            this.CencusListTable.totalRows = Items.length
            this.CencusListTable.currentPage = 1
        },
    },
}
</script>
