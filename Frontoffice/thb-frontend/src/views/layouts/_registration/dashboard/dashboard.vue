<template>
    <div>
        <section class="row">
            <div class="col-lg">
                <!-- <incomeCard type="success" title="Total Pendapatan" income="57.250.000" :percent=7.2 /> -->
                <incomeCard type="success" title="Total Pendapatan" :income="dashboardDataItem.income" :percent=7.2 />
            </div>
            <div class="col-lg mt-16 mt-lg-0">
                <incomeCard title="Total Pendapatan Poliklinik" type="down" :income="dashboardDataItem.clinic_income" />
            </div>
            <div class="col-lg mt-16 mt-lg-0">
                <incomeCard type="danger" title="Total Pendapatan Farmasi" :income="dashboardDataItem.pharmacy_income"
                    :percent=3.1 />
            </div>
        </section>
        <section class="row">
            <div class="col-lg-4 pt-4">
                <div class="card bg-light h-100">
                    <div class="card-body">
                        <div>
                            <div class="text-semi-18">Total Pasien hari ini</div>
                            <!-- <div class="text-medium-16">5.987,34</div> -->
                            <div class="text-medium-16">{{ dashboardDataItem.total_patient }}</div>
                        </div>
                        <div class="mt-20">
                            <DoughnutChart />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg">
                <div class="row">
                    <div class="col-6 col-lg-4 mt-2 mt-lg-4">
                        <numberCard title="Pasien Rawat Jalan"
                            :number="dashboardDataItem.type_register ? dashboardDataItem.type_register.Outpatient : 0" />
                    </div>
                    <div class="col-6 col-lg-4 mt-2 mt-lg-4">
                        <numberCard title="Pasien Rawat Inap"
                            :number="dashboardDataItem.type_register ? dashboardDataItem.type_register.Inpatient : 0" />
                    </div>
                    <div class="col-6 col-lg-4 mt-2 mt-lg-4">
                        <numberCard title="Pasien IGD"
                            :number="dashboardDataItem.type_register ? dashboardDataItem.type_register.Emergency : 0" />
                    </div>
                    <div class="col-6 col-lg-4 mt-2 mt-lg-4">
                        <numberCard color="green" title="Radiologi"
                            :number="dashboardDataItem ? dashboardDataItem.radiology : 0" />
                    </div>
                    <div class="col-6 col-lg-4 mt-2 mt-lg-4">
                        <numberCard color="blue" title="Laboratorium"
                            :number="dashboardDataItem ? dashboardDataItem.laboratory : 0" />
                    </div>
                    <div class="col-6 col-lg-4 mt-2 mt-lg-4">
                        <numberCard color="orange" title="Fisioterapi"
                            :number="dashboardDataItem ? dashboardDataItem.physiotherapy : 0" />
                    </div>
                </div>
            </div>
        </section>
        <section class="mt-32">
            <div class="text-dark text-bold-24">Jenis pasien</div>
            <section class="row">
                <div class="col-lg">
                    <div class="row">
                        <div class="col-6 mt-2 mt-lg-4">
                            <typesCard type="down" title="Pasien Lama"
                                :number="dashboardDataItem.patient_type ? dashboardDataItem.patient_type.old : 0" />
                        </div>
                        <div class="col-6 mt-2 mt-lg-4">
                            <typesCard type="down" title="Pasien Baru"
                                :number="dashboardDataItem.patient_type ? dashboardDataItem.patient_type.new : 0" />
                        </div>
                        <div class="col-6 mt-2 mt-lg-4">
                            <typesCard title="Pasien Laki-laki"
                                :number="dashboardDataItem.sex ? dashboardDataItem.sex.male : 0" color="blue" />
                        </div>
                        <div class="col-6 mt-2 mt-lg-4">
                            <typesCard title="Pasien Perempuan"
                                :number="dashboardDataItem.sex ? dashboardDataItem.sex.female : 0" color="red" />
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 pt-4">
                    <div class="card bg-light h-100">
                        <div class="card-header font-weight-bold text-dark">Jenis Pasien</div>
                        <div class="card-body">
                            <div class="mt-20">
                                <PieChart />
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="mt-32">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card h-100">
                            <div class="card-header font-weight-bold text-dark">Jumlah Resep per Poli klinik</div>
                            <div class="card-body">
                                <BarChart class="col px-0 d-flex align-self-stretch" />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg mt-20 mt-lg-0">
                        <div class="row">
                            <div class="col">
                                <recipeCard />
                            </div>
                            <div class="col">
                                <recipeCard title="Fast" :number=20 color="red" class="col" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </section>
        <div class="p-16 p-lg-40 rounded content position-relative overflow-hidden mt-32" style="background: #F5EDFC;">
            <img src="../../../../@core/assets/icons/dashboard/decoration-top.svg" alt=""
                class="d-none d-lg-block position-absolute" style="top:0; right:0;">
            <img src="../../../../@core/assets/icons/dashboard/decoration-bot.svg" alt=""
                class="d-none d-lg-block position-absolute" style="bottom:0; left:0;">
            <div class="d-flex" style="z-index:100;">
                <b-button @click="goTotab('disease')" :variant="active_tab == 'disease' ? 'primary' : 'outline-primary'"
                    class="mr-2">10 Besar Penyakit</b-button>
                <b-button @click="goTotab('action')" :variant="active_tab == 'action' ? 'primary' : 'outline-primary'">10
                    Besar Tindakan</b-button>
            </div>
            <div class="col px-0 px-md-3">
                <div v-show="active_tab == 'disease'" class="mt-20">
                    <div class="d-flex justify-content-center">
                        <img src="../../../../@core/assets/images/dashboard/organs.png" alt="organs" width="50%">
                    </div>
                </div>
                <div v-show="active_tab == 'action'" class="mt-20">
                    <div class="bg-white p-2 rounded">
                        <b-table responsive head-variant="light" :items="items" thead-class="text-bold-14"
                            tbody-tr-class="text-regular-12" class="mt-12"></b-table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import moment from 'moment'
import { mapState } from 'vuex'
import numberCard from '../../../../@core/components/registration/numberCard.vue';
import incomeCard from '@/@core/components/registration/incomeCard.vue'
import DoughnutChart from './DoughnutChart.vue';

import typesCard from '../../../../@core/components/registration/typesCard.vue'
import recipeCard from '../../../../@core/components/registration/recipeCard.vue';
import BarChart from './BarChart.vue';
import PieChart from './PieChart.vue';

export default {
    components: {
        numberCard, incomeCard, typesCard, recipeCard, DoughnutChart, BarChart, PieChart
    },
    data() {
        return {
            active_tab: 'disease',
            items: [
                { disease: 'Blepharitis', total: '2' },
                { disease: 'Acute bronchitis', total: '2' },
                { disease: 'Impacted teeth', total: '2' },
                { disease: 'Nausea & vomiting', total: '2' },
                { disease: 'Acute nasopharyngitis', total: '3' },
                { disease: 'Chronic periodontitis', total: '5' },
                { disease: 'Neonatal jaundice', total: '5' },
                { disease: 'Pulpitis', total: '6' },
                { disease: 'Health supervision and care of other healthy infant and child', total: '20,8' },
            ],
            dashboardDataItem: {},
            params: {
                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date_end: moment(new Date()).format('YYYY-MM-DD'),
            },
        }
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat("en-US", { style: "currency", currency: "IDR" }).format(value)
        }
    },
    watch: {
        dashboardData(obj) {
            if (obj) {
                this.dashboardDataItem = obj
            }
        },
    },
    methods: {
        goTotab(tab) {
            this.active_tab = tab
        },
    },
    computed: {
        ...mapState({
            dashboardData: state => state.dashboard.dashboardData,
        }),
    },

    mounted() {
        console.log(this.params)
        this.$store.dispatch('dashboard/getDashboardByDate', {
                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date_end: moment(new Date()).format('YYYY-MM-DD'),
            });
    }
}
</script>
<!-- <style scoped>

.right-side::before {
    content: '';
    width: 537px;
    height: 365px;
    border-radius: 20px;
    background-color: #e0e0e0;
    position: absolute;
}
</style> -->