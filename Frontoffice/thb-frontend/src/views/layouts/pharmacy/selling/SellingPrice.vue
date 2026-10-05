<template>
    <div>
        <TopPharmacyMenu selectedMenu="menu8" />
        <div class="mt-32 col">
            <section class="card">
                <div class="card-header bg-primary-200 font-weight-bold">Update Data</div>
                <div class="card-body d-flex justify-content-center">
                    <div class="col-lg-10">
                        <div class="row">
                            <div class="col-lg-6">
                                <div>
                                    <label class="c-form-label">Nama Katalog</label>
                                    <b-form-select />
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Non Kelas</label>
                                    <b-form-select />
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Pembelian Bebas</label>
                                    <b-form-select />
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Kelas 3</label>
                                    <b-form-select />
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Kelas 2</label>
                                    <b-form-select />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mt-12 mt-lg-0">
                                    <label class="c-form-label">Kelas 1</label>
                                    <b-form-select />
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">VIP</label>
                                    <b-form-select />
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">SVIP</label>
                                    <b-form-select />
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Update</label>
                                    <div>
                                        <b-form-input disabled value="simrs, Adm" />
                                        <b-form-input disabled value="25-05-2023 15:56:47" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <b-button pill variant="outline-primary" class="px-3 mr-2">
                                <feather-icon icon="XIcon" /> Reset
                            </b-button>
                            <b-button pill variant="primary" class="px-3">Update</b-button>
                        </div>
                    </div>
                </div>
            </section>
            <section class="mt-24 mt-lg-32">
                <p class="mb-0 text-bold-24">List Data</p>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="row mx-0">
                        <span class="text-regular-14">Tampilkan</span>
                        <select id="per-page-select" v-model="SellingTable.perPage"
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
                <b-table bordered responsive small head-variant="light" :fields="SellingTable.fields"
                    :items="filteredItems" :current-page="SellingTable.currentPage" :per-page="SellingTable.perPage"
                    class="mt-12 text-center" thead-class="text-bold-14" tbody-tr-class="text-regular-14">
                    <template #cell(action)>
                        <div>
                            <b-button size="sm" variant="info-700">Ubah</b-button>
                        </div>
                    </template>
                </b-table>
                <div class="col px-0 mt-20">
                    <div class="d-flex">
                        <span class="text-regular-14">Menampilkan {{ firstRow }} hingga {{ lastRow }} dari {{
                            SellingTable.totalRows }} entri</span>
                        <div class="ml-auto">
                            <b-pagination v-model="SellingTable.currentPage" :total-rows="SellingTable.totalRows"
                                :per-page="SellingTable.perPage" :current-page="SellingTable.currentPage" align="fill"
                                size="sm" class="my-0"></b-pagination>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
import TopPharmacyMenu from '@/@core/components/top-menu/TopPharmacyMenu.vue';

export default {
    components: {
        TopPharmacyMenu
    },
    data() {
        return {
            searchTerm: '',
            SellingTable: {
                fields: [
                    { key: 'calalog', label: 'Katalog', thStyle: { minWidth: '200px' } },
                    { key: 'free_purchase', label: 'Pembelian Bebas', thStyle: { minWidth: '140px' } },
                    { key: 'non_class', label: 'Non Kelas', thStyle: { minWidth: '140px' } },
                    { key: 'class_3', label: 'Kelas 3', thStyle: { minWidth: '140px' } },
                    { key: 'class_2', label: 'Kelas 2', thStyle: { minWidth: '140px' } },
                    { key: 'class_1', label: 'Kelas 1', thStyle: { minWidth: '140px' } },
                    { key: 'vip', label: 'VIP', thStyle: { minWidth: '140px' } },
                    { key: 'svip', label: 'SVIP', thStyle: { minWidth: '140px' } },
                    { key: 'update', label: 'Update', thStyle: { minWidth: '140px' } },
                    { key: 'action', label: 'Aksi', thStyle: { minWidth: '140px' } },
                ],
                items: [
                    {
                        calalog: '1.5/0.5 BURR HOLE PLATE 18.5 MM 01-7308',
                        free_purchase: '2.402.400',
                        non_class: '2.413.988',
                        class_3: '2.447.252',
                        class_2: '2.497.148',
                        class_1: '2.591.396',
                        vip: '2.686.568',
                        svip: '2.780631',
                        update: '28-06-2021 00:00:00',
                    },
                    {
                        calalog: '1.5/0.5 BURR HOLE PLATE 18.5 MM 01-7308',
                        free_purchase: '2.402.400',
                        non_class: '2.413.988',
                        class_3: '2.447.252',
                        class_2: '2.497.148',
                        class_1: '2.591.396',
                        vip: '2.686.568',
                        svip: '2.780631',
                        update: '28-06-2021 00:00:00',
                    },
                    {
                        calalog: '1.5/0.5 BURR HOLE PLATE 18.5 MM 01-7308',
                        free_purchase: '2.402.400',
                        non_class: '2.413.988',
                        class_3: '2.447.252',
                        class_2: '2.497.148',
                        class_1: '2.591.396',
                        vip: '2.686.568',
                        svip: '2.780631',
                        update: '28-06-2021 00:00:00',
                    },
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
        }
    },
    mounted() {
        this.SellingTable.totalRows = this.SellingTable.items.length
    },
    computed: {
        filteredItems() {
            const searchTerm = this.searchTerm.toLowerCase().trim()
            if (!searchTerm) {
                return this.SellingTable.items
            }
            return this.SellingTable.items.filter(item => {
                // Update the condition based on the structure of the items array
                return item.document_number.toLowerCase().includes(searchTerm) ||
                    item.receipt_number.toLowerCase().includes(searchTerm) ||
                    item.date.toLowerCase().includes(searchTerm) ||
                    item.document.toLowerCase().includes(searchTerm) ||
                    item.supplier.toLowerCase().includes(searchTerm) ||
                    item.warehouse.toLowerCase().includes(searchTerm) ||
                    item.notes.toLowerCase().includes(searchTerm) ||
                    item.values.toLowerCase().includes(searchTerm)
            })
        },
        firstRow() {
            return (this.SellingTable.currentPage - 1) * this.SellingTable.perPage + 1
        },
        lastRow() {
            return Math.min(this.SellingTable.currentPage * this.SellingTable.perPage, this.SellingTable.totalRows)
        },
    },
} 
</script>