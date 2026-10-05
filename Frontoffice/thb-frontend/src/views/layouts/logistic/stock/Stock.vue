<template>
    <div>
        <TopLogisticMenu selectedMenu="menu5" />
        <div class="col mt-32">
            <section class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="label-head">
                    <span class="px-16 text-bold-24 text-neutral-700">Permintaan Logistik Umum</span>
                </div>
                <!-- <div class="d-flex flex-wrap">
                    <b-button @click="active.ModalOpen = !active.ModalOpen" pill variant="primary" class="px-3">
                        <feather-icon icon="PlusIcon" class="mr-8" /> Data Perencanaan
                    </b-button>
                </div> -->
            </section>
            <div class="card mt-24">
                <div class="card-header font-weight-bold bg-primary-200">Filter Kartu Stok Logistik</div>
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div class="col-lg-5">
                            <div class="row">
                                <div class="col-lg-6">
                                    <label class="c-form-label">Tanggal Filter</label>
                                    <b-form-input type="date" />
                                </div>
                                <div class="col-lg-6 mt-12 mt-lg-0">
                                    <label class="c-form-label">Sampai dengan</label>
                                    <b-form-input type="date" />
                                </div>
                            </div>
                            <!-- <div class="mt-12">
                                <label class="c-form-label">Gudang / Apotik / Satelit</label>
                                <b-form-select />
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Nama Barang</label>
                                <b-form-select />
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Kelompok Barang</label>
                                <b-form-select />
                            </div> -->
                            <div class="mt-12">
                                <label class="c-form-label">Gudang</label>
                                <b-form-radio-group v-model="PostFilter.Warehouse" size="sm" stacked>
                                    <b-form-radio value="1">kartu Stok Barang</b-form-radio>
                                    <b-form-radio value="2">Monitor Persediaan Barang Excel</b-form-radio>
                                    <b-form-radio value="3">Monitor Persediaan Barang Latlest</b-form-radio>
                                </b-form-radio-group>
                            </div>
                            <div class="mt-24 d-flex justify-content-lg-end">
                                <b-button variant="outline-primary-500" class="px-3 mr-2">
                                    <feather-icon icon="XIcon" /> Clear Filter
                                </b-button>
                                <router-link :to="PostFilter.Warehouse === '3'? { name: 'LogisticStocLogisticView' } : { name: 'LogisticStockCardView' }" target="_blank">
                                    <b-button variant="primary-500" class="px-3"> Filter Data </b-button>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
            PostFilter: {
                // TanggalFilter: '',
                // SampaiDengan: '',
                // GudangApotikSatelit: '',
                // NamaBarang: '',
                // KelompokBarang: '',
                Warehouse: '',
            },
            // active: {
            //     ModalOpen: false
            // }
        };
    },
};
</script>
<style>
.label-head::before {
    content: '';
    border-left: 6px solid #648DE1;
}
</style>
