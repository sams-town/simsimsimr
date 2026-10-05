<template>
    <div>
        <TopBPJSeMenu selectedMenu="menu3" />
        <div class="col mt-32">
            <div class="d-flex justify-content-between align-items-center">
                <div class="row mx-0">
                    <span class="text-regular-14">Tampilkan</span>
                    <select id="per-page-select" v-model="DataSubmission.perPage"
                        class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                        <option value="5" selected>5</option>
                        <option value="10" selected>10</option>
                        <option value="15" selected>15</option>
                    </select>
                    <span class="text-regular-14">Entri</span>
                </div>
                <div class="col-6 col-lg-3 d-flex align-items-center position-relative">
                    <b-form-input size="sm" autocomplete="off" placeholder="Search" v-model="searchTerm"
                        class="rounded-pill pl-5"></b-form-input>
                    <b-icon icon="search" class="position-absolute pl-3 text-neutral-400" />
                </div>
            </div>
            <b-table small bordered responsive hover head-variant="light" :fields="DataSubmission.fields" :items="filteredItems"
                :current-page="DataSubmission.currentPage" :per-page="DataSubmission.perPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                <template #cell(action)>
                    <div>
                        <b-button size="sm" variant="primary">initial</b-button>
                    </div>
                </template>
            </b-table>
            <div class="col px-0 mt-20">
                <div class="d-flex">
                    <span class="text-regular-14">Menampilkan {{ firstRow }} hingga {{ lastRow }} dari {{
                        DataSubmission.totalRows }} entri</span>
                    <div class="ml-auto">
                        <b-pagination v-model="DataSubmission.currentPage" :total-rows="DataSubmission.totalRows"
                            :per-page="DataSubmission.perPage" :current-page="DataSubmission.currentPage" align="fill"
                            size="sm" class="my-0"></b-pagination>
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
            searchTerm: '',
            DataSubmission: {
                fields: [
                    { key: 'no', label: 'No' },
                    { key: 'card_number', label: 'Nomor Kartu', thStyle: { minWidth: '140px' } },
                    { key: 'name', label: 'Nama', thStyle: { minWidth: '140px' } },
                    { key: 'no_rm', label: 'No RM', thStyle: { minWidth: '140px' } },
                    { key: 'service_type', label: 'Jenis Pelayanan', thStyle: { minWidth: '140px' } },
                    { key: 'sep_date', label: 'Tanggal SEP', thStyle: { minWidth: '140px' } },
                    { key: 'notes', label: 'Keterangan', thStyle: { minWidth: '140px' } },
                    { key: 'user', label: 'User', thStyle: { minWidth: '140px' } },
                    { key: 'action', label: 'Aksi', thStyle: { minWidth: '140px' } },
                ],
                items: [
                    {
                        no: '1',
                        card_number: 'a',
                        name: '-',
                        no_rm: '-',
                        service_type: '-',
                        sep_date: '-',
                        notes: '-',
                        user: '-',
                    },
                    {
                        no: '2',
                        card_number: '-',
                        name: 'b',
                        no_rm: '-',
                        service_type: '-',
                        sep_date: '-',
                        notes: '-',
                        user: '-',
                    },
                    {
                        no: '3',
                        card_number: '-',
                        name: '-',
                        no_rm: 'c',
                        service_type: '-',
                        sep_date: '-',
                        notes: '-',
                        user: '-',
                    },
                    {
                        no: '4',
                        card_number: '-',
                        name: '-',
                        no_rm: '-',
                        service_type: 'd',
                        sep_date: '-',
                        notes: '-',
                        user: '-',
                    },
                    {
                        no: '5',
                        card_number: '-',
                        name: '-',
                        no_rm: '-',
                        service_type: '-',
                        sep_date: 'e',
                        notes: '-',
                        user: '-',
                    },
                    {
                        no: '6',
                        card_number: '-',
                        name: '-',
                        no_rm: '-',
                        service_type: '-',
                        sep_date: '-',
                        notes: 'f',
                        user: '-',
                    },

                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },

        }
    },
    mounted() {
        this.DataSubmission.totalRows = this.DataSubmission.items.length
    },
    computed: {
        filteredItems() {
            const searchTerm = this.searchTerm.toLowerCase().trim()
            if (!searchTerm) {
                return this.DataSubmission.items
            }
            return this.DataSubmission.items.filter(item => {
                // Update the condition based on the structure of the items array
                return item.card_number.toLowerCase().includes(searchTerm) ||
                    item.name.toLowerCase().includes(searchTerm) ||
                    item.no_rm.toLowerCase().includes(searchTerm) ||
                    item.service_type.toLowerCase().includes(searchTerm) ||
                    item.sep_date.toLowerCase().includes(searchTerm) ||
                    item.notes.toLowerCase().includes(searchTerm) ||
                    item.user.toLowerCase().includes(searchTerm)
            })
        },
        firstRow() {
            return (this.DataSubmission.currentPage - 1) * this.DataSubmission.perPage + 1
        },
        lastRow() {
            return Math.min(this.DataSubmission.currentPage * this.DataSubmission.perPage, this.DataSubmission.totalRows)
        },
    },
}
</script>
