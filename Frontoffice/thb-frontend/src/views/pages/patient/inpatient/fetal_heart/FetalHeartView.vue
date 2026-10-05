<template>
    <div>
        <section class="mt-24">
            <div class="chart-card">
                <div class="chart-card-header">
                    <div>
                        <div class="chart-title">Grafik Detak Jantung Janin (DJJ)</div>
                        <div class="chart-subtitle">Rentang normal: 110&ndash;160 bpm</div>
                    </div>
                    <div class="chart-legend">
                        <span class="legend-item"><span class="legend-dot dot-normal"></span>Normal</span>
                        <span class="legend-item"><span class="legend-dot dot-line"></span>DJJ</span>
                    </div>
                </div>

                <template v-if="fhrChart.points.length > 0">
                    <svg :viewBox="`0 0 ${fhrChart.width} ${fhrChart.height}`" class="chart-svg" preserveAspectRatio="none">
                        <!-- pita rentang normal 110-160 bpm -->
                        <rect
                            :x="fhrChart.paddingX"
                            :y="fhrChart.normalBandY"
                            :width="fhrChart.width - fhrChart.paddingX * 2"
                            :height="fhrChart.normalBandHeight"
                            class="normal-band"
                        />

                        <!-- garis grid horizontal -->
                        <line v-for="g in fhrChart.gridLines" :key="'g-'+g.value"
                            :x1="fhrChart.paddingX" :x2="fhrChart.width - fhrChart.paddingX"
                            :y1="g.y" :y2="g.y" class="grid-line" />
                        <text v-for="g in fhrChart.gridLines" :key="'gt-'+g.value"
                            :x="fhrChart.paddingX - 8" :y="g.y + 4" class="axis-label" text-anchor="end">{{ g.value }}</text>

                        <!-- garis DJJ -->
                        <polyline :points="fhrChart.polyline" class="fhr-line" fill="none" />

                        <!-- titik data -->
                        <g v-for="(p, idx) in fhrChart.points" :key="'p-'+idx">
                            <circle :cx="p.x" :cy="p.y" r="4" class="fhr-dot" />
                            <text :x="p.x" :y="fhrChart.height - 4" class="axis-label" text-anchor="middle">{{ p.label }}</text>
                        </g>
                    </svg>
                </template>
                <template v-else>
                    <div class="chart-empty">Belum ada data untuk ditampilkan</div>
                </template>
            </div>
        </section>

        <section class="mt-20">
            <div class="table-toolbar">
                <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-0">
                    <span class="text-regular-14">Tampilkan</span>
                    <b-form-select v-model="perPage" size="sm" :options="pageOptions" class="mx-1"/>
                    <span class="text-regular-14">Entri</span>
                </b-form-group>
                <b-button @click="openModalFetal" pill variant="primary" class="px-3">
                    <feather-icon icon="PlusIcon" class="mr-1" /> Tambah
                </b-button>
            </div>

            <div class="table-card mt-12">
                <b-table bordered head-variant="light" :items="fetal_list" :fields="fields" class="fetal-table mb-0" style="font-size: 13px;">
                    <template #cell(user)="row">
                        <div class="officer-name">{{ row.item.employee_nurse_name }}</div>
                        <div class="officer-date">{{ row.item.date_updated }}</div>
                    </template>

                    <template #cell(monitoring)="row">
                        <div class="monitor-grid">
                            <div class="monitor-item highlight">
                                <span class="monitor-label">DJJ</span>
                                <span class="monitor-value">{{ row.item.fetal_heart_rate || '-' }}<small> bpm</small></span>
                            </div>
                            <div class="monitor-item">
                                <span class="monitor-label">Tensi</span>
                                <span class="monitor-value">{{ row.item.systolic || '-' }}/{{ row.item.diastolic || '-' }}<small> mmHg</small></span>
                            </div>
                            <div class="monitor-item">
                                <span class="monitor-label">Ton. Uteri</span>
                                <span class="monitor-value">{{ row.item.uterine_tone || '-' }}</span>
                            </div>
                            <div class="monitor-item">
                                <span class="monitor-label">Kekuatan HIS</span>
                                <span class="monitor-value">{{ row.item.uterine_contractions || '-' }}</span>
                            </div>
                            <div class="monitor-item">
                                <span class="monitor-label">Lama HIS</span>
                                <span class="monitor-value">{{ row.item.uterine_contractions_duration || '-' }}</span>
                            </div>
                            <div class="monitor-item">
                                <span class="monitor-label">Interval</span>
                                <span class="monitor-value">{{ row.item.interval || '-' }}</span>
                            </div>
                        </div>
                    </template>

                    <template #cell(action_plan)="row">
                        <div class="action-plan-text" v-html="parsedBody(row.item.action_plan)"></div>
                    </template>

                    <template #cell(action)="row">
                        <div class="d-flex">
                            <b-button @click="onEditFetal(row.item)" size="sm" variant="warning-500" class="mr-1 action-btn">
                                <feather-icon icon="EditIcon" />
                            </b-button>
                            <b-button @click="removeFetal(row.item)" size="sm" variant="danger-500" class="action-btn">
                                <feather-icon icon="TrashIcon" />
                            </b-button>
                        </div>
                    </template>
                </b-table>
            </div>

            <div class="col px-0 mt-16">
                <div class="d-flex align-items-center">
                    <span class="text-regular-14 text-neutral-500">Menampilkan {{ firstRows }} hingga <template v-if="lastRows < totalRows">{{ lastRows }}</template> <template v-else>{{ totalRows }}</template> dari {{ totalRows }} entri</span>
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

        <b-modal v-model="active.modal_fetal" title="Observasi Detak Jantung Janin" size="lg" no-close-on-backdrop>
            <div class="row">
                <div class="col-lg-4">
                    <label class="c-form-label">Tanggal</label>
                    <b-form-input v-model="form_fetal.observation_date" type="datetime-local" />
                </div>
                <div class="col-lg-4 mt-10 mt-lg-0">
                    <label class="c-form-label">Systolic</label>
                    <b-input-group append="mmHg">
                        <b-form-input type="number" autocomplete="off" v-model="form_fetal.systolic" />
                    </b-input-group>
                </div>
                <div class="col-lg-4 mt-10 mt-lg-0">
                    <label class="c-form-label">Diastolic</label>
                    <b-input-group append="mmHg">
                        <b-form-input type="number" autocomplete="off" v-model="form_fetal.diastolic" />
                    </b-input-group>
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Ton. Uteri</label>
                    <b-form-input autocomplete="off" v-model="form_fetal.uterine_tone" />
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Kekuatan HIS</label>
                    <b-form-input autocomplete="off" v-model="form_fetal.uterine_contractions" />
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Lama HIS</label>
                    <b-form-input autocomplete="off" v-model="form_fetal.uterine_contractions_duration" />
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Interval</label>
                    <b-form-input autocomplete="off" v-model="form_fetal.interval" />
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Detak Janjung Janin</label>
                    <b-form-input type="number" autocomplete="off" v-model="form_fetal.fetal_heart_rate" />
                </div>
                <div class="col-12 mt-10">
                    <label class="c-form-label">Rencana Tindakan</label>
                    <b-textarea v-model="form_fetal.action_plan" />
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button @click="validationFetal" :disabled="active.disabledFetal" variant="primary" class="px-3">Simpan</b-button>
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
// Import store langsung sebagai fallback: this.$store / this.$root.$store
// terbukti undefined saat komponen ini dirender di dalam b-modal (lihat debug log).
// Sesuaikan path berikut dengan lokasi file store di project (mis. '@/store' atau '@/store/index').
import store from '@/store'

export default {
    name: 'FetalHeart',
    components: {
        ModernLabel, vSelect
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
            fetal_list: [],
            active: {
                modal_fetal: false,
                disabledFetal: false,
            },
            params: {
                offset: 0,
                limit: 20,
                registration_destination_id: ''
            },
            pageOptions: [20, 50, 100],
            currentPage:1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            isDisabledFilter: false,
            totalRows: 0,
            form_fetal: {
                id: '',
                registration_destination_id: '',
                employee_nurse_id: '',
                observation_date: moment(new Date()).format('YYYY-MM-DD HH:MM'),
                systolic: '',
                diastolic: '',
                uterine_tone: '',
                uterine_contractions: '',
                uterine_contractions_duration: '',
                interval: '',
                fetal_heart_rate: '',
                action_plan: '',
            },
            fields: [
                { key: 'user', label: 'Petugas', thStyle: 'width: 220px' },
                { key: 'observation_date', label: 'Tanggal Observasi', thStyle: 'width: 150px' },
                { key: 'monitoring', label: 'Monitoring' },
                { key: 'action_plan', label: 'Rencana tindakan', thStyle: 'width: 260px' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 90px' },
            ],
        }
    },
    computed: {
        // Data DJJ diurutkan dari observasi paling lama ke paling baru untuk grafik
        fhrSeries() {
            return [...this.fetal_list]
                .filter(item => item.fetal_heart_rate !== '' && item.fetal_heart_rate !== null && item.fetal_heart_rate !== undefined)
                .sort((a, b) => new Date(a.observation_date) - new Date(b.observation_date))
                .map(item => ({
                    value: Number(item.fetal_heart_rate),
                    label: item.observation_date ? moment(item.observation_date).format('DD/MM HH:mm') : ''
                }))
        },

        fhrChart() {
            const width = 760
            const height = 220
            const paddingX = 40
            const paddingY = 24
            const series = this.fhrSeries

            if (series.length === 0) {
                return { points: [], polyline: '', width, height, paddingX, paddingY, gridLines: [], normalBandY: 0, normalBandHeight: 0 }
            }

            const values = series.map(d => d.value)
            const maxVal = Math.max(...values, 160) + 10
            const minVal = Math.max(Math.min(...values, 110) - 10, 0)
            const range = (maxVal - minVal) || 1

            const plotWidth = width - paddingX * 2
            const plotHeight = height - paddingY * 2
            const stepX = series.length > 1 ? plotWidth / (series.length - 1) : 0

            const toY = (val) => height - paddingY - ((val - minVal) / range) * plotHeight

            const points = series.map((d, i) => ({
                x: paddingX + i * stepX,
                y: toY(d.value),
                value: d.value,
                label: d.label
            }))

            const polyline = points.map(p => `${p.x},${p.y}`).join(' ')

            // garis grid horizontal pada beberapa nilai acuan
            const gridValues = Array.from(new Set([minVal, 110, 160, maxVal].map(v => Math.round(v)))).sort((a, b) => a - b)
            const gridLines = gridValues.map(v => ({ value: v, y: toY(v) }))

            return {
                points,
                polyline,
                width,
                height,
                paddingX,
                paddingY,
                gridLines,
                normalBandY: toY(160),
                normalBandHeight: toY(110) - toY(160)
            }
        }
    },
    watch: {
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.getFetalList()
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.getFetalList()
            }
        },
        // Jika komponen pemanggil mengganti prop id secara dinamis
        // (tanpa re-mount komponen ini), data ikut di-refresh
        id: {
            immediate: false,
            handler: function(value) {
                if (value) {
                    this.reg_des_id = value
                    this.getFetalList()
                }
            }
        }
    },

    methods: {
        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
        },
        
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

        openModalFetal() {
            this.resetFormFetal()
            this.active.modal_fetal = true
        },

        resetFormFetal() {
            this.form_fetal.id = ''
            this.form_fetal.registration_destination_id = ''
            this.form_fetal.employee_nurse_id = ''
            this.form_fetal.observation_date = moment(new Date()).format('YYYY-MM-DD HH:MM')
            this.form_fetal.systolic = ''
            this.form_fetal.diastolic = ''
            this.form_fetal.uterine_tone = ''
            this.form_fetal.uterine_contractions = ''
            this.form_fetal.uterine_contractions_duration = ''
            this.form_fetal.interval = ''
            this.form_fetal.fetal_heart_rate = ''
            this.form_fetal.action_plan = ''
        },

        validationFetal() {
            this.form_fetal.registration_destination_id = this.reg_des_id
            this.form_fetal.employee_nurse_id = this.userData.employee_id

            try {
                this.active.disabledFetal = true
                store.dispatch('fetalheart/postPutFetalHeart', this.form_fetal)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_fetal = false
                        this.active.disabledFetal = false
                        this.resetFormFetal()
                        this.getFetalList()
                    } 
                )
                .catch(({error, response}) => {
                    this.notification('Failed','CloseIcon','danger',`${error}`)
                    this.active.disabledFetal = false
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
                this.active.disabledFetal = false
            }
        },

        getFetalList(regDesId) {
            this.params.registration_destination_id = this.reg_des_id

            try {                
                store.dispatch('fetalheart/getListFetalHeart', this.params).then((response) => {
                    this.fetal_list = response.rows
                    this.totalRows = response.iTotal
            }, error => {
                    console.error('Error while GET fetalheart/getListFetalHeart at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET fetalheart/getListFetalHeart at: ', error);
            }
        },

        onEditFetal(obj) {
            this.openModalFetal()

            this.form_fetal.id = obj.id ? obj.id : ''
            this.form_fetal.registration_destination_id = obj.registration_destination_id ? obj.registration_destination_id : ''
            this.form_fetal.employee_nurse_id = obj.employee_nurse_id ? obj.employee_nurse_id : ''
            this.form_fetal.observation_date = obj.observation_date ? obj.observation_date : ''
            this.form_fetal.systolic = obj.systolic ? obj.systolic : ''
            this.form_fetal.diastolic = obj.diastolic ? obj.diastolic : ''
            this.form_fetal.uterine_tone = obj.uterine_tone ? obj.uterine_tone : ''
            this.form_fetal.uterine_contractions = obj.uterine_contractions ? obj.uterine_contractions : ''
            this.form_fetal.uterine_contractions_duration = obj.uterine_contractions_duration ? obj.uterine_contractions_duration : ''
            this.form_fetal.interval = obj.interval ? obj.interval : ''
            this.form_fetal.fetal_heart_rate = obj.fetal_heart_rate ? obj.fetal_heart_rate : ''
            this.form_fetal.action_plan = obj.action_plan ? obj.action_plan : ''
        },


        removeFetal(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus observasi tanggal ${obj.observation_date} dari ${obj.employee_nurse_name} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    store.dispatch('fetalheart/deleteFetalHeart', obj.id)
                    .then(
                        response => {
                            if (response.status === 200 || response.status === 201) {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getFetalList()
                            } else {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            }
                        },
                        error => {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            console.error('Error While Delete Using fetalheart/deleteFetalHeart at: ', error)
                        }
                    )
                    .catch(({error, response}) => {
                        this.notification('Failed','CloseIcon','danger',`${error}`)
                    })
                }
            })
        },
    },
   
    created () {
        if (this.id) {
            this.reg_des_id = this.id
            this.getFetalList()
        }
    }
}
</script>
<style scoped>
/* ===== Chart card ===== */
.chart-card {
    background: #fff;
    border: 1px solid #edf0f4;
    border-radius: 12px;
    padding: 16px 20px;
    box-shadow: 0 1px 3px rgba(16, 24, 40, 0.04);
}
.chart-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
    flex-wrap: wrap;
    gap: 8px;
}
.chart-title {
    font-size: 14px;
    font-weight: 700;
    color: #1d2939;
}
.chart-subtitle {
    font-size: 12px;
    color: #667085;
    margin-top: 2px;
}
.chart-legend {
    display: flex;
    gap: 14px;
}
.legend-item {
    font-size: 12px;
    color: #475467;
    display: flex;
    align-items: center;
    gap: 6px;
}
.legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
}
.dot-normal { background: #d1fadf; border: 1px solid #6ce9a6; }
.dot-line { background: #e6262a; }

.chart-svg {
    width: 100%;
    height: 220px;
    display: block;
}
.normal-band {
    fill: #ecfdf3;
}
.grid-line {
    stroke: #eaecf0;
    stroke-width: 1;
}
.axis-label {
    font-size: 9px;
    fill: #98a2b3;
}
.fhr-line {
    stroke: #e6262a;
    stroke-width: 2;
}
.fhr-dot {
    fill: #ffffff;
    stroke: #e6262a;
    stroke-width: 2;
}
.chart-empty {
    text-align: center;
    color: #98a2b3;
    font-size: 13px;
    padding: 40px 0;
}

/* ===== Table toolbar & card ===== */
.table-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.table-card {
    background: #fff;
    border: 1px solid #edf0f4;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(16, 24, 40, 0.04);
}
.fetal-table :deep(table) {
    margin-bottom: 0;
}
.fetal-table :deep(thead th) {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    color: #667085;
    background: #f9fafb;
    border-bottom: 1px solid #edf0f4;
    vertical-align: middle;
}
.fetal-table :deep(tbody td) {
    vertical-align: middle;
    border-color: #f0f2f5;
}
.fetal-table :deep(tbody tr:hover) {
    background-color: #f9fafb;
}

/* ===== Officer / petugas cell ===== */
.officer-name {
    font-weight: 600;
    color: #1d2939;
}
.officer-date {
    color: #667085;
    font-size: 12px;
    margin-top: 2px;
}

/* ===== Monitoring grid ===== */
.monitor-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(110px, 1fr));
    gap: 8px;
}
.monitor-item {
    background: #f9fafb;
    border: 1px solid #f0f2f5;
    border-radius: 8px;
    padding: 6px 10px;
    display: flex;
    flex-direction: column;
    line-height: 1.3;
}
.monitor-label {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    color: #98a2b3;
}
.monitor-value {
    font-size: 13px;
    font-weight: 600;
    color: #1d2939;
}
.monitor-value small {
    font-weight: 400;
    color: #667085;
    font-size: 11px;
}
.monitor-item.highlight {
    background: #fef3f2;
    border-color: #fecdca;
}
.monitor-item.highlight .monitor-value {
    color: #d92d20;
}

/* ===== Action plan cell ===== */
.action-plan-text {
    color: #344054;
    font-size: 13px;
    line-height: 1.4;
}

/* ===== Action buttons ===== */
.action-btn {
    border-radius: 6px;
}
</style>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>