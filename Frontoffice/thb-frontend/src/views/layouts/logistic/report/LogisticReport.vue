<template>
    <div>
        <TopLogisticMenu selectedMenu="menu11" />
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
                    <router-link :to="{ name: 'PharmacyReportAcceptanceView' }" class="prevent-hover">
                        <div class="bg-primary-100 p-20 justify-content-center align-items-center text-center d-flex flex-row flex-lg-column"
                            style="border-radius: 16px; border: 2px solid rgb(245, 165, 255);">
                            <img src="@/@core/assets/icons/pharmacy/report2.svg" alt="">
                            <p class="text-bold-16 mt-16 text-neutral-800">Laporan Penerimaan Farmasi</p>
                        </div>
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import TopLogisticMenu from '@/@core/components/top-menu/TopLogisticMenu.vue';

export default {
    components: {
        TopLogisticMenu
    },

} 
</script>