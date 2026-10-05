<template>
    <div>
        <TopBPJSeMenu selectedMenu="menu2" />
        <div class="col mt-32">
            <div class="card">
                <div class="card-header font-weight-bold bg-primary-200">Filter Data</div>
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div>
                                        <label class="c-form-label">No Kartu</label>
                                        <b-form-input type="number" />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">No RM</label>
                                        <b-form-input type="number" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mt-12 mt-lg-0">
                                        <label class="c-form-label">No SEP</label>
                                        <b-form-select></b-form-select>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Tanggal Pendaftaran</label>
                                        <b-form-input type="date" />
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-lg-end mt-24">
                                <b-button pill variant="outline-primary" class="mr-2"><feather-icon icon="XIcon" /> Clear Filter</b-button>
                                <b-button pill variant="primary">Filter Data</b-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <section class="mt-32 mt-lg-40">
                <div class="row mx-0">
                    <span class="text-regular-14">Tampilkan</span>
                    <select id="per-page-select" v-model="BPJSMemberTable.perPage"
                        class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                        <option value="5" selected>5</option>
                        <option value="10" selected>10</option>
                        <option value="15" selected>15</option>
                    </select>
                    <span class="text-regular-14">Entri</span>
                </div>
                <b-table bordered responsive head-variant="light" :fields="BPJSMemberTable.field"
                    :items="BPJSMemberTable.item" :current-page="BPJSMemberTable.currentPage" @filtered="TableFilter"
                    :per-page="BPJSMemberTable.perPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12"
                    class="mt-12 text-center">
                    <template #cell(action)>
                        <!-- <router-link :to="{ name: 'PatientDetailView' }"> -->
                            <!-- </router-link> -->
                        <div class="row mx-0 text-regular-16 font-weight-normal">
                            <b-badge size="sm" variant="warning-500" class="font-weight-normal pointer p-2 m-1">SPRI</b-badge>
                            <b-badge size="sm" variant="warning-500" class="font-weight-normal pointer p-2 m-1">SPRJ</b-badge>
                            <b-badge size="sm" variant="info-600" class="font-weight-normal pointer p-2 m-1">Rujuk Peserta</b-badge>
                            <b-badge size="sm" variant="info-600" class="font-weight-normal pointer p-2 m-1">Rujukan Khusus</b-badge>
                            <b-badge size="sm" variant="info-600" class="font-weight-normal pointer p-2 m-1">PRB</b-badge>
                            <b-badge size="sm" variant="warning-500" class="font-weight-normal pointer p-2 m-1">Update</b-badge>
                            <router-link :to="{ name: 'BpjsMemberDataUpdateView' }">
                                <b-badge size="sm" variant="info-600" class="font-weight-normal pointer p-2 m-1">View</b-badge>
                            </router-link>
                        </div>
                    </template>
                </b-table>
                <div class="col px-0 mt-20">
                    <div class="d-flex">
                        <span class="text-regular-14">Menampilkan {{ firstRow }} hingga {{ lastRow }} dari {{
                            BPJSMemberTable.totalRows }} entri</span>
                        <div class="ml-auto">
                            <b-pagination v-model="BPJSMemberTable.currentPage" :total-rows="BPJSMemberTable.totalRows"
                                :per-page="BPJSMemberTable.perPage" :current-page="BPJSMemberTable.currentPage" align="fill"
                                size="sm" class="my-0"></b-pagination>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
import TopBPJSeMenu from '@/@core/components/top-menu/TopBPJSMenu.vue';

export default {
    name: 'PatientPayment',
    components: {
        TopBPJSeMenu
    },
    data() {
        return {
            BPJSMemberTable: {
                field: [
                    { key: 'no_rm', label: 'No RM', thStyle: { minWidth: '140px' } },
                    { key: 'registration_code', label: 'Kode Pendaftaran', thStyle: { minWidth: '140px' } },
                    { key: 'sep_number', label: 'No SEP', thStyle: { minWidth: '140px' } },
                    { key: 'sep_date', label: 'Tanggal SEP', thStyle: { minWidth: '140px' } },
                    { key: 'card_number', label: 'No Kartu', thStyle: { minWidth: '140px' } },
                    { key: 'patient_name', label: 'Nama Pasien', thStyle: { minWidth: '140px' } },
                    { key: 'gender', label: 'Jenis Kelamin', thStyle: { minWidth: '140px' } },
                    { key: 'installation', label: 'Instalasi', thStyle: { minWidth: '140px' } },
                    { key: 'poli', label: 'Poli', thStyle: { minWidth: '140px' } },
                    { key: 'action', label: 'Aksi', thStyle: { minWidth: '350px' } },
                ],
                item: [
                    {
                        no_rm: '00610992',
                        registration_code: '202307270700001',
                        sep_number: '0301R01690444513',
                        sep_date: '27-07-2023',
                        card_number: '0003502569857',
                        patient_name: 'BINTANG',
                        gender: 'Perempuan',
                        installation: 'Instalasi Rawat Jalan',
                        poli: 'Poli Penyakit Dalam',
                    },
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
        }
    },
    mounted() {
        this.BPJSMemberTable.totalRows = this.BPJSMemberTable.item.length
    },
    computed: {
        firstRow() {
            return (this.BPJSMemberTable.currentPage - 1) * this.BPJSMemberTable.perPage + 1
        },
        lastRow() {
            return Math.min(this.BPJSMemberTable.currentPage * this.BPJSMemberTable.perPage, this.BPJSMemberTable.totalRows)
        },
    },
    methods: {
        TableFilter(items) {
            this.BPJSMemberTable.totalRows = items.length
            this.BPJSMemberTable.currentPage = 1
        },
    },
}
</script>