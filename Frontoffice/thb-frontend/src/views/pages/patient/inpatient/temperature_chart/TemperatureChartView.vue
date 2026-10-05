<template>
    <div>
        <section class="mt-32">
            <ModernLabel label="Monitoring Grafik Suhu" class="mt-32 mb-16" />

            <div class="tc-chart-card">
                <div class="row mb-16">
                    <div class="col-lg-3">
                        <label class="c-form-label">Tanggal Mulai</label>
                        <b-form-input v-model="params_range.date_start" @change="getTCRange" type="date" size="sm"/>
                    </div>
                    <div class="col-lg-3">
                        <label class="c-form-label">Tanggal Akhir</label>
                        <b-form-input v-model="params_range.date_end" @change="getTCRange" type="date" size="sm"/>
                    </div>
                </div>
                <div v-if="chartData.labels.length === 0" class="tc-chart-empty">
                    <feather-icon icon="ActivityIcon" size="28" class="mb-2"/>
                    <div>Belum ada data pada rentang tanggal ini</div>
                </div>
                <LineChart
                    v-else
                    :chart-options="chartOptions"
                    :chart-data="chartData"
                    :height="260"
                />
            </div>
        </section>

        <section class="mt-32">
            <div class="d-flex justify-content-between align-items-center mb-16">
                <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-0">
                    <span class="text-regular-14">Tampilkan</span>
                    <b-form-select v-model="perPage" size="sm" :options="pageOptions" class="mx-1" style="width: 80px; display: inline-block;"/>
                    <span class="text-regular-14">Entri</span>
                </b-form-group>
                <b-button @click="openModalMain" size="sm" variant="primary" class="rounded-pill">
                    <feather-icon icon="PlusIcon" class="mr-1"/> Tambah
                </b-button>
            </div>

            <div v-if="temperature_list.length === 0" class="tc-empty-list">
                <feather-icon icon="ThermometerIcon" size="26" class="mb-2"/>
                <div>Belum ada data monitoring</div>
            </div>

            <div
                v-for="(item, index) in temperature_list"
                :key="item.id"
                class="tc-card"
            >
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-start flex-wrap tc-card-header">
                    <div>
                        <span class="tc-badge">Monitoring ke-{{ firstRows + index }}</span>
                        <div class="tc-card-title">{{ localDate(item.monitoring_date) }}</div>
                        <div class="tc-subtext" v-if="item.diet">
                            <feather-icon icon="ClipboardIcon" size="12" class="mr-1"/>Diet: {{ item.diet }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center tc-header-actions">
                        <b-button size="sm" variant="primary" class="rounded-pill mr-1" @click="onEditTemperature(item)">
                            <feather-icon icon="EditIcon" size="13" class="mr-1"/>Edit
                        </b-button>
                        <b-button size="sm" variant="danger" class="rounded-pill mr-1" @click="removeTemperature(item)">
                            <feather-icon icon="TrashIcon" size="13" class="mr-1"/>Hapus
                        </b-button>
                        <b-button size="sm" variant="outline-success" class="rounded-pill" @click="openModalItem(item.id)">
                            <feather-icon icon="PlusIcon" size="13" class="mr-1"/>Monitoring
                        </b-button>
                    </div>
                </div>

                <!-- Grid sections -->
                <div class="row tc-grid-row">
                    <div class="col-lg-7">
                        <div class="tc-section-title">DATA MONITORING</div>
                        <div class="tc-box-grid">
                            <div class="tc-box">
                                <div class="tc-box-label">Diet</div>
                                <div class="tc-box-value">{{ item.diet || '-' }}</div>
                            </div>
                            <div class="tc-box">
                                <div class="tc-box-label">Bentuk BAB</div>
                                <div class="tc-box-value">{{ item.defecation_form || '-' }}</div>
                            </div>
                            <div class="tc-box">
                                <div class="tc-box-label">Frekuensi BAB</div>
                                <div class="tc-box-value">{{ item.defecation_frequency || '-' }}</div>
                            </div>
                            <div class="tc-box">
                                <div class="tc-box-label">Intake</div>
                                <div class="tc-box-value">{{ item.intake || 0 }} ml</div>
                            </div>
                            <div class="tc-box">
                                <div class="tc-box-label">Output</div>
                                <div class="tc-box-value">{{ item.output || 0 }} ml</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="tc-section-title">TEKANAN DARAH</div>
                        <div class="tc-box-grid">
                            <div class="tc-box">
                                <div class="tc-box-label">Pagi</div>
                                <div class="tc-box-value">{{ item.systolic_morning || '-' }}/{{ item.diastolic_morning || '-' }} mmHg</div>
                            </div>
                            <div class="tc-box">
                                <div class="tc-box-label">Siang</div>
                                <div class="tc-box-value">{{ item.systolic_afternoon || '-' }}/{{ item.diastolic_afternoon || '-' }} mmHg</div>
                            </div>
                            <div class="tc-box">
                                <div class="tc-box-label">Malam</div>
                                <div class="tc-box-value">{{ item.systolic_evening || '-' }}/{{ item.diastolic_evening || '-' }} mmHg</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mini trend charts -->
                <div class="row tc-grid-row" v-if="item.temperature_chart_items?.length > 0">
                    <div class="col-lg-6">
                        <div class="tc-mini-chart-card">
                            <div class="tc-mini-title">Tren Suhu <span class="tc-mini-sub">(normal 36-37,5 °C)</span></div>
                            <LineChart
                                :chart-options="miniChartOptions"
                                :chart-data="miniChartData(item, 'temperature', 36, 37.5, '#F2994A')"
                                :height="90"
                            />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="tc-mini-chart-card">
                            <div class="tc-mini-title">Tren Nadi <span class="tc-mini-sub">(normal 60-100 x/mnt)</span></div>
                            <LineChart
                                :chart-options="miniChartOptions"
                                :chart-data="miniChartData(item, 'pulse', 60, 100, '#5C6BC0')"
                                :height="90"
                            />
                        </div>
                    </div>
                </div>

                <!-- Items table -->
                <div class="tc-grid-row" v-if="item.temperature_chart_items?.length > 0">
                    <b-table :fields="ItemFields" :items="item.temperature_chart_items" responsive class="tc-item-table mb-0">
                        <template #cell(observation_hours)="data">
                            <span class="tc-time">{{ data.value }}</span>
                        </template>
                        <template #cell(pulse)="data">
                            {{ data.value }} <span class="tc-unit">x/mnt</span>
                        </template>
                        <template #cell(rr)="data">
                            {{ data.value }} <span class="tc-unit">x/mnt</span>
                        </template>
                        <template #cell(temperature)="data">
                            {{ data.value }} <span class="tc-unit">°C</span>
                        </template>
                        <template #cell(action)="data">
                            <div class="d-flex">
                                <b-button v-b-tooltip.hover title="Edit" @click="onEditItem(data.item)" size="sm" variant="warning-500" class="mr-1 tc-icon-btn">
                                    <feather-icon icon="EditIcon" size="14"/>
                                </b-button>
                                <b-button v-b-tooltip.hover title="Hapus" @click="removeItemTemperature(data.item, item.monitoring_date)" size="sm" variant="danger-500" class="tc-icon-btn">
                                    <feather-icon icon="TrashIcon" size="14"/>
                                </b-button>
                            </div>
                        </template>
                    </b-table>
                </div>
                <div class="tc-grid-row tc-no-items" v-else>
                    Belum ada observasi jam untuk tanggal ini.
                </div>
            </div>

            <div class="col px-0 mt-20" v-if="temperature_list.length > 0">
                <div class="d-flex align-items-center">
                    <span class="text-regular-14 tc-subtext">Menampilkan {{ firstRows }} hingga <template v-if="lastRows < totalRows">{{ lastRows }}</template> <template v-else>{{ totalRows }}</template> dari {{ totalRows }} entri</span>
                    <div class="ml-auto">
                        <b-pagination
                            v-model="currentPage"
                            :total-rows="totalRows"
                            :per-page="perPage"
                            align="fill" size="sm" class="my-0">
                        </b-pagination>
                    </div>
                </div>
            </div>
        </section>

        <b-modal v-model="active.modal_main" title="Tambah/edit Tanggal Observasi" no-close-on-backdrop>
            <div class="row">
                <div class="col-lg-4">
                    <label class="c-form-label">Tangal Monitoring</label>
                    <b-form-input v-model="form_temperature.monitoring_date" type="date" />
                </div>
                <div class="col-lg-4">
                    <label class="c-form-label">Diet</label>
                    <b-form-input v-model="form_temperature.diet" />
                </div>
                <div class="col-lg-4">
                    <label class="c-form-label">Bentuk BAB</label>
                    <b-form-input v-model="form_temperature.defecation_form" />
                </div>
                <div class="col-lg-4">
                    <label class="c-form-label">Frekuensi BAB</label>
                    <b-form-input v-model="form_temperature.defecation_frequency" />
                </div>
                <div class="col-lg-4">
                    <label class="c-form-label">Intake</label>
                    <b-form-input v-model="form_temperature.intake" type="number"/>
                </div>
                <div class="col-lg-4">
                    <label class="c-form-label">Output</label>
                    <b-form-input v-model="form_temperature.output" type="number"/>
                </div>
                <div class="col-lg-4">
                    <label class="c-form-label">Sistolik Pagi</label>
                    <b-input-group append="mmHg">
                        <b-form-input v-model="form_temperature.systolic_morning" type="number" />
                    </b-input-group>
                </div>
                <div class="col-lg-4">
                    <label class="c-form-label">Diastolik Pagi</label>
                    <b-input-group append="mmHg">
                        <b-form-input v-model="form_temperature.diastolic_morning" type="number"/>
                    </b-input-group>
                </div>
                <div class="col-lg-4">
                    <label class="c-form-label">Sistolik Siang</label>
                    <b-input-group append="mmHg">
                        <b-form-input v-model="form_temperature.systolic_afternoon" type="number"/>
                    </b-input-group>
                </div>
                <div class="col-lg-4">
                    <label class="c-form-label">Diastolik Siang</label>
                    <b-input-group append="mmHg">
                        <b-form-input v-model="form_temperature.diastolic_afternoon" type="number"/>
                    </b-input-group>
                </div>
                <div class="col-lg-4">
                    <label class="c-form-label">Sistolik Malam</label>
                    <b-input-group append="mmHg">
                        <b-form-input v-model="form_temperature.systolic_evening" type="number"/>
                    </b-input-group>
                </div>
                <div class="col-lg-4">
                    <label class="c-form-label">Diastolik Malam</label>
                    <b-input-group append="mmHg">
                        <b-form-input v-model="form_temperature.diastolic_evening" type="number"/>
                    </b-input-group>
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button @click="validationTemperature" :disabled="active.disabled_main" variant="primary">Simpan</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal_item" size="lg" title="Tambah/edit Observasi" no-close-on-backdrop>
            <div class="row">
                <div class="col-lg-4">
                    <label class="c-form-label">Jam Observasi</label>
                    <b-form-input v-model="form_item.observation_hours" disabled type="time" />
                </div>
                <div class="col-lg-4">
                    <label class="c-form-label">Frekuensi Nadi</label>
                    <b-input-group append="x/mnt">
                        <b-form-input type="number" v-model="form_item.pulse" />
                    </b-input-group>
                </div>
                <div class="col-lg-4">
                    <label class="c-form-label">Frekuensi Nafas</label>
                    <b-input-group append="x/mnt">
                        <b-form-input type="number" v-model="form_item.rr" />
                    </b-input-group>
                </div>
                <div class="col-lg-4">
                    <label class="c-form-label">Suhu</label>
                    <b-input-group append="ᵒC">
                        <b-form-input type="number" v-model="form_item.temperature" />
                    </b-input-group>
                </div>
            </div>

            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button @click="validationItem" :disabled="active.disabled_item" variant="primary">Simpan</b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>
<script>
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import vSelect from 'vue-select'
import moment from 'moment'
import store from '@/store'


import { Line as LineChart } from 'vue-chartjs/legacy'
import {
    Chart as ChartJS, Title, Tooltip, Legend,
    LineElement, PointElement, CategoryScale, LinearScale, Filler,
} from 'chart.js'
ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement, Filler)

// Draws the first/last value near the ends of the "value" line on the mini
// trend charts, mirroring the reference design's endpoint labels.
const endpointLabelPlugin = {
    id: 'tcEndpointLabel',
    afterDatasetsDraw(chart) {
        const valueDatasetIndex = chart.data.datasets.findIndex(d => d._tcValueLine)
        if (valueDatasetIndex === -1) return
        const meta = chart.getDatasetMeta(valueDatasetIndex)
        if (!meta.data.length) return
        const dataset = chart.data.datasets[valueDatasetIndex]
        const { ctx } = chart
        ctx.save()
        ctx.font = '600 10px sans-serif'
        ctx.fillStyle = '#8A8F9B'
        ctx.textAlign = 'center'
        const first = meta.data[0]
        const firstVal = dataset.data[0]
        if (firstVal !== undefined && firstVal !== null) {
            ctx.fillText(firstVal, first.x, first.y - 10)
        }
        if (meta.data.length > 1) {
            const last = meta.data[meta.data.length - 1]
            const lastVal = dataset.data[dataset.data.length - 1]
            if (lastVal !== undefined && lastVal !== null) {
                ctx.fillText(lastVal, last.x, last.y - 10)
            }
        }
        ctx.restore()
    }
}
ChartJS.register(endpointLabelPlugin)


export default {
    name: 'TemperatureChart',
    components: {
         ModernLabel, vSelect, LineChart
    },
    props: {
        id: {
            type: [String, Number],
            default: null
        }
    },
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            reg_des_id: '',
            regData: {},
            active: {
                modal_main: false,
                disabled_main: false,
                modal_item: false,
                disabled_item: false,
            },
            params: {
                limit: 20,
                offset: 0,
                registration_destination_id: ''
            },
            pageOptions: [20, 50, 100],
            currentPage:1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            isDisabledFilter: false,
            totalRows: 0,

            form_temperature: {
                id: '',
                registration_destination_id: '',
                employee_nurse_id: '',
                monitoring_date: moment(new Date()).format('YYYY-MM-DD'),
                diet: '',
                defecation_form: '',
                defecation_frequency: '',
                intake: '',
                output: '',
                systolic_morning: '',
                diastolic_morning: '',
                systolic_afternoon: '',
                diastolic_afternoon: '',
                systolic_evening: '',
                diastolic_evening: '',
            },

            form_item: {
                id: '',
                temperature_chart_id: '',
                observation_hours: moment(new Date()).format('HH:mm'),
                rr: '',
                pulse: '',
                temperature: '',
            },
            temperature_list: [],
            ItemFields: [
                { key: 'observation_hours', label: 'Waktu' },
                { key: 'pulse', label: 'Nadi' },
                { key: 'rr', label: 'Frekuensi Nafas' },
                { key: 'temperature', label: 'Suhu' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 140px' },
            ],

            // Options shared by every mini trend chart (axes/legend hidden,
            // scale auto-fits so the normal-range band and the real reading
            // both stay visible).
            miniChartOptions: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { top: 16, right: 4, bottom: 4, left: 4 } },
                scales: {
                    y: { display: false },
                    x: { display: false },
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true },
                },
            },

            chartData: {
                labels: [],
                datasets: [
                    {
                        label: 'Suhu (°C)',
                        data: [],
                        borderColor: '#648DE1',
                        backgroundColor: 'rgba(100, 141, 225, 0.12)',
                        pointBackgroundColor: '#648DE1',
                        yAxisID: 'ySuhu',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        borderWidth: 2,
                    },
                    {
                        label: 'Nadi (x/mnt)',
                        data: [],
                        borderColor: '#DC3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.08)',
                        pointBackgroundColor: '#DC3545',
                        yAxisID: 'yVital',
                        fill: false,
                        tension: 0.3,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        borderWidth: 2,
                    },
                    {
                        label: 'Pernafasan (x/mnt)',
                        data: [],
                        borderColor: '#0D9488',
                        backgroundColor: 'rgba(13, 148, 136, 0.08)',
                        pointBackgroundColor: '#0D9488',
                        yAxisID: 'yVital',
                        fill: false,
                        tension: 0.3,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        borderWidth: 2,
                        borderDash: [4, 3],
                    },
                ]
            },
            chartOptions: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                layout: {
                    padding: { top: 8, right: 8, bottom: 0, left: 0 }
                },
                scales: {
                    ySuhu: {
                        type: 'linear',
                        position: 'left',
                        suggestedMin: 34,
                        suggestedMax: 41,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        title: { display: true, text: 'Suhu (°C)', font: { size: 11 } },
                        ticks: { font: { size: 11 } },
                    },
                    yVital: {
                        type: 'linear',
                        position: 'right',
                        suggestedMin: 0,
                        suggestedMax: 140,
                        grid: { drawOnChartArea: false },
                        title: { display: true, text: 'Nadi / Nafas (x/mnt)', font: { size: 11 } },
                        ticks: { font: { size: 11 } },
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 10 },
                            maxRotation: 0,
                            autoSkip: true,
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            boxWidth: 10,
                            boxHeight: 10,
                            usePointStyle: true,
                            font: { size: 12 },
                        },
                    },
                    tooltip: {
                        backgroundColor: '#09101E',
                        padding: 10,
                        titleFont: { size: 12 },
                        bodyFont: { size: 12 },
                        cornerRadius: 6,
                    },
                }
            },

            params_range: {
                date_start: moment().subtract(1, 'month').format('YYYY-MM-DD'),
                date_end: moment(new Date()).format('YYYY-MM-DD'),
                registration_destination_id: ''
            },

        }
    },

    watch: {
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.getTemperature()
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.getTemperature()
            }
        },
    },

    methods: {

        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        localDate(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return "";
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return "";
            }

            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },

        parsed_age(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return "";
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return "";
            }

            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();

            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = currentDate.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = currentDate.diff(birthDate, 'days');

            return `${years} th ${months} bln ${days} hr`;
        },

        getData(regDesId) {
			store.dispatch('registration/getById', regDesId).then(data => {
                this.regData = data[0]
            })
        },

        // Builds the {labels, datasets} object for a single mini trend chart:
        // a translucent band marking the normal range, plus the actual reading line.
        miniChartData(item, field, normalMin, normalMax, color) {
            const rows = [...(item.temperature_chart_items || [])].sort((a, b) => {
                return (a.observation_hours || '').localeCompare(b.observation_hours || '')
            })
            const labels = rows.map(r => r.observation_hours)
            const values = rows.map(r => r[field] !== undefined && r[field] !== null ? Number(r[field]) : null)

            return {
                labels,
                datasets: [
                    {
                        label: '_bandTop',
                        data: labels.map(() => normalMax),
                        borderWidth: 0,
                        pointRadius: 0,
                        fill: false,
                    },
                    {
                        label: '_bandBottom',
                        data: labels.map(() => normalMin),
                        borderWidth: 0,
                        pointRadius: 0,
                        fill: '-1',
                        backgroundColor: 'rgba(46, 204, 113, 0.15)',
                    },
                    {
                        label: field,
                        data: values,
                        borderColor: color,
                        backgroundColor: color,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: color,
                        pointBorderWidth: 2,
                        borderWidth: 2,
                        pointRadius: (ctx) => (ctx.dataIndex === 0 || ctx.dataIndex === values.length - 1) ? 4 : 0,
                        tension: 0,
                        fill: false,
                        _tcValueLine: true,
                    },
                ],
            }
        },

        openModalMain() {
            this.clearFormTemperature()
            this.active.modal_main = true
        },

        clearFormTemperature() {
            this.form_temperature.id = ''
            this.form_temperature.registration_destination_id = ''
            this.form_temperature.employee_nurse_id = ''
            this.form_temperature.monitoring_date = moment(new Date()).format('YYYY-MM-DD')
            this.form_temperature.diet = ''
            this.form_temperature.defecation_form = ''
            this.form_temperature.defecation_frequency = ''
            this.form_temperature.intake = ''
            this.form_temperature.output = ''
            this.form_temperature.systolic_morning = ''
            this.form_temperature.diastolic_morning = ''
            this.form_temperature.systolic_afternoon = ''
            this.form_temperature.diastolic_afternoon = ''
            this.form_temperature.systolic_evening = ''
            this.form_temperature.diastolic_evening = ''
        },

        validationTemperature() {
            if (this.form_temperature.id == '') {
                this.form_temperature.registration_destination_id = this.reg_des_id
                this.form_temperature.employee_nurse_id = this.userData.employee_id
            }

            try {
                this.active.disabled_main = true
                store.dispatch('temperaturechart/postPutTemperature', this.form_temperature)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_main = false
                        this.clearFormTemperature()
                        this.getTemperature()
                        this.getTCRange()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)
            } finally {
                this.active.disabled_main = false
            }
        },

        onEditTemperature(obj) {
            this.openModalMain()

            this.form_temperature.id = obj?.id || ''
            this.form_temperature.registration_destination_id = obj?.registration_destination_id || ''
            this.form_temperature.employee_nurse_id = obj?.employee_nurse_id || ''
            this.form_temperature.monitoring_date = obj?.monitoring_date || ''
            this.form_temperature.diet = obj?.diet || ''
            this.form_temperature.defecation_form = obj?.defecation_form || ''
            this.form_temperature.defecation_frequency = obj?.defecation_frequency || ''
            this.form_temperature.intake = obj?.intake || ''
            this.form_temperature.output = obj?.output || ''
            this.form_temperature.systolic_morning = obj?.systolic_morning || ''
            this.form_temperature.diastolic_morning = obj?.diastolic_morning || ''
            this.form_temperature.systolic_afternoon = obj?.systolic_afternoon || ''
            this.form_temperature.diastolic_afternoon = obj?.diastolic_afternoon || ''
            this.form_temperature.systolic_evening = obj?.systolic_evening || ''
            this.form_temperature.diastolic_evening = obj?.diastolic_evening || ''
        },

        getTemperature() {
            this.params.registration_destination_id = this.reg_des_id
            try {
                store.dispatch('temperaturechart/getListTemperature', this.params).then((response) => {
                    this.temperature_list = response.rows
                    this.totalRows = response.iTotal
                }, error => {
                    console.error('Error while GET temperaturechart/getListTemperature at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET temperaturechart/getListTemperature at: ', error);
            }
        },

        removeTemperature(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus monitoring tanggal ${obj.monitoring_date}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    store.dispatch('temperaturechart/deleteTemperature', obj.id)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.getTemperature()
                            this.getTCRange()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                }
            })
        },

        openModalItem(temperature_id) {
            this.clearFormItem()
            this.active.modal_item = true
            this.form_item.temperature_chart_id = temperature_id
        },

        clearFormItem() {
            this.form_item.id = ''
            this.form_item.temperature_chart_id = ''
            this.form_item.observation_hours = moment(new Date()).format('HH:mm')
            this.form_item.rr = ''
            this.form_item.pulse = ''
            this.form_item.temperature = ''
        },

        validationItem() {
            try {
                this.active.disabled_item = true
                store.dispatch('temperaturechart/postPutItemTemperature', this.form_item)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_item = false
                        this.clearFormItem()
                        this.getTemperature()
                        this.getTCRange()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)
            } finally {
                this.active.disabled_item = false
            }
        },

        onEditItem(obj) {
            this.openModalItem()

            this.form_item.id = obj?.id || ''
            this.form_item.temperature_chart_id = obj?.temperature_chart_id || ''
            this.form_item.observation_hours = obj?.observation_hours || ''
            this.form_item.rr = obj?.rr || ''
            this.form_item.pulse = obj?.pulse || ''
            this.form_item.temperature = obj?.temperature || ''
        },

        removeItemTemperature(obj, date) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus monitoring jam ${obj.observation_hours} pada tanggal ${date}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    store.dispatch('temperaturechart/deleteItemTemperature', obj.id)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.getTemperature()
                            this.getTCRange()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                }
            })
        },

        getTCRange() {
            this.params_range.registration_destination_id = this.reg_des_id
            this.chartData.labels = []
            this.chartData.datasets[0].data = []
            this.chartData.datasets[1].data = []
            this.chartData.datasets[2].data = []

            try {
                store.dispatch('temperaturechart/getListTemperatureV2', this.params_range).then((response) => {
                    let temp_data = response

                    if (temp_data?.length > 0) {
                        for (let i = 0; i < temp_data.length; i++) {
                            const el = temp_data[i];
                            this.chartData.datasets[0].data.push(el.temperature)
                            this.chartData.datasets[1].data.push(el.pulse)
                            this.chartData.datasets[2].data.push(el.rr)
                            let temp_label = `${moment(el.monitoring_date).format('DD/MM')} ${el.observation_hours?.substring(0,5) || ''}`
                            this.chartData.labels.push(temp_label)
                        }
                    }
                }, error => {
                    console.error('Error while GET temperaturechart/getListTemperatureV2 at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET temperaturechart/getListTemperatureV2 at: ', error);
            }
        }

    },

    created () {
        if (this.id) {
            this.reg_des_id = this.id
            this.getTemperature()
            this.getTCRange()
        }
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';

.tc-empty-list {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #B0B4BF;
    padding: 40px 0;
    font-size: 13px;
}

.tc-card {
    background: #fff;
    border: 1px solid #EEF0F5;
    border-radius: 10px;
    padding: 18px 20px;
    margin-bottom: 20px;
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
}

.tc-card-header {
    padding-bottom: 14px;
    margin-bottom: 14px;
    border-bottom: 1px solid #F1F2F6;
}

.tc-badge {
    display: inline-block;
    background: #EAF0FF;
    color: #3D5AFE;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    margin-bottom: 6px;
}

.tc-card-title {
    font-size: 15px;
    font-weight: 700;
    color: #09101E;
}

.tc-subtext {
    font-size: 12px;
    color: #8A8F9B;
    display: flex;
    align-items: center;
    margin-top: 2px;
}

.tc-header-actions {
    margin-top: 4px;
}

.tc-grid-row {
    margin-top: 16px;
}

.tc-section-title {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.04em;
    color: #3D5AFE;
    margin-bottom: 8px;
}

.tc-box-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.tc-box {
    background: #F8F9FB;
    border-radius: 8px;
    padding: 8px 12px;
    flex: 1 1 130px;
    min-width: 110px;
}

.tc-box-label {
    font-size: 11px;
    color: #8A8F9B;
    margin-bottom: 2px;
}

.tc-box-value {
    font-size: 14px;
    font-weight: 600;
    color: #09101E;
}

.tc-mini-chart-card {
    background: #fff;
    border: 1px solid #EEF0F5;
    border-radius: 8px;
    padding: 12px 14px 4px;
}

.tc-mini-title {
    font-size: 13px;
    font-weight: 700;
    color: #09101E;
}

.tc-mini-sub {
    font-size: 11px;
    font-weight: 400;
    color: #8A8F9B;
}

.tc-no-items {
    font-size: 12px;
    color: #B0B4BF;
    font-style: italic;
}

.tc-item-table {
    font-size: 13px;

    thead th {
        background-color: #F8F9FC;
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #6E7485;
        border-bottom: 2px solid #E9ECF2;
    }

    tbody td {
        vertical-align: middle;
        border-color: #EEF0F5;
    }
}

.tc-time {
    font-variant-numeric: tabular-nums;
    font-weight: 500;
}

.tc-unit {
    font-size: 11px;
    color: #8A8F9B;
}

.tc-icon-btn {
    padding: 0.4rem 0.55rem;
}

.tc-chart-card {
    background: #fff;
    border: 1px solid #EEF0F5;
    border-radius: 8px;
    padding: 20px;
}

.tc-chart-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #B0B4BF;
    padding: 40px 0;
    font-size: 13px;
}
</style>