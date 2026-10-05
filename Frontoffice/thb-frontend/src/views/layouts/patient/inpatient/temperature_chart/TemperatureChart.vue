<template>
    <div>
        <patientMenu selectedMenu="menu22" />
        <section class="card mt-32">
            <div class="card-header bg-indigo-200">
                <span class="text-bold-18">Data Dasar</span>
            </div>
            <div class="card-body">
                <div class="row" style="font-size: 14px">
                    <div class="col-lg-4">
                        <div class="font-weight-bold">Nama</div>
                        <div>{{ regData.name }} ({{ parsed_age(regData.birth_date) }})</div>
                        <div>{{ regData.room_name }} ({{ regData.room_name }})</div>
                    </div>
                    <div class="col-lg-4">
                        <div class="font-weight-bold">Tanggal Lahir</div>
                        <div>{{ localDate(regData.birth_date) }}</div>
                    </div>
                    <div class="col-lg-4">
                        <div class="font-weight-bold">No RM</div>
                        <div>{{ regData.norm }}</div>
                    </div>
                </div>
            </div>
        </section>
        <section class="mt-32">
            <div class="d-flex justify-content-between align-items-center">
                <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0">
                    <span class="text-regular-14">Tampilkan</span>
                    <b-form-select v-model="perPage" size="sm" :options="pageOptions" class="mx-1"/>
                    <span class="text-regular-14">Entri</span>
                </b-form-group>
                <b-button @click="openModalMain" size="sm" variant="primary">
                    <feather-icon icon="PlusIcon" class="mr-1"/> Tambah
                </b-button>
            </div>

            <b-table :fields="MainFields" :items="temperature_list" bordered responsive class="mt-8" style="font-size: 13px;">
                <template #cell(monitoring_date)="row">
                    <div>{{ row.value }}</div>
                    <div>{{ row.item.diet }}</div>
                </template>
                <template #cell(defecation)="row">
                    <div>Bentuk: {{ row.item.defecation_form }}</div>
                    <div>Frekuensi: {{ row.item.defecation_frequency }}</div>
                </template>
                <template #cell(intake)="row">
                    <div>Intake: {{ row.item.intake }}</div>
                    <div>Output: {{ row.item.output }}</div>
                </template>
                <template #cell(blood_pressure)="row">
                    <div>Pagi: {{ row.item.systolic_morning }}/{{ row.item.diastolic_morning }} mmHg</div>
                    <div>Siang: {{ row.item.systolic_afternoon }}/{{ row.item.diastolic_afternoon }} mmHg</div>
                    <div>Malam: {{ row.item.systolic_evening }}/{{ row.item.diastolic_evening }} mmHg</div>
                </template>
                <template #cell(action)="row">
                    <div class="d-flex">
                        <b-button @click="onEditTemperature(row.item)" size="sm" variant="warning-500" class="mr-1">
                            <feather-icon icon="EditIcon"/>
                        </b-button>
                        <b-button @click="removeTemperature(row.item)" size="sm" variant="danger-500" class="mr-1">
                            <feather-icon icon="TrashIcon" />
                        </b-button>
                        <b-button @click="openModalItem(row.item.id)" size="sm" variant="outline-w3green" class="mr-1">
                            <feather-icon icon="PlusIcon" class="mr-1" />Item
                        </b-button>
                        <template v-if="row.item.temperature_chart_items?.length > 0">
                            <b-button size="sm" @click="row.toggleDetails" variant="outline-primary-500">
                                {{ row.detailsShowing ? 'Hide' : 'Show'}}
                            </b-button>
                        </template>
                    </div>
                </template>
                <template #row-details="row">
                    <b-table :fields="ItemFields" :items="row.item.temperature_chart_items" small responsive >
                        <template #cell(action)="data">
                            <div class="d-flex">
                                <b-button @click="onEditItem(data.item)" size="sm" variant="warning-500" class="mr-1">
                                    <feather-icon icon="EditIcon"/>
                                </b-button>
                                <b-button @click="removeItemTemperature(data.item, row.item.monitoring_date)" size="sm" variant="danger-500">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </div>
                        </template>
                    </b-table>
                </template>
            </b-table>

            <div class="col px-0 mt-20">
                <div class="d-flex">
                    <span class="text-regular-14">Menampilkan {{ firstRows }} hingga <template v-if="lastRows < totalRows">{{ lastRows }}</template> <template v-else>{{ totalRows }}</template> dari {{ totalRows }} entri</span>
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

        <section>
            <ModernLabel label="Monitoring Grafik Suhu" class="mt-32 mb-8" />
            <div class="row mb-16">
                <div class="col-lg-2">
                    <label class="c-form-label">Tangal Mulai</label>
                    <b-form-input v-model="params_range.date_start" @change="getTCRange" type="date" />
                </div>
                <div class="col-lg-2">
                    <label class="c-form-label">Tangal Akhir</label>
                    <b-form-input v-model="params_range.date_end" @change="getTCRange" type="date" />
                </div>
            </div>
            <LineChart
                :chart-options="chartOptions"
                :chart-data="chartData"
                :height="210"
            />

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
import patientMenu from '@/@core/components/tab-menu/patientMenu.vue';
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import vSelect from 'vue-select'
import moment from 'moment'


import { Line as LineChart } from 'vue-chartjs/legacy'
import { 
    Chart as ChartJS, Title, Tooltip, Legend, 
    LineElement, PointElement, CategoryScale, LinearScale,
} from 'chart.js'
ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement)


export default {
    name: 'TemperatureChart',
    components: {
        patientMenu, ModernLabel, vSelect, LineChart
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
            MainFields: [
                { key: 'monitoring_date', label: 'Monitoring' },
                { key: 'defecation', label: 'BAB' },
                { key: 'intake', label: 'Intake/Output' },
                { key: 'blood_pressure', label: 'Tensi' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 250px' },
            ],
            ItemFields: [
                { key: 'observation_hours', label: 'Waktu' },
                { key: 'pulse', label: 'Nadi' },
                { key: 'rr', label: 'Frekuensi Nafas' },
                { key: 'temperature', label: 'Suhu' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 235px' },
            ],

            chartData: {
                labels: [],
                datasets: [
                    {
                        label: 'Suhu',
                        data: [30,100,80],
                        fill: true,
                        borderColor: '#648DE1',
                        tension: 0
                    },
                    {
                        label: 'Nadi',
                        data: [50, 70, 20],
                        fill: true,
                        borderColor: '#DC3545',
                        tension: 0
                    },
                    {
                        label: 'Pernafasan',
                        data: [10, 90, 60],
                        fill: true,
                        borderColor: '#09101E',
                        tension: 0
                    },
                ]
            },
            chartOptions: {
                scales: {
                    y: {
                        display: true,
                        ticks: {
                            display: true,
                        }
                    },
                    x: {
                        display: true,
                        ticks: {
                            display: true,
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        align: 'end',

                        labels: {
                            boxWidth: 12,
                            boxHeight: 12,
                        },
                    },
                }
            },

            params_range: {
                date_start: moment(new Date()).format('YYYY-MM-DD'),
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
			this.$store.dispatch('registration/getById', regDesId).then(data => {
                this.regData = data[0]
            })
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
                this.$store.dispatch('temperaturechart/postPutTemperature', this.form_temperature)
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
                this.$store.dispatch('temperaturechart/getListTemperature', this.params).then((response) => {
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
                    this.$store.dispatch('temperaturechart/deleteTemperature', obj.id)
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
                this.$store.dispatch('temperaturechart/postPutItemTemperature', this.form_item)
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
                    this.$store.dispatch('temperaturechart/deleteItemTemperature', obj.id)
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
                this.$store.dispatch('temperaturechart/getListTemperatureV2', this.params_range).then((response) => {
                    let temp_data = response

                    if (temp_data?.length > 0) {
                        for (let i = 0; i < temp_data.length; i++) {
                            const el = temp_data[i];
                            this.chartData.datasets[0].data.push(el.temperature)
                            this.chartData.datasets[1].data.push(el.pulse)
                            this.chartData.datasets[2].data.push(el.rr)
                            let temp_label = `${el.monitoring_date} ${el.observation_hours}`
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
        if (this.$route.params.id) {
            this.reg_des_id = this.$route.params.id
            this.getData(this.$route.params.id)
            this.getTemperature()
            this.getTCRange()
        }
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>