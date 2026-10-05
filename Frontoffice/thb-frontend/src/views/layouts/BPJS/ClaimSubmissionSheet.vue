<template>
    <div>
        <TopBPJSeMenu selectedMenu="menu4" />
        <div class="col mt-32">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between bg-indigo-200">
                    <span class="text-bold-18">Filter Data</span>
                    <router-link :to="{ name: 'BpjsCreateClaimView' }">
                        <b-button pill variant="outline-neutral-600" size="sm" class="px-3">
                            <b-icon icon="plus" /> Buat Klaim Baru
                        </b-button>
                    </router-link>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div class="col-lg-5">
                            <div>
                                <label class="c-form-label">Tanggal Pembuatan Klaim</label>
                                <b-form-input type="date" v-model="filter_form.claim_date"></b-form-input>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Jenis Pelayanan</label>
                                <v-select v-model="filter_form.health_facility" aria-readonly="true" :options="OptionsHealthFacility" label="title" placeholder="-- Pelayanan --"></v-select>
                            </div>
                            <div class="d-flex justify-content-lg-end mt-24">
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
                    <select id="per-page-select" v-model="ClaimSubmission.perPage"
                        class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                        <option value="5" selected>5</option>
                        <option value="10" selected>10</option>
                        <option value="15" selected>15</option>
                    </select>
                    <span class="text-regular-14">Entri</span>
                </div>
                <b-table bordered responsive hover small head-variant="light" :fields="ClaimSubmission.field"
                    :items="ClaimSubmission.item" :current-page="ClaimSubmission.currentPage" @filtered="patientFiltered"
                    :per-page="ClaimSubmission.perPage" tbody-tr-class="text-medium-14" class="mt-12 text-center">
                    <template #cell(action)>
                        <div>
                            <b-button size="sm">Initial</b-button>
                        </div>
                    </template>
                </b-table>
                <div class="col px-0 mt-20">
                    <div class="d-flex">
                        <span class="text-regular-14">Menampilkan {{ firstRow }} hingga {{ lastRow }} dari {{
                            ClaimSubmission.totalRows }} entri</span>
                        <div class="ml-auto">
                            <b-pagination v-model="ClaimSubmission.currentPage" :total-rows="ClaimSubmission.totalRows"
                                :per-page="ClaimSubmission.perPage" :current-page="ClaimSubmission.currentPage" align="fill" size="sm"
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
import vSelect from 'vue-select'

export default {
    components: {
        TopBPJSeMenu, vSelect
    },
    data() {
        return {
            OptionsHealthFacility: [
                { value: 1, title: 'Rawat Inap' },
                { value: 2, title: 'Rawat Jalan' },
            ],
            filter_form: {
                claim_date: '',
                health_facility: '',
            },
            ClaimSubmission: {
                field: [
                    { key: 'no', label: 'No' },
                    { key: 'sep_number', label: 'No SEP', thStyle: { minWidth: '140px' } },
                    { key: 'sep_date', label: 'Tanggal SEP', thStyle: { minWidth: '140px' } },
                    { key: 'card_number', label: 'No Kartu', thStyle: { minWidth: '140px' } },
                    { key: 'name', label: 'Nama Pasien', thStyle: { minWidth: '140px' } },
                    { key: 'no_rm', label: 'No RM', thStyle: { minWidth: '140px' } },
                    { key: 'service_type', label: 'Jenis Layanan', thStyle: { minWidth: '140px' } },
                    { key: 'action', label: 'Aksi', thStyle: { minWidth: '140px' } },
                ],
                item: [
                    { no: '1', no_rm: '-', service_type: '-', sep_number: '-', sep_date: '-', card_number: '-', name: '-', },
                    { no: '2', no_rm: '-', service_type: '-', sep_number: '-', sep_date: '-', card_number: '-', name: '-', },
                    { no: '3', no_rm: '-', service_type: '-', sep_number: '-', sep_date: '-', card_number: '-', name: '-', },
                    { no: '4', no_rm: '-', service_type: '-', sep_number: '-', sep_date: '-', card_number: '-', name: '-', },
                    { no: '5', no_rm: '-', service_type: '-', sep_number: '-', sep_date: '-', card_number: '-', name: '-', },
                    { no: '6', no_rm: '-', service_type: '-', sep_number: '-', sep_date: '-', card_number: '-', name: '-', },
                    
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },

        }
    },
    mounted() {
        this.ClaimSubmission.totalRows = this.ClaimSubmission.item.length
    },
    computed: {
        firstRow() {
            return (this.ClaimSubmission.currentPage - 1) * this.ClaimSubmission.perPage + 1
        },
        lastRow() {
            return Math.min(this.ClaimSubmission.currentPage * this.ClaimSubmission.perPage, this.ClaimSubmission.totalRows)
        },
    },
    methods: {
        patientFiltered(patientItems) {
            this.ClaimSubmission.totalRows = patientItems.length
            this.ClaimSubmission.currentPage = 1
        },
    },
}
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
</style>