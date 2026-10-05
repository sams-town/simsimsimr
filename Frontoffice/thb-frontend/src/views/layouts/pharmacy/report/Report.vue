<template>
    <div>
        <TopPharmacyMenu selectedMenu="menu12" />
        <div class="col mt-32">
            <div class="row">
                <div class="col-lg-3">
                    <router-link :to="{ name: 'PharmacyReportSalesView' }" class="prevent-hover">
                        <div class="bg-info-300 p-20 justify-content-center align-items-center text-center d-flex flex-row flex-lg-column"
                            style="border-radius: 16px; border: 2px solid rgb(125, 188, 255);">
                            <img src="@/@core/assets/icons/pharmacy/report1.svg" alt="">
                            <p class="text-bold-16 mt-16 text-neutral-800">Laporan Penjualan Farmasi</p>
                        </div>
                    </router-link>
                </div>
                <div class="col-lg-3 mt-12 mt-lg-0">
                    <router-link :to="{ name: 'PharmacyReportOrderView' }" class="prevent-hover">
                        <div class="bg-primary-100 p-20 justify-content-center align-items-center text-center d-flex flex-row flex-lg-column"
                            style="border-radius: 16px; border: 2px solid rgb(245, 165, 255);">
                            <img src="@/@core/assets/icons/pharmacy/report2.svg" alt="">
                            <p class="text-bold-16 mt-16 text-neutral-800">Laporan Pembelian Farmasi</p>
                        </div>
                    </router-link>
                </div>
                <div class="col-lg-3 mt-12 mt-lg-0">
                    <router-link :to="{ name: 'PharmacyReportAcceptanceView' }" class="prevent-hover">
                        <div class="bg-primary-100 p-20 justify-content-center align-items-center text-center d-flex flex-row flex-lg-column"
                            style="border-radius: 16px; border: 2px solid rgb(245, 165, 255);">
                            <img src="@/@core/assets/icons/pharmacy/report2.svg" alt="">
                            <p class="text-bold-16 mt-16 text-neutral-800">Laporan Penerimaan Farmasi</p>
                        </div>
                    </router-link>
                </div>
                <div class="col-lg-3 mt-12 mt-lg-0">
                    <router-link :to="{ name: 'PharmacyReportOpnameView' }" class="prevent-hover">
                        <div class="bg-primary-100 p-20 justify-content-center align-items-center text-center d-flex flex-row flex-lg-column"
                            style="border-radius: 16px; border: 2px solid rgb(245, 165, 255);">
                            <img src="@/@core/assets/icons/pharmacy/report2.svg" alt="">
                            <p class="text-bold-16 mt-16 text-neutral-800">Laporan Stok Opname Farmasi</p>
                        </div>
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import TopPharmacyMenu from '@/@core/components/top-menu/TopPharmacyMenu.vue';

export default {
    components: {
        TopPharmacyMenu
    },

} 
</script>