<template>
    <div>
        <section class="mt-32">
            <div class="d-flex justify-content-end">
                <b-button @click="openModalTransfusion" size="sm" variant="primary">
                    <feather-icon icon="PlusIcon" class="mr-1"/> Tambah
                </b-button>
            </div>
        </section>

        <template v-if="transfusion_list && transfusion_list.length > 0">
            <template v-for="(item, index) in transfusion_list">
                <section class="bag-card mt-20" :key="'bag-'+index">
                    <div class="bag-card-body">
                        <div class="bag-card-header">
                            <div class="bag-badge">Kantong ke-{{ item.bag }}</div>
                            <div class="bag-header-text">
                                <div class="bag-title">No. Kantong: {{ item.bag_number }}</div>
                                <div class="bag-meta">Ditambahkan {{ item.date_added }} oleh {{ item.employee_nurse_name }}</div>
                            </div>
                        </div>

                        <div class="d-flex mt-12 flex-wrap bag-actions">
                            <b-button pill size="sm" variant="primary" class="px-16 mr-2 mb-2" @click="onEditTransfusion(item)">
                                <feather-icon icon="EditIcon" class="mr-1" /> Edit
                            </b-button>
                            <b-button pill size="sm" variant="danger" class="px-16 mr-2 mb-2" @click="removeTransfusion(item)">
                                <feather-icon icon="TrashIcon" class="mr-1" /> Hapus
                            </b-button>
                            <b-button @click="openModalItem(item.id)" pill variant="outline-success" size="sm" class="px-16 mb-2">
                                <feather-icon icon="PlusIcon" class="mr-1" />Monitoring
                            </b-button>
                        </div>

                        <div class="row mt-16">
                            <div class="col-lg-6">
                                <div class="info-section-title">Transfusi Darah</div>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <span class="info-label">Tetes</span>
                                        <span class="info-value">{{ item.bag_drops || '-' }} <small>tetes/mnt</small></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Jenis Darah</span>
                                        <span class="info-value">{{ item.blood_group || '-' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Kecepatan</span>
                                        <span class="info-value">{{ item.blood_velocity || '-' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Gol. Darah</span>
                                        <span class="info-value">{{ item.blood_type || '-' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Jumlah</span>
                                        <span class="info-value">{{ item.blood_amount || '-' }} <small>cc</small></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Kadaluarsa</span>
                                        <span class="info-value">{{ item.expired_date || '-' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Tgl/Jam Mulai</span>
                                        <span class="info-value">{{ item.start_datetime || '-' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Tgl/Jam Selesai</span>
                                        <span class="info-value">{{ item.finish_datetime || '-' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 mt-16 mt-lg-0">
                                <div class="info-section-title">Stiker Pasien</div>

                                <div class="reaction-badges" v-if="reactionLabels(item.reaction_arry).length">
                                    <span v-for="(r, ri) in reactionLabels(item.reaction_arry)" :key="'r-'+ri" class="reaction-badge">{{ r }}</span>
                                </div>
                                <div class="reaction-badges" v-else>
                                    <span class="reaction-badge muted">Tidak ada reaksi</span>
                                </div>

                                <div class="info-grid mt-8">
                                    <div class="info-item">
                                        <span class="info-label">Reaksi Lainnya</span>
                                        <span class="info-value">{{ item.reaction || '-' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Waktu Reaksi</span>
                                        <span class="info-value">{{ item.reaction_time || '-' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Transfusi</span>
                                        <span class="info-value">{{ item.transfusion_process_type_id ? (item.transfusion_process_type_id == '1' ? 'Stop' : 'Dilanjutkan') : '-' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Waktu Transfusi</span>
                                        <span class="info-value">{{ item.transfusion_process_time || '-' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Intake</span>
                                        <span class="info-value">{{ item.intake || '-' }} <small>ml</small></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Output</span>
                                        <span class="info-value">{{ item.output || '-' }} <small>ml</small></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="item.blood_transfusion_items && item.blood_transfusion_items.length > 0" class="row mt-20">
                            <div class="col-lg-6">
                                <div class="mini-chart-card">
                                    <div class="mini-chart-title">Tren Suhu <span class="mini-chart-sub">(normal 36&ndash;37,5 ᵒC)</span></div>
                                    <svg :viewBox="`0 0 ${bagCharts[item.id].temperature.width} ${bagCharts[item.id].temperature.height}`" class="mini-chart-svg" preserveAspectRatio="none">
                                        <rect
                                            :x="bagCharts[item.id].temperature.paddingX"
                                            :y="bagCharts[item.id].temperature.normalBandY"
                                            :width="bagCharts[item.id].temperature.width - bagCharts[item.id].temperature.paddingX * 2"
                                            :height="bagCharts[item.id].temperature.normalBandHeight"
                                            class="normal-band"
                                        />
                                        <line v-for="g in bagCharts[item.id].temperature.gridLines" :key="'tg-'+g.value"
                                            :x1="bagCharts[item.id].temperature.paddingX" :x2="bagCharts[item.id].temperature.width - bagCharts[item.id].temperature.paddingX"
                                            :y1="g.y" :y2="g.y" class="grid-line" />
                                        <text v-for="g in bagCharts[item.id].temperature.gridLines" :key="'tgt-'+g.value"
                                            :x="bagCharts[item.id].temperature.paddingX - 6" :y="g.y + 3" class="axis-label" text-anchor="end">{{ g.value }}</text>
                                        <polyline :points="bagCharts[item.id].temperature.polyline" class="vital-line temp-line" fill="none" />
                                        <g v-for="(p, pi) in bagCharts[item.id].temperature.points" :key="'tp-'+pi">
                                            <circle :cx="p.x" :cy="p.y" r="3.5" class="vital-dot temp-dot" />
                                        </g>
                                    </svg>
                                </div>
                            </div>
                            <div class="col-lg-6 mt-16 mt-lg-0">
                                <div class="mini-chart-card">
                                    <div class="mini-chart-title">Tren Nadi <span class="mini-chart-sub">(normal 60&ndash;100 x/mnt)</span></div>
                                    <svg :viewBox="`0 0 ${bagCharts[item.id].pulse.width} ${bagCharts[item.id].pulse.height}`" class="mini-chart-svg" preserveAspectRatio="none">
                                        <rect
                                            :x="bagCharts[item.id].pulse.paddingX"
                                            :y="bagCharts[item.id].pulse.normalBandY"
                                            :width="bagCharts[item.id].pulse.width - bagCharts[item.id].pulse.paddingX * 2"
                                            :height="bagCharts[item.id].pulse.normalBandHeight"
                                            class="normal-band"
                                        />
                                        <line v-for="g in bagCharts[item.id].pulse.gridLines" :key="'pg-'+g.value"
                                            :x1="bagCharts[item.id].pulse.paddingX" :x2="bagCharts[item.id].pulse.width - bagCharts[item.id].pulse.paddingX"
                                            :y1="g.y" :y2="g.y" class="grid-line" />
                                        <text v-for="g in bagCharts[item.id].pulse.gridLines" :key="'pgt-'+g.value"
                                            :x="bagCharts[item.id].pulse.paddingX - 6" :y="g.y + 3" class="axis-label" text-anchor="end">{{ g.value }}</text>
                                        <polyline :points="bagCharts[item.id].pulse.polyline" class="vital-line pulse-line" fill="none" />
                                        <g v-for="(p, pi) in bagCharts[item.id].pulse.points" :key="'pp-'+pi">
                                            <circle :cx="p.x" :cy="p.y" r="3.5" class="vital-dot pulse-dot" />
                                        </g>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div v-if="item.blood_transfusion_items && item.blood_transfusion_items.length > 0" class="table-card mt-20">
                            <b-table bordered fixed :fields="transfusionItemFields" :items="item.blood_transfusion_items" class="transfusion-table mb-0" style="font-size: 13px;">
                                <template #cell(temperature)="row">{{ row.value }} ᵒC</template>
                                <template #cell(blood_pressure)="row">{{ row.item.systolic }}/{{ row.item.diastolic }} mmHg</template>
                                <template #cell(pulse)="row">{{ row.value }} x/mnt</template>
                                <template #cell(respiratory_rate)="row">{{ row.value }} x/mnt</template>
                                <template #cell(action)="row">
                                    <b-button @click="onEditItem(row.item)" size="sm" variant="warning-500" class="mr-1 action-btn">
                                        <feather-icon icon="EditIcon" />
                                    </b-button>
                                    <b-button @click="removeItem(row.item)" size="sm" variant="danger-500" class="action-btn">
                                        <feather-icon icon="TrashIcon" />
                                    </b-button>
                                </template>
                            </b-table>
                        </div>
                        <div v-else class="empty-items mt-20">Belum ada data monitoring untuk kantong ini.</div>
                    </div>
                </section>
            </template>
        </template>

        <template v-else>
            <div class="empty-state mt-20">Belum ada data transfusi darah.</div>
        </template>

        <b-modal v-model="active.modal_main" size="lg" title="Tambah Kantong" no-close-on-backdrop>
            <div>
                <ModernLabel label="Monitoring Tranfusi darah" />
                <div class="row">
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Kantong Ke</label>
                        <b-form-input v-model="form_transfusion.bag" type="number" />
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Tetes</label>
                        <b-input-group append="tetes/mnt">
                            <b-form-input v-model="form_transfusion.bag_drops" type="number" />
                        </b-input-group>
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Jenis Darah</label>
                        <v-select v-model="form_transfusion.blood_group" :options="OptionsBloodGroup" :reduce="item => item.value" label="title" placeholder="-- Jenis darah --"></v-select>
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Kecepatan</label>
                        <b-input-group append="ml">
                            <b-form-input v-model="form_transfusion.blood_velocity" type="number" />
                        </b-input-group>
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Golongan Darah</label>
                        <v-select v-model="form_transfusion.blood_type" :options="OptionsBloodType" :reduce="item => item.value" label="title" placeholder="-- Jenis darah --"></v-select>
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Jumlah CC</label>
                        <b-input-group append="cc">
                            <b-form-input v-model="form_transfusion.blood_amount" type="number" />
                        </b-input-group>
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Nomor Kantong</label>
                        <b-form-input v-model="form_transfusion.bag_number" type="number" />
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Tanggal Kadaluarsa</label>
                        <b-form-input v-model="form_transfusion.expired_date" type="date" />
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Tgl/Jam Mulai</label>
                        <b-form-input v-model="form_transfusion.start_datetime" type="datetime-local" />
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Tgl/Jam Selesai</label>
                        <b-form-input v-model="form_transfusion.finish_datetime" type="datetime-local" />
                    </div>
                </div>
            </div>
            <div class="mt-24">
                <ModernLabel label="Stiker Pasien" />
                <div class="row">
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Reaksi</label>
                        <b-form-checkbox-group size="sm" v-model="form_transfusion.reaction_arry">
                            <b-form-checkbox value="a" class="col-6 mr-0 my-1">Menggigil</b-form-checkbox>
                            <b-form-checkbox value="b" class="col-6 mr-0 my-1">Sakit Kepala</b-form-checkbox>
                            <b-form-checkbox value="c" class="col-6 mr-0 my-1">Kulit Kemerahan</b-form-checkbox>
                            <b-form-checkbox value="d" class="col-6 mr-0 my-1">Urtikaria</b-form-checkbox>
                            <b-form-checkbox value="e" class="col-6 mr-0 my-1">Sesak nafas</b-form-checkbox>
                            <b-form-checkbox value="f" class="col-6 mr-0 my-1">Suhu &gt; 37,8ᵒC</b-form-checkbox>
                            <b-form-checkbox value="g" class="col-6 mr-0 my-1">Lainnya</b-form-checkbox>
                        </b-form-checkbox-group>
                        <b-form-input v-model="form_transfusion.reaction" class="mt-10" v-if="form_transfusion.reaction_arry.indexOf('g') !== -1" />
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Waktu Reaksi</label>
                        <b-form-input v-model="form_transfusion.reaction_time" type="time" />
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Tranfusi</label>
                        <v-select v-model="form_transfusion.transfusion_process_type_id" :options="OptionsTransfusion" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Waktu Tranfusi</label>
                        <b-form-input v-model="form_transfusion.transfusion_process_time" type="time" />
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Balance Cairan (Intake)</label>
                        <b-input-group append="ml">
                            <b-form-input v-model="form_transfusion.intake" type="number" />
                        </b-input-group>
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Balance Cairan (Output)</label>
                        <b-input-group append="ml">
                            <b-form-input v-model="form_transfusion.output" type="number" />
                        </b-input-group>
                    </div>
                </div>
            </div>

            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button @click="validationTransfusion" :disabled="active.disabled_main" variant="primary">Simpan</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal_item" size="lg" title="Tambah Kantong" no-close-on-backdrop>
            <div class="row">
                <div class="col-lg-4">
                    <label class="c-form-label">Waktu</label>
                    <b-form-input v-model="form_item.time" type="time" />
                </div>
                <div class="col-lg-4 mt-10 mt-lg-0">
                    <label class="c-form-label">Systolic</label>
                    <b-input-group append="mmHg">
                        <b-form-input type="number" autocomplete="off" v-model="form_item.systolic" />
                    </b-input-group>
                </div>
                <div class="col-lg-4 mt-10 mt-lg-0">
                    <label class="c-form-label">Diastolic</label>
                    <b-input-group append="mmHg">
                        <b-form-input type="number" autocomplete="off" v-model="form_item.diastolic" />
                    </b-input-group>
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Kesadaran</label>
                    <b-form-input v-model="form_item.consciousness" />
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Nadi</label>
                    <b-input-group append="x/mnt">
                        <b-form-input type="number" autocomplete="off" v-model="form_item.pulse" />
                    </b-input-group>
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Pernafasan</label>
                    <b-input-group append="x/mnt">
                        <b-form-input type="number" autocomplete="off" v-model="form_item.respiratory_rate" />
                    </b-input-group>
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Suhu</label>
                    <b-input-group append="ᵒC">
                        <b-form-input type="number" autocomplete="off" v-model="form_item.temperature" />
                    </b-input-group>
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Nyeri</label>
                    <b-form-input v-model="form_item.sore" />
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Kondisi Akses Intravena</label>
                    <b-form-input v-model="form_item.intravenous_access_conditions" />
                </div>
            </div>

            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button @click="validationItem" :disabled="active.disabled_main_" variant="primary">Simpan</b-button>
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

export default {
    name: 'BloodTransfusion',
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
            active: {
                modal_main: false,
                disabled_main: false,
                disabled_main_: false,
                modal_item: false,
                disabled_item: false,
            },
            params: {
                registration_destination_id: ''
            },
            OptionsBloodGroup: [
                { value: 'WB', title: 'WB' },
                { value: 'PRC', title: 'PRC' },
                { value: 'TC', title: 'BTC' },
            ],
            OptionsBloodType: [
                { value: 'A+', title: 'A+' },
                { value: 'A-', title: 'A-' },
                { value: 'B+', title: 'B+' },
                { value: 'B-', title: 'B-' },
                { value: 'AB+', title: 'AB+' },
                { value: 'AB-', title: 'AB-' },
                { value: 'O+', title: 'AB-' },
                { value: 'O-', title: 'AB-' },
            ],
            OptionsTransfusion: [
                { value: 1, title: 'Stop' },
                { value: 2, title: 'Dilanjutkan' },
            ],
            reactionMap: {
                a: 'Menggigil',
                b: 'Sakit Kepala',
                c: 'Kulit Kemerahan',
                d: 'Urtikaria',
                e: 'Sesak nafas',
                f: 'Suhu > 37,8ᵒC',
                g: 'Lainnya',
            },
            transfusion_list: [],
            form_transfusion: {
                id:'',
                registration_destination_id: '',
                employee_nurse_id: '',
                bag: '',
                bag_drops: '',
                blood_group: '',
                blood_velocity: '',
                blood_type: '',
                blood_amount: '',
                bag_number: '',
                expired_date: '',
                start_datetime: moment(new Date()).format('YYYY-MM-DD HH:mm'),
                finish_datetime: moment(new Date()).format('YYYY-MM-DD HH:mm'),
                reaction_arry: [],
                reaction: '',
                reaction_time: moment(new Date()).format('HH:mm'),
                transfusion_process_type_id: '',
                transfusion_process_time: moment(new Date()).format('HH:mm'),
                intake: '',
                output: ''
            },

            transfusionItemFields: [
                { key: 'time', label: 'Waktu' },
                { key: 'consciousness', label: 'Kesadaran' },
                { key: 'blood_pressure', label: 'Tekanan Darah' },
                { key: 'pulse', label: 'Nadi' },
                { key: 'respiratory_rate', label: 'Pernafasan' },
                { key: 'temperature', label: 'Suhu' },
                { key: 'sore', label: 'Nyeri' },
                { key: 'intravenous_access_conditions', label: 'Kondisi Akses Intravena' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 100px' },
            ],

            form_item: {
                id: '',
                blood_transfusion_id: '',
                time: moment(new Date()).format('HH:mm'),
                consciousness: '',
                blood_pressure: '',
                systolic: '',
                diastolic: '',
                pulse: '',
                respiratory_rate: '',
                temperature: '',
                sore: '',
                intravenous_access_conditions: ''
            }
        }
    },

    computed: {
        // Grafik tren Suhu & Nadi per kantong transfusi, dibangun sekali dari transfusion_list
        bagCharts() {
            const result = {}
            for (const bag of this.transfusion_list) {
                result[bag.id] = {
                    temperature: this.buildVitalChart(bag.blood_transfusion_items, 'temperature', 36, 37.5),
                    pulse: this.buildVitalChart(bag.blood_transfusion_items, 'pulse', 60, 100),
                }
            }
            return result
        }
    },

    watch: {
        "form_transfusion.reaction_arry"(newValues) {
            if (newValues && !newValues.includes("g")) {
                this.form_transfusion.reaction = ""
            }
        },
    },

    methods: {
        reactionLabels(arry) {
            if (!arry || arry.length === 0) return []
            return arry.map(code => this.reactionMap[code]).filter(Boolean)
        },

        // Membangun geometri chart SVG sederhana (tanpa library tambahan) untuk satu metrik vital
        buildVitalChart(items, key, normalMin, normalMax) {
            const width = 320
            const height = 140
            const paddingX = 32
            const paddingY = 16

            const series = (items || [])
                .filter(it => it[key] !== '' && it[key] !== null && it[key] !== undefined && !isNaN(Number(it[key])))
                .map(it => ({ value: Number(it[key]), label: it.time || '' }))

            if (series.length === 0) {
                return { points: [], polyline: '', width, height, paddingX, paddingY, gridLines: [], normalBandY: 0, normalBandHeight: 0 }
            }

            const values = series.map(d => d.value)
            const buffer = (normalMax - normalMin) * 0.3 || 5
            const maxVal = Math.max(...values, normalMax) + buffer
            const minVal = Math.max(Math.min(...values, normalMin) - buffer, 0)
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
            const gridValues = Array.from(new Set([minVal, normalMin, normalMax, maxVal].map(v => Math.round(v * 10) / 10))).sort((a, b) => a - b)
            const gridLines = gridValues.map(v => ({ value: v, y: toY(v) }))

            return {
                points,
                polyline,
                width,
                height,
                paddingX,
                paddingY,
                gridLines,
                normalBandY: toY(normalMax),
                normalBandHeight: toY(normalMin) - toY(normalMax)
            }
        },

        openModalTransfusion() {
            this.clearFormTransfusion()
            this.active.modal_main = true
        },

        clearFormTransfusion() {
            this.form_transfusion.id = ''
            this.form_transfusion.registration_destination_id = ''
            this.form_transfusion.employee_nurse_id = ''
            this.form_transfusion.bag = ''
            this.form_transfusion.bag_drops = ''
            this.form_transfusion.blood_group = ''
            this.form_transfusion.blood_velocity = ''
            this.form_transfusion.blood_type = ''
            this.form_transfusion.blood_amount = ''
            this.form_transfusion.bag_number = ''
            this.form_transfusion.expired_date = ''
            this.form_transfusion.start_datetime = moment(new Date()).format('YYYY-MM-DD HH:mm')
            this.form_transfusion.finish_datetime = moment(new Date()).format('YYYY-MM-DD HH:mm')
            this.form_transfusion.reaction_arry = []
            this.form_transfusion.reaction = ''
            this.form_transfusion.reaction_time = moment(new Date()).format('HH:mm')
            this.form_transfusion.transfusion_process_type_id = ''
            this.form_transfusion.transfusion_process_time = moment(new Date()).format('HH:mm')
            this.form_transfusion.intake = ''
            this.form_transfusion.output = ''
        },

        validationTransfusion() {
            if (this.form_transfusion.id == '') {
                this.form_transfusion.registration_destination_id = this.reg_des_id
                this.form_transfusion.employee_nurse_id = this.userData.employee_id
            }

            try {
                this.active.disabled_main = true
                store.dispatch('bloodtransfusion/postPutTransfusionMonitoring', this.form_transfusion)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_main = false
                        this.active.disabled_main = false
                        this.clearFormTransfusion()
                        this.getTransfusion()
                    } 
                )
                .catch(({error, response}) => {
                    this.notification('Failed','CloseIcon','danger',`${error}`)
                    this.active.disabled_main = false
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
                this.active.disabled_main = false
            }
        },

        getTransfusion() {
            this.params.registration_destination_id = this.reg_des_id
            try {                
                store.dispatch('bloodtransfusion/getListTransfusionMonitoring', this.params).then((response) => {
                    this.transfusion_list = response.rows
                }, error => {
                    console.error('Error while GET bloodtransfusion/getListTransfusionMonitoring at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET bloodtransfusion/getListTransfusionMonitoring at: ', error);
            }
        },

        onEditTransfusion(obj) {
            this.openModalTransfusion()
            
            this.form_transfusion.id = obj.id
            this.form_transfusion.registration_destination_id = obj.registration_destination_id
            this.form_transfusion.employee_nurse_id = obj.employee_nurse_id
            this.form_transfusion.bag = obj.bag
            this.form_transfusion.bag_drops = obj.bag_drops
            this.form_transfusion.blood_group = obj.blood_group
            this.form_transfusion.blood_velocity = obj.blood_velocity
            this.form_transfusion.blood_type = obj.blood_type
            this.form_transfusion.blood_amount = obj.blood_amount
            this.form_transfusion.bag_number = obj.bag_number
            this.form_transfusion.expired_date = obj.expired_date
            this.form_transfusion.start_datetime = obj.start_datetime
            this.form_transfusion.finish_datetime = obj.finish_datetime
            this.form_transfusion.reaction_arry = obj.reaction_arry
            this.form_transfusion.reaction = obj.reaction
            this.form_transfusion.reaction_time = obj.reaction_time
            this.form_transfusion.transfusion_process_type_id = obj.transfusion_process_type_id
            this.form_transfusion.transfusion_process_time = obj.transfusion_process_time
            this.form_transfusion.intake = obj.intake
            this.form_transfusion.output = obj.output
        },

        removeTransfusion(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus monitoring ${obj.employee_nurse_name} pada kantong nomor-${obj.bag_number}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    store.dispatch('bloodtransfusion/deleteTransfusionMonitoring', obj.id)
                    .then(
                        response => {
                            if (response.status === 200 || response.status === 201) {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getTransfusion()
                            } else {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            }
                        },
                        error => {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            console.error('Error While Delete Using bloodtransfusion/deleteTransfusionMonitoring at: ', error)
                        }
                    )
                    .catch(({error, response}) => {
                        this.notification('Failed','CloseIcon','danger',`${error}`)
                    })
                }
            })
        },

        openModalItem(transfusion_id) {
            this.clearFormItem()
            this.active.modal_item = true
            this.form_item.blood_transfusion_id = transfusion_id
        },

        clearFormItem() {
            this.form_item.id = ''
            this.form_item.blood_transfusion_id = ''
            this.form_item.time = moment(new Date()).format('HH:mm')
            this.form_item.consciousness = ''
            this.form_item.systolic = ''
            this.form_item.diastolic = ''
            this.form_item.pulse = ''
            this.form_item.respiratory_rate = ''
            this.form_item.temperature = ''
            this.form_item.sore = ''
            this.form_item.intravenous_access_conditions = ''
        },

        validationItem() {
            if (this.form_item.id == '') {
                this.form_item.registration_destination_id = this.reg_des_id
                this.form_item.employee_nurse_id = this.userData.employee_id
            }

            try {
                this.active.disabled_main_ = true
                store.dispatch('bloodtransfusion/postPutTransfusionItem', this.form_item)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_item = false
                        this.active.disabled_main_ = false
                        this.clearFormItem()
                        this.getTransfusion()
                    } 
                )
                .catch(({error, response}) => {
                    this.notification('Failed','CloseIcon','danger',`${error}`)
                    this.active.disabled_main_ = false
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
                this.active.disabled_main_ = false
            }
        },

        onEditItem(obj) {
            this.clearFormItem()
            this.active.modal_item = true

            this.form_item.id = obj.id
            this.form_item.blood_transfusion_id = obj.blood_transfusion_id
            this.form_item.time = obj.time
            this.form_item.consciousness = obj.consciousness
            this.form_item.systolic = obj.systolic
            this.form_item.diastolic = obj.diastolic
            this.form_item.pulse = obj.pulse
            this.form_item.respiratory_rate = obj.respiratory_rate
            this.form_item.temperature = obj.temperature
            this.form_item.sore = obj.sore
            this.form_item.intravenous_access_conditions = obj.intravenous_access_conditions
        },

        removeItem(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus monitoring jam ${obj.time} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    store.dispatch('bloodtransfusion/deleteTransfusionItem', obj.id)
                    .then(
                        response => {
                            if (response.status === 200 || response.status === 201) {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getTransfusion()
                            } else {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            }
                        },
                        error => {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            console.error('Error While Delete Using bloodtransfusion/deleteTransfusionItem at: ', error)
                        }
                    )
                    .catch(({error, response}) => {
                        this.notification('Failed','CloseIcon','danger',`${error}`)
                    })
                }
            })
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

    },
   
    created () {
        if (this.id) {
            this.reg_des_id = this.id
            this.getTransfusion()
        }
    }
}
</script>
<style scoped>
/* ===== Bag card ===== */
.bag-card {
    background: #fff;
    border: 1px solid #edf0f4;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(16, 24, 40, 0.04);
}
.bag-card-body {
    padding: 18px 20px;
}
.bag-card-header {
    display: flex;
    align-items: center;
    gap: 12px;
}
.bag-badge {
    flex-shrink: 0;
    background: #eef4ff;
    color: #3538cd;
    font-size: 12px;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 999px;
    white-space: nowrap;
}
.bag-title {
    font-size: 14px;
    font-weight: 700;
    color: #1d2939;
}
.bag-meta {
    font-size: 12px;
    color: #667085;
    margin-top: 2px;
}

/* ===== Info sections ===== */
.info-section-title {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: #3538cd;
    margin-bottom: 8px;
}
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 8px;
}
.info-item {
    background: #f9fafb;
    border: 1px solid #f0f2f5;
    border-radius: 8px;
    padding: 6px 10px;
    display: flex;
    flex-direction: column;
    line-height: 1.3;
}
.info-label {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    color: #98a2b3;
}
.info-value {
    font-size: 13px;
    font-weight: 600;
    color: #1d2939;
    word-break: break-word;
}
.info-value small {
    font-weight: 400;
    color: #667085;
    font-size: 11px;
}

/* ===== Reaction badges ===== */
.reaction-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.reaction-badge {
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 999px;
    background: #fef3f2;
    color: #d92d20;
    border: 1px solid #fecdca;
}
.reaction-badge.muted {
    background: #f2f4f7;
    color: #667085;
    border-color: #eaecf0;
}

/* ===== Mini vital charts ===== */
.mini-chart-card {
    background: #fff;
    border: 1px solid #edf0f4;
    border-radius: 10px;
    padding: 10px 14px;
}
.mini-chart-title {
    font-size: 12px;
    font-weight: 700;
    color: #1d2939;
    margin-bottom: 4px;
}
.mini-chart-sub {
    font-weight: 400;
    color: #98a2b3;
    font-size: 11px;
}
.mini-chart-svg {
    width: 100%;
    height: 140px;
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
    font-size: 8px;
    fill: #98a2b3;
}
.vital-line {
    stroke-width: 2;
}
.temp-line { stroke: #ef6820; }
.pulse-line { stroke: #3538cd; }
.vital-dot {
    fill: #fff;
    stroke-width: 2;
}
.temp-dot { stroke: #ef6820; }
.pulse-dot { stroke: #3538cd; }

/* ===== Monitoring table ===== */
.table-card {
    border: 1px solid #edf0f4;
    border-radius: 10px;
    overflow: hidden;
}
.transfusion-table :deep(table) {
    margin-bottom: 0;
}
.transfusion-table :deep(thead th) {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    color: #667085;
    background: #f9fafb;
    border-bottom: 1px solid #edf0f4;
}
.transfusion-table :deep(tbody td) {
    vertical-align: middle;
    border-color: #f0f2f5;
}
.transfusion-table :deep(tbody tr:hover) {
    background-color: #f9fafb;
}
.action-btn {
    border-radius: 6px;
}

/* ===== Empty states ===== */
.empty-items {
    text-align: center;
    color: #98a2b3;
    font-size: 13px;
    padding: 16px 0;
}
.empty-state {
    text-align: center;
    color: #98a2b3;
    font-size: 14px;
    padding: 40px 0;
    background: #fff;
    border: 1px dashed #eaecf0;
    border-radius: 12px;
}
</style>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>