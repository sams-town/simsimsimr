<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
            { text: 'Home', link: '/' },
            { text: 'Dashboard Executive' },
            ]"
        >
        <section class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="text-dark text-bold-24 mb-4">Dashboard Executive</div>

            <div class="d-flex flex-wrap align-items-end gap-3">
                <div class="filter-group mr-3">
                    <label class="filter-label">Unit / Layanan</label>
                    <select
                        v-model="selectedType"
                        @change="fetchWithFilters"
                        class="form-control filter-input"
                    >
                        <option value="emergency">IGD</option>
                        <option value="outpatientVisits">Poliklinik</option>
                        <option value="outpatientIntoInpatient">Poliklinik Masuk Ranap</option>
                    </select>
                </div>
                <div class="filter-group mr-3">
                    <label class="filter-label">Periode</label>
                    <select
                        v-model="params.period"
                        @change="fetchWithFilters"
                        class="form-control filter-input"
                    >
                        <option value="daily">Harian</option>
                        <option value="monthly">Bulanan</option>
                    </select>
                </div>
                <div class="filter-group mr-3">
                    <label class="filter-label">Tanggal Mulai</label>
                    <input type="date"
                        v-model="params.date_start"
                        @change="fetchWithFilters"
                        class="form-control filter-input"
                    />
                </div>
                <div class="filter-group mr-3">
                    <label class="filter-label">Tanggal Akhir</label>
                    <input type="date"
                        v-model="params.date_end"
                        @change="fetchWithFilters"
                        class="form-control filter-input"
                    />
                </div>
            </div>
            </section>

            <section class="mt-32">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="text-dark fw-bold mb-3">
                           {{ formattedTitle }}
                        </h5>
                        <div class="horizontal-scroll">
                            <b-table
                                :items="tableData"
                                :fields="fields"
                                bordered
                                small
                                class="freeze-first-col"
                            >
                                <template #thead-top>
                                    <tr>
                                        <th rowspan="1" class="text-center bg-primary text-white"></th>
                                        <th rowspan="1" class="text-center bg-primary text-white">Total</th>
                                        <th v-for="(count, idx) in dateColCounts"
                                            :key="'date'+idx"
                                            :colspan="count"
                                            class="text-center bg-primary text-white">
                                            {{ dateLabels[idx] }}
                                        </th>
                                    </tr>
                                </template>

                                <template #cell(total)="data">
                                    <span class="text-primary font-weight-bold">{{ data.value }}</span>
                                </template>
                            </b-table>
                        </div>
                    </div>
                </div>
            </section>
        </Layout>
    </div>
</template>
<script>
import moment from 'moment'
import { mapState } from 'vuex'
import numberCard from '@/@core/components/registration/numberCard.vue';
import incomeCard from '@/@core/components/registration/incomeCard.vue'
import DoughnutChart from '@/views/layouts/_registration/dashboard/DoughnutChart.vue';

import typesCard from '@/@core/components/registration/typesCard.vue'
import recipeCard from '@/@core/components/registration/recipeCard.vue';
import BarChart from '@/views/layouts/_registration/dashboard/BarChart.vue';
import PieChart from '@/views/layouts/_registration/dashboard/PieChart.vue';

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
                period:'daily',
                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date_end: moment(new Date()).format('YYYY-MM-DD'),
            },

            tableData: [],
            fields: [],
            dateColCounts: [],  
            dateLabels: [],
            selectedType:'emergency',
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
    computed: {
        ...mapState({
            // dashboardData: state => state.dashboard.dashboardData,
        }),
        formattedTitle() {
            const unitNames = {
                emergency: "IGD",
                outpatientVisits: "Poliklinik",
                outpatientIntoInpatient: "Poliklinik Masuk Ranap"
            }

            let periodLabel = this.params.period === "daily" ? "Harian" : "Bulanan"

            let title = `Data ${unitNames[this.selectedType] || ''} ${periodLabel}`

            if (this.params.date_start && this.params.date_end) {
                let start = this.formatDateView(this.params.date_start)
                let end = this.formatDateView(this.params.date_end)
                title += ` (${start} – ${end})`
            }

            return title
        }
    },
    methods: {
        formatDateView(date) {
            return new Date(date).toLocaleDateString("id-ID", {
            day: "2-digit",
            month: "short",
            year: "numeric"
            }).replace(/\./g, '')
        },
        fetchWithFilters() {
            this.getData(this.selectedType);
        },
        getData(type) {
            console.log(type,'typegetdata')

            if(type=='emergency'){
                this.$store.dispatch('executive/getDashboardEmergencyVisits', this.params).then(data => {
                    console.log('this.getDashboardEmergencyVisits: ',data)
                    this.fetchTableDataEmergency(data.data)
                })
            }

            if(type=='outpatientVisits'){
                this.$store.dispatch('executive/getDashboardOutpatientVisits', this.params).then(data => {
                    console.log('this.getDashboardOutpatientVisits: ',data)
                    this.fetchTableDataOutpatientVisits(data.data)
                })
            }

            if(type=='outpatientIntoInpatient'){
                this.$store.dispatch('executive/getDashboardOutpatientIntoInpatient', this.params).then(data => {
                    console.log('this.getDashboardOutpatientIntoInpatient: ',data)
                    this.fetchTableDataOutpatientIntoInpatient(data.data)
                })
            }
        },
        fetchTableDataOutpatientIntoInpatient(data) {
            function formatDate(d) {
                return new Date(d).toLocaleDateString("id-ID", {
                    day: "2-digit",
                    month: "short",
                    year: "numeric"
                });
            }

            const polikliniks = new Set();
            const dateLabels = [];
            const fields = [
                { key: "category", label: "" },
                { key: "total", label: "" }
            ];

            // Step 1: Collect all date columns & poliklinik names
            data.forEach((day, i) => {
                dateLabels.push(formatDate(day.date));
                fields.push({ key: `d${i}`, label: "" });

                day.data.forEach(item => {
                    polikliniks.add(item.name);
                });
            });

            this.fields = fields;
            this.dateLabels = dateLabels;
            this.dateColCounts = data.map(() => 1); // each date = 1 column

            // Step 2: Build rows for each poliklinik
            const rows = [...polikliniks].map(name => ({
                category: name
            }));

            // Step 3: Fill data per date
            rows.forEach(row => {
                data.forEach((day, i) => {
                    const item = day.data.find(x => x.name === row.category);
                    row[`d${i}`] = item ? parseInt(item.total_inpatient) : 0;
                });
            });

            // Step 4: Add total per date row
            const totalRow = { category: "TOTAL RANAP DARI POLIKLINIK", total: 0 };
            data.forEach((day, i) => {
                const total = parseInt(day.total);

                totalRow[`d${i}`] = total;
                totalRow.total += total;
            });
            rows.push(totalRow);

            this.tableData = rows;
        },
        fetchTableDataOutpatientVisits(data) {
            function formatDate(d) {
                return new Date(d).toLocaleDateString("id-ID", {
                    day: "2-digit",
                    month: "short",
                    year: "numeric"
                });
            }

            const polikliniks = new Set();
            const dateLabels = [];
            const fields = [
                { key: "category", label: "" },
                { key: "total", label: "" }
            ];

            // Step 1: Collect date & poliklinik names
            data.forEach((day, i) => {
                dateLabels.push(formatDate(day.date));
                fields.push({ key: `d${i}`, label: "" });

                day.data.forEach(item => {
                    polikliniks.add(item.name);
                });
            });

            this.fields = fields;
            this.dateLabels = dateLabels;
            this.dateColCounts = data.map(() => 1);

            // Step 2: Create base rows
            const rows = [...polikliniks].map(name => ({
                category: name,
            }));

            // Step 3: Fill poliklinik data + sum totals
            rows.forEach(row => {
                data.forEach((day, i) => {
                    const item = day.data.find(x => x.name === row.category);
                    const value = item ? parseInt(item.total) : 0;
                    row[`d${i}`] = value;
                });
            });

            // Step 4: Footer rows (special totals) — do not sum left "total" column
            const totalPoliRow = { category: "TOTAL KUNJUNGAN RAWAT JALAN", total: 0 };
            const totalSpecialistPoliRow = { category: "TOTAL KUNJUNGAN POLIKLINIK SPESIALIS"};
            const totalOthersPoliRow = { category: "TOTAL KUNJUNGAN RAWAT JALAN LAINNYA"};

            data.forEach((day, i) => {
                const total = parseInt(day.total);
                const totalSpec = parseInt(day.total_specialist);
                const totalOther = parseInt(day.total_other);

                totalPoliRow[`d${i}`] = total;
                totalSpecialistPoliRow[`d${i}`] = totalSpec;
                totalOthersPoliRow[`d${i}`] = totalOther;

                totalPoliRow.total += total;
                // totalSpecialistPoliRow.total += totalSpec;
                // totalOthersPoliRow.total += totalOther;
            });

            rows.push(totalPoliRow);
            rows.push(totalSpecialistPoliRow);
            rows.push(totalOthersPoliRow);

            this.tableData = rows;
        },

        fetchTableDataEmergency(data) {
            function formatDate(d) {
                const dt = new Date(d);
                return dt.toLocaleDateString("id-ID", {
                    day: "2-digit",
                    month: "short",
                    year: "numeric"
                });
            }

            const dateColCounts = [];
            const fields = [
                { key: "category", label: " " }, // Category column with no header
                { key: "total", label: " " } 
            ]

            // Create fields dynamically based on doctor count per date
            for (let i = 0; i < data.length; i++) {
                const doctorCount = data[i].data.length;
                dateColCounts.push(doctorCount);

                for (let j = 0; j < doctorCount; j++) {
                    fields.push({
                        key: `d${i}_${j}`,
                        label: " ", // Hide column header text
                    })
                }
            }

            this.fields = fields;
            this.dateColCounts = dateColCounts;
            this.dateLabels = data.map(i => formatDate(i.date));

            const rows = [
                { category: "DOKTER UMUM" },
                { category: "PASIEN RAWAT JALAN" },
                { category: "PASIEN RAWAT INAP" },
                { category: "PASIEN MENINGGAL" },
                { category: "JUMLAH PASIEN" },
            ];

            //assign total buat sum
            rows[0].total = ""; 
            rows[1].total = "";
            rows[2].total = 0;
            rows[3].total = "";
            rows[4].total = 0;

            // Fill cells
            for (let i = 0; i < data.length; i++) {
                const group = data[i]
                for (let j = 0; j < group.data.length; j++) {
                    const key = "d" + i + "_" + j
                    const doc = group.data[j]


                    rows[0][key] = doc.name
                    rows[1][key] = doc.total_outpatient
                    rows[2][key] = doc.total_inpatient
                    rows[3][key] = doc.total_death

                    let sumValue = doc.total_outpatient + doc.total_inpatient + doc.total_death
                    rows[4][key] = sumValue

                    
                    // rows[1].total += rows[1][key]
                    rows[2].total += rows[2][key]
                    // rows[3].total += rows[3][key]
                    rows[4].total += sumValue
                }
            }

            // console.log(rows, 'thistabledatarow')
            this.tableData = rows;
        },
    },
    mounted() {
        console.log(this.params)
        this.getData(this.selectedType)
    }
}
</script>
<style scoped>

/* .right-side::before {
content: '';
width: 537px;
height: 365px;
border-radius: 20px;
background-color: #e0e0e0;
position: absolute;
} */

.filter-group {
  display: flex;
  flex-direction: column;
  min-width: 150px;
}

.filter-label {
  font-size: 0.75rem;
  font-weight: 600;
  margin-bottom: 2px;
  color: #4a4a4a;
}

.filter-input {
  min-width: 150px;
  height: 36px;
}

@media (max-width: 768px) {
  .filter-group {
    width: 100%;
  }
}


.horizontal-scroll {
  overflow-x: auto;
  width: 100%;
}

/* Freeze first column (header + rows) */
:deep(.freeze-first-col th:first-child),
:deep(.freeze-first-col td:first-child) {
  position: sticky;
  left: 0;
  z-index: 2;
  background: #fff;
  white-space: nowrap;
}

/* Optional: shadow so freeze looks nicer */
:deep.freeze-first-col td:first-child {
  box-shadow: 3px 0 6px rgba(0,0,0,0.15);
}


</style>