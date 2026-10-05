<template>
    <div>
        <TopLaboratoryMenu selectedMenu="menu5" />
        <div class="col mt-32">
            <div class="card">
                <div @click="showFilter = !showFilter" class="card-header d-flex justify-content-between bg-primary-200">
                    <span class="text-bold-18">Filter Data</span>
                    <b-icon v-if="showFilter" icon="chevron-up" />
                    <b-icon v-else icon="chevron-down" />
                </div>
                <div v-if="showFilter" class="card-body">
                    <div class="d-flex justify-content-center">
                        <div class="col-lg-6">
                            <div>
                                <label class="c-form-label">Grafik</label>
                                <b-form-select></b-form-select>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Tahun</label>
                                <b-form-datepicker></b-form-datepicker>
                            </div>
                            <div class="mt-20 row">
                                <div class="col col-lg-3 ml-auto">
                                    <b-button pill variant="outline-primary" class="btn-block">Cancel</b-button>
                                </div>
                                <div class="col col-lg-3">
                                    <b-button pill variant="primary" class="btn-block">Submit</b-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body mt-32" style="background-color: #F8F9FA;">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-semi-18">Grafik Jumlah Kunjungan Pasien 2022</p>
                        <p class="text-regular-12">Januari - Desember 2022</p>
                    </div>
                    <div>
                        <feather-icon icon="InfoIcon" />
                    </div>
                </div>
                <BarChart :height=300 class="mt-24 w-100"/>
            </div>
        </div>
    </div>
</template>
<script>
import TopLaboratoryMenu from '@/@core/components/top-menu/TopLaboratoryMenu.vue';
import BarChart from './BarChart.vue';

export default {
    name: 'LaboratoryListView',
    components: {
    TopLaboratoryMenu,
    BarChart,
    BarChart
},
    data() {
        return {
            showFilter: true,
        };
    },
}
</script>