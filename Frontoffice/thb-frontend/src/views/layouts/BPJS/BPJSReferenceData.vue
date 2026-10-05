<template>
    <div>
        <TopBPJSeMenu selectedMenu="menu6" />
        <div class="col mt-32">
            <div class="card">
                <div class="card-header font-weight-bold bg-indigo-200">Filter Data</div>
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div class="col-lg-5">
                            <div>
                                <label class="c-form-label">No Rujukan</label>
                                <b-form-input type="number" v-model="filter_form.reference_number" />
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">No SEP</label>
                                <b-form-input type="number" v-model="filter_form.sep_number" />
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Tanggal Rujukan</label>
                                <b-form-input v-model="filter_form.reference_date" />
                            </div>
                            <div class="d-flex justify-content-lg-end mt-40">
                                <div class="col-lg-3 px-0">
                                    <b-button pill class="w-100" variant="primary">Cari</b-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-32 mt-lg-40">
                <div class="row mx-0 mt-8">
                    <span class="text-regular-14">Tampilkan</span>
                    <select id="per-page-select" v-model="BPJSData.perPage"
                        class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                        <option value="5" selected>5</option>
                        <option value="10" selected>10</option>
                        <option value="15" selected>15</option>
                    </select>
                    <span class="text-regular-14">Entri</span>
                </div>
                <b-table bordered responsive hover small head-variant="light" :fields="BPJSData.field"
                    :items="BPJSData.item" :current-page="BPJSData.currentPage" @filtered="patientFiltered"
                    :per-page="BPJSData.perPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                </b-table>
                <div class="col px-0 mt-20">
                    <div class="d-flex">
                        <span class="text-regular-14">Menampilkan {{ firstRow }} hingga {{ lastRow }} dari {{
                            BPJSData.totalRows }} entri</span>
                        <div class="ml-auto">
                            <b-pagination v-model="BPJSData.currentPage" :total-rows="BPJSData.totalRows"
                                :per-page="BPJSData.perPage" :current-page="BPJSData.currentPage" align="fill" size="sm"
                                class="my-0"></b-pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import TopBPJSeMenu from '@/@core/components/top-menu/TopBPJSMenu.vue';

export default {
    components: {
        TopBPJSeMenu
    },
    data() {
        return {
            filter_form: {
                reference_number: '',
                sep_number: '',
                reference_date: '',
            },
            BPJSData: {
                field: [
                    { key: 'reference_number', label: 'No Rujukan', thStyle: { minWidth: '140px' } },
                    { key: 'reference_date', label: 'Tanggal Rujukan', thStyle: { minWidth: '140px' } },
                    { key: 'installation', label: 'Instalasi', thStyle: { minWidth: '140px' } },
                    { key: 'sep_number', label: 'No SEP', thStyle: { minWidth: '140px' } },
                    { key: 'card_number', label: 'No Kartu', thStyle: { minWidth: '140px' } },
                    { key: 'name', label: 'Nama', thStyle: { minWidth: '140px' } },
                    { key: 'reference_ppk', label: 'PPK Rujuk', thStyle: { minWidth: '140px' } },
                ],
                item: [
                    { reference_number: '1', reference_date: '-', installation: '-', sep_number: '-', card_number: '-', name: '-', reference_ppk: '-', },
                    { reference_number: '2', reference_date: '-', installation: '-', sep_number: '-', card_number: '-', name: '-', reference_ppk: '-', },
                    { reference_number: '3', reference_date: '-', installation: '-', sep_number: '-', card_number: '-', name: '-', reference_ppk: '-', },
                    { reference_number: '4', reference_date: '-', installation: '-', sep_number: '-', card_number: '-', name: '-', reference_ppk: '-', },
                    { reference_number: '5', reference_date: '-', installation: '-', sep_number: '-', card_number: '-', name: '-', reference_ppk: '-', },
                    { reference_number: '6', reference_date: '-', installation: '-', sep_number: '-', card_number: '-', name: '-', reference_ppk: '-', },
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },

        }
    },
    mounted() {
        this.BPJSData.totalRows = this.BPJSData.item.length
    },
    computed: {
        firstRow() {
            return (this.BPJSData.currentPage - 1) * this.BPJSData.perPage + 1
        },
        lastRow() {
            return Math.min(this.BPJSData.currentPage * this.BPJSData.perPage, this.BPJSData.totalRows)
        },
    },
    methods: {
        patientFiltered(patientItems) {
            this.BPJSData.totalRows = patientItems.length
            this.BPJSData.currentPage = 1
        },
    },
}
</script>