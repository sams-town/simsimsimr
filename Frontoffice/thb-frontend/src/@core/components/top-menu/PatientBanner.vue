<template>
    <section>
        <div class="card">
            <template v-if="regData.health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f'">
                <div class="card-header py-1 px-12 fs-12 font-weight-bold" :class="getEwsVariant(regData.ews_score)">EWS: {{ getEwsLabel(regData.ews_score) }}</div>
            </template>
            <div class="card-body py-8 px-12">
                <div class="d-flex flex-col flex-lg-row w-100">
                    <div class="w-100">
                        <router-link :to="{ path: '/patient/detail/' + regData.registration_destination_id }">
                            <span class="fs-24 text-uppercase font-weight-bold text-neutral-900" style="line-height: 100%;">{{ regData.alias_name }}. {{ regData.name }}  </span>
                        </router-link>
                        <div class="fs-14 text-neutral-900">
                            <span>{{ localDate(regData.birth_date) }} ({{ parsedAge(regData.birth_date) }}) </span>
                            <span>|  {{ regData.norm }}  </span>
                            <span>|  {{ regData.is_new == 'N' ? 'Pasien Lama' : 'Pasien Baru' }}  </span>
                            <span>|  {{ regData.health_facility_item_name | capitalize }} </span>
                            <span>|  {{ getPrimaryInsurance() }}</span>
                        </div>
                        <div class="fs-13 text-neutral-500">
                            <span>EWS: {{ regData.ews_score | number}}  </span>
                            <span>|  Tensi: {{ regData.ttv?.systolic | number }}/{{ regData.ttv?.diastolic | number }}  </span>
                            <span>|  Nadi: {{ regData.ttv?.pulse | number }}  </span>
                            <span>|  Suhu: {{ regData.ttv?.temperature | number }}  </span>
                            <span>|  Nafas: {{ regData.ttv?.respiratory_rate | number }}  </span>
                            <span>|  Saturasi: {{ regData.ttv?.oxygen_saturation | number }}  </span>
                            <span>|  Oksigenasi: {{ getOxygenName(regData.ttv?.oxygen_type_id || 0) }}  </span>
                            <span>|  Kesadaran: {{ getAwareName(regData.aware || 0) }}  </span>
                            <span>|  BB: {{ regData.ttv?.weight | number }}  </span>
                            <span>|  TB: {{ regData.ttv?.height | number }}</span>
                            <span>
                                |  diperbarui pada: 
                                <span @click="openModalVital" style="color: blue;" class="pointer">
                                    {{ localDateTime(regData.ttv?.observation_date)}}
                                    <feather-icon icon="ArrowRightCircleIcon" color="blue" v-if="regData.ttv?.observation_date" />
                                </span>
                            </span>
                                |
                            <span @click="openModalMonitoring" style="color: blue;" class="pointer">
                                {{ 'Monitoring' }}
                                <feather-icon icon="ArrowRightCircleIcon" color="blue"/>
                            </span>
                        </div>
                    </div>
                    <!-- <template v-if="cashNotPrimaryInsurance()"> -->
                    <template v-if="regData?.logo_url">
                        <div class="ml-lg-auto" style="width: 120px; height: 80px;">
                            <img :src="urlbe + regData?.logo_url" :alt="regData.logo_url" width="120px" height="80px">
                        </div>
                    </template>
                </div>
            </div>
        </div>
        <b-modal v-model="active.modal_vital" size="lg" hide-footer scrollable>
            <template #modal-header="{ close }">
                <div class="d-flex justify-content-between w-100">
                    <div class="text-uppercase font-weight-bold fs-13 pr-16">Riwayat TTV <span class="text-primary">{{ temp_header.alias_name }}. {{ temp_header.name }} | {{ temp_header.norm }} | {{localDate(temp_header.birth_date)}} ({{ parsedAge(temp_header.birth_date) }}) | BB: {{temp_header.weight | number}} kg | TB: {{ temp_header.height | number}} cm</span></div>
                    <feather-icon icon="XIcon" @click="close()" class="pointer"/>
                </div>
            </template>
            <section class="">
                <b-button @click="openModalAddVital()" variant="primary" size="sm" class="mr-2">
                    <feather-icon icon="PlusCircleIcon" class="mr-1" />
                    <span class="fs-12">TAMBAH TTV</span>
                </b-button>
                <b-button
                    v-if="regData.sex == 0"
                    :to="`/patient/inpatient/partograph/${regData.registration_destination_id}`"
                    variant="success"
                    size="sm"
                    class="mr-2"
                >
                    <feather-icon icon="FileTextIcon" class="mr-1" />
                    <span class="fs-12">BUKA PARTOGRAF</span>
                </b-button>
                <section class="modal-content-relative">
                    <div class="table-scroll-x" style="width: 100%;">
                            <b-table
                            bordered small responsive
                            :items="vital_list"
                            :fields="vitalFields"
                            head-variant="dark"
                            thead-tr-class="fs-13 whitespace-nowrap"
                            tbody-tr-class="fs-12"
                            class="mt-8 wide-table"
                            >
                            <template #cell(orserver)="row">
                                <div class="font-weight-bold">{{ row.item.employee_nurse_name }}</div>
                                <div>{{ localDateTime(row.item.observation_date) }}</div>
                            </template>
                            <template #cell(blood_pressure)="row">
                                <div style="white-space: nowrap;">{{ row.item.systolic }}/{{ row.item.diastolic }} mmHg</div>
                            </template>
                            <template #cell(pulse)="row">
                                <div style="white-space: nowrap;">{{ row.value }} x/mnt</div>
                            </template>
                            <template #cell(temperature)="row">
                                <div style="white-space: nowrap;">{{ row.value }} ᵒC</div>
                            </template>
                            <template #cell(respiratory_rate)="row">
                                <div style="white-space: nowrap;">{{ row.value }} x/mnt</div>
                            </template>
                            <template #cell(oxygen_saturation)="row">
                                <div style="white-space: nowrap;">{{ row.value }} %</div>
                            </template>
                            <template #cell(oxygen_type_id)="row">
                                <div class="text-center">{{ getOxygenName(row.value || '-') }}</div>
                            </template>
                            <template #cell(aware)="row">
                                <div>{{ getAwareName(row.value ?? '-') }}</div>
                            </template>
                            <template #cell(eye_score)="row">
                                <div>{{ getGcsEyeName(row.value || '-') }}</div>
                            </template>
                            <template #cell(verbal_score)="row">
                                <div>{{ getGcsVerbalName(row.value || '-') }}</div>
                            </template>
                            <template #cell(motor_score)="row">
                                <div>{{ getGcsMotoricName(row.value || '-') }}</div>
                            </template>
                            <template #cell(skin_color)="row">
                                <div>{{ getColorSkinName(row.value || '-') }}</div>
                            </template>
                            <template #cell(behavior)="row">
                                <div>{{ getBehaviorName(row.value || '-') }}</div>
                            </template>
                            <template #cell(weight)="row">
                                <div style="white-space: nowrap;">{{ row.value }} kg</div>
                            </template>
                            <template #cell(height)="row">
                                <div style="white-space: nowrap;">{{ row.value }} cm</div>
                            </template>
                        </b-table>
                    </div>
                </section>
            </section>
        </b-modal>
        <b-modal v-model="active.modal_monitoring" size="lg" hide-footer scrollable dialog-class="monitoring-modal">
            <template #modal-header="{ close }">
                <div class="d-flex justify-content-between w-100">
                    <div class="text-uppercase font-weight-bold fs-13 pr-16">
                        Monitoring Pasien
                        <span class="text-primary">
                            {{ temp_header.alias_name }}. {{ temp_header.name }} | {{ temp_header.norm }} | {{ localDate(temp_header.birth_date) }} ({{ parsedAge(temp_header.birth_date) }}) | BB: {{ temp_header.weight | number }} kg | TB: {{ temp_header.height | number }} cm
                        </span>
                    </div>
                    <feather-icon icon="XIcon" @click="close()" class="pointer" />
                </div>
            </template>

            <section class="monitoring-tabs-wrapper">
                <b-tabs pills nav-class="monitoring-nav" content-class="monitoring-content mt-16">
                    <b-tab title="Detak Jantung Janin" v-if="regData.sex == 0" active lazy>
                        <FetalHeart :id="regData.registration_destination_id" />
                    </b-tab>
                    <b-tab title="Transfusi Darah" lazy>
                        <BloodTransfusion :id="regData.registration_destination_id" />
                    </b-tab>
                    <b-tab title="Grafik Suhu" lazy>
                        <TemperatureChart :id="regData.registration_destination_id" />
                    </b-tab>
                    <b-tab title="Observasi Pasien dengan Nyeri" lazy>
                        <PainObservation :id="regData.registration_destination_id" />
                    </b-tab>
                    <b-tab title="Sliding Scale Insulin" lazy>
                        <SlidingScale :id="regData.registration_destination_id" />
                    </b-tab>
                    <b-tab title="EWS" lazy>
                        <EWS :id="regData.registration_destination_id" />
                    </b-tab>
                    <!--
                        Tambahkan monitoring lain di sini, contoh:
                        <b-tab title="Balance Cairan" lazy>
                            <FluidBalance :id="regData.registration_destination_id" />
                        </b-tab>
                    -->
                </b-tabs>
            </section>
        </b-modal>
        <b-modal ref="addVitalModal" v-model="active.is_addVital" size="lg" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="d-flex justify-content-between w-100">
                    <div class="text-uppercase font-weight-bold fs-13 pr-16">
                        Riwayat TTV
                        <span class="text-primary">
                            {{ temp_header.alias_name }}. {{ temp_header.name }} | {{ temp_header.norm }} | {{localDate(temp_header.birth_date)}} ({{ parsedAge(temp_header.birth_date) }}) | BB: {{temp_header.weight | number}} kg | TB: {{ temp_header.height | number}} cm
                        </span>
                    </div>
                    <feather-icon icon="XIcon" @click="close()" class="pointer"/>
                </div>
            </template>
            <div class="row">
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Tanggal</label>
                    <b-form-input v-model="form_vital.observation_date" size="sm" type="datetime-local" />
                </div>
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Systolic</label>
                    <b-input-group append="mmHg" size="sm">
                        <b-form-input type="number" autocomplete="off" v-model="form_vital.systolic"></b-form-input>
                    </b-input-group>
                </div>
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Diastolic</label>
                    <b-input-group append="mmHg" size="sm">
                        <b-form-input type="number" autocomplete="off" v-model="form_vital.diastolic"></b-form-input>
                    </b-input-group>
                </div>
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Frekuensi Nadi</label>
                    <b-input-group append="x/mnt" size="sm">
                        <b-form-input type="number" autocomplete="off" v-model="form_vital.pulse"></b-form-input>
                    </b-input-group>
                </div>
                <div class="col-lg-3 mt-8">
                    <label class="fs-13 mb-0">Suhu</label>
                    <b-input-group append="ᵒC" size="sm">
                        <b-form-input type="number" autocomplete="off" v-model="form_vital.temperature"></b-form-input>
                    </b-input-group>
                </div>
                <div class="col-lg-3 mt-8">
                    <label class="fs-13 mb-0">Frekuensi Nafas</label>
                    <b-input-group append="x/mnt" size="sm">
                        <b-form-input type="number" autocomplete="off" v-model="form_vital.respiratory_rate"></b-form-input>
                    </b-input-group>
                </div>
                <div class="col-lg-3 mt-8">
                    <label class="fs-13 mb-0">Saturasi Oksigen</label>
                    <b-input-group append="%" size="sm">
                        <b-form-input type="number" autocomplete="off" v-model="form_vital.oxygen_saturation"></b-form-input>
                    </b-input-group>
                </div>
                <div class="col-lg-3 mt-8">
                    <label class="fs-13 mb-0">Oksigenasi</label>
                    <v-select v-model="form_vital.oxygen_type_id" :options="OptionsOxygen" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm"></v-select>
                </div>
                <div class="col-12 mt-4 mb-2">
                    <div class="fs-13 text-muted" style="font-style: italic;">
                        Data kesadaran dan antropometri diisi secara otomatis berdasarkan data TTV sebelumnya. Lakukan perubahan jika diperlukan.
                    </div>
                </div>
                <div class="col-lg-3 mt-8">
                    <label class="fs-13 mb-0">Kesadaran</label>
                    <v-select v-model="form_vital.aware" :options="OptionsAware" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm"></v-select>
                </div>
                <div class="col-lg-3 mt-8">
                    <label class="fs-13 mb-0">GCS Mata</label>
                    <v-select v-model="form_vital.eye_score" :options="OptionGcsEye" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm"></v-select>
                </div>
                <div class="col-lg-3 mt-8">
                    <label class="fs-13 mb-0">GCS verbal</label>
                    <v-select v-model="form_vital.verbal_score" :options="OptionGcsVerbal" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm"></v-select>
                </div>
                <div class="col-lg-3 mt-8">
                    <label class="fs-13 mb-0">GCS Motorik</label>
                    <v-select v-model="form_vital.motor_score" :options="OptionGcsMotoric" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm"></v-select>
                </div>
                <template v-if="isPews">
                    <div class="col-lg-3 mt-8">
                        <label class="fs-13 mb-0">Warna Kulit</label>
                        <v-select v-model="form_vital.skin_color" :options="optSkinColor" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                    </div>
                    <div class="col-lg-3 mt-8">
                        <label class="fs-13 mb-0">Perilaku</label>
                        <v-select v-model="form_vital.behavior" :options="optBehavior" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                    </div>
                </template> 
                <div class="col-lg-3 mt-8">
                    <label class="fs-13 mb-0">Berat Badan</label>
                    <b-input-group append="kg" size="sm">
                        <b-form-input type="number" v-model="form_vital.weight" />
                    </b-input-group>
                </div>
                <div class="col-lg-3 mt-8">
                    <label class="fs-13 mb-0">Tinggi Badan</label>
                    <b-input-group append="cm" size="sm">
                        <b-form-input type="number" v-model="form_vital.height" />
                    </b-input-group>
                </div>
                <div class="col-12 mt-8">
                    <label class="fs-13 mb-0">Keterangan</label>
                    <b-textarea v-model="form_vital.notes" size="sm"/>
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button @click="validationVital" :disabled="active.disabledVital" variant="primary" class="px-3">Simpan</b-button>
                </div>
            </template>
        </b-modal>
    </section>
</template>
<script>
import { mapState } from 'vuex'
import dateMixin from "@/mixins/dateMixin";
import optionMixin from '@/mixins/optionMixin';
import moment from 'moment';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import FetalHeart from '@/views/pages/patient/inpatient/fetal_heart/FetalHeartView.vue'
import BloodTransfusion from '@/views/pages/patient/inpatient/blood_transfusion/BloodTransfusionView.vue'
import TemperatureChart from '@/views/pages/patient/inpatient/temperature_chart/TemperatureChartView.vue';
import PainObservation from '@/views/pages/patient/inpatient/pain_observation/PainObservationView.vue';
import SlidingScale from '@/views/pages/patient/inpatient/sliding_scale/SlidingScaleView.vue';
import EWS from '@/views/pages/patient/inpatient/EWS/EWSView.vue';

export default {
    mixins: [dateMixin, optionMixin],
    components: { FetalHeart, BloodTransfusion, TemperatureChart, PainObservation, SlidingScale, EWS },
    data() {
        return {
            tableScrollWidth: 0,
            userData: JSON.parse(localStorage.getItem('userData')),
            emergency_id: "444b3134-5b75-4b67-af72-a558d46716d7",
            inpatient_id: "40f06534-07ec-4221-ad9d-dea08196079f",
            outpatient_id:"3cb626f5-9148-4fb1-8663-3d7f028b01a6",
            urlbe:process.env.VUE_APP_IMAGE,
            isPews: false,
            active: {
                modal_vital: false,
                modal_monitoring: false,
                is_addVital:false,
                disabledVital:false,
            },
            regData: {},
            vital_list: [],
            vitalFields: [
                { key: 'orserver', label: 'Observasi' },
                { key: 'blood_pressure', label: 'Tensi' },
                { key: 'pulse', label: 'Nadi' },
                { key: 'temperature', label: 'Suhu' },
                { key: 'respiratory_rate', label: 'Nafas' },
                { key: 'oxygen_saturation', label: 'SpO2' },
                { key: 'oxygen_type_id', label: 'Oksigenasi' },
                { key: 'aware', label: 'Kesadaran' },
                { key: 'eye_score', label: 'GCS Mata' },
                { key: 'verbal_score', label: 'GCS Verbal' },
                { key: 'motor_score', label: 'GCS Motorik' },
                { key: 'skin_color', label: 'Warna kulit' },
                { key: 'behavior', label: 'Perilaku' },
                { key: 'notes', label: 'Keterangan' },
                { key: 'weight', label: 'Berat Badan' },
                { key: 'height', label: 'Tinggi Badan' },
            ],

            OptionsResponse: [
                { value: 0, title: 'A' },
                { value: 1, title: 'V' },
                { value: 2, title: 'P' },
                { value: 3, title: 'U' },
            ],
            form_vital: {
                id: '',
                registration_destination_id: '',
                employee_nurse_id: '',
                observation_date: moment(new Date()).format('YYYY-MM-DD HH:mm'),
                weight: '',
                height: '',
                systolic: '',
                diastolic: '',
                pulse: '',
                temperature: '',
                respiratory_rate: '',
                oxygen_saturation: '',
                oxygen_type_id: '',
                aware: '',
                eye_score: '',
                verbal_score: '',
                motor_score: '',
                skin_color: '',
                behavior: '',
                notes: ''
            },
            temp_header: {
                alias_name: '',
                name: '',
                norm: '',
                birth_date: '',
                weight: '',
                height: '',
            },
            dr_position_obgyn: '146b0883-efff-4bf1-a40b-8beae6e2c1bd',
        }
    },

    computed: {
        ...mapState({ 
            OptionsOxygen: state => state.soap.vitals.OptionsOxygen,
            OptionsAware: state => state.soap.vitals.OptionsAware,
            OptionGcsEye: state => state.soap.vitals.OptionGcsEye,
            OptionGcsVerbal: state => state.soap.vitals.OptionGcsVerbal,
            OptionGcsMotoric: state => state.soap.vitals.OptionGcsMotoric,
            optSkinColor: state => state.frontoptions.general.vital_sign.skin_color,
            optBehavior: state => state.frontoptions.general.vital_sign.behavior,
        }),
    },
    filters: {
        capitalize(text) {
            if (!text) return "";
            return text.toLowerCase().replace(/\b\w/g, (char) => char.toUpperCase());
        },
        number(value) {
            return Number(value)
        },
        // parsedBody(text){
        //     return String(text || '').replace(/\n/g, "<br>");
        // },
    },
    methods: {
        cashNotPrimaryInsurance() {
            let temp_ins = this.regData.registration_insurance || []
            if (temp_ins?.length == 0) return false

            let primary_index = temp_ins?.findIndex(item => item.is_primary == 1)

            if (primary_index !== -1) {
                let final_row = temp_ins[primary_index]
                return final_row.insurance_id == 1 ? false : true
            }
            return false
        },

        getPrimaryInsurance() {
            let temp_ins = this.regData.registration_insurance || []
            if (temp_ins?.length == 0) return '-'

            let primary_index = temp_ins?.findIndex(item => item.is_primary == 1)

            if (primary_index !== -1) {
                let final_row = temp_ins[primary_index]
                return final_row.insurance_id == 1 ? `${final_row.insurance_name} Kelas ${final_row?.service_class_name || '-'}` : `${final_row.company_name} Kelas ${final_row?.service_class_name || '-'}`
            }
            return '-'
        },

        openModalVital() {
            this.active.modal_vital = true
        }, 

        openModalMonitoring() {
            this.active.modal_monitoring = true
        },     
        openModalAddVital(){
            const latest = this.vital_list && this.vital_list.length > 0 ? this.vital_list[0] : {};
            // console.log('latest', latest);  
            this.form_vital.oxygen_type_id = latest.oxygen_type_id ?? '';
            this.form_vital.aware = latest.aware ?? '';
            this.form_vital.eye_score = latest.eye_score ?? '';
            this.form_vital.verbal_score = latest.verbal_score ?? '';
            this.form_vital.motor_score = latest.motor_score ?? '';
            this.form_vital.skin_color = latest.skin_color ?? '';
            this.form_vital.behavior = latest.behavior ?? '';
            this.form_vital.weight = latest.weight ?? '';
            this.form_vital.height = latest.height ?? '';

            this.form_vital.id = '';
            this.form_vital.registration_destination_id = '';
            this.form_vital.employee_nurse_id = '';
            this.form_vital.observation_date = moment(new Date()).format('YYYY-MM-DD HH:mm');
            this.form_vital.systolic = '';
            this.form_vital.diastolic = '';
            this.form_vital.pulse = '';
            this.form_vital.temperature = '';
            this.form_vital.respiratory_rate = '';
            this.form_vital.oxygen_saturation = '';
            this.form_vital.notes = '';

            this.active.is_addVital = true
            // this.resetFormVital()
        },
        validationVital() {
            this.form_vital.registration_destination_id = this.reg_des_id
            this.form_vital.employee_nurse_id = this.userData.employee_id

            try {
                this.active.disabledVital = true
                this.$store.dispatch('vitalsign/postPutVitalSign', this.form_vital)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_vital = false
                        this.active.disabledVital = false
                        this.resetFormVital()
                        this.getVitalList(this.regData?.registration_id)
                        this.$refs['addVitalModal'].hide()
                        this.openModalVital()
                    } 
                )
                .catch(({error, response}) => {
                    this.notification('Failed','CloseIcon','danger',`${error}`)
                    console.log('Error catch at 1: ', error)
                    this.active.disabledVital = false
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at 2: ', error)                
                this.active.disabledVital = false
            }
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        resetFormVital() {
            this.form_vital.id = ''
            this.form_vital.registration_destination_id = ''
            this.form_vital.employee_nurse_id = ''
            this.form_vital.observation_date = moment(new Date()).format('YYYY-MM-DD HH:mm')
            this.form_vital.weight = ''
            this.form_vital.height = ''
            this.form_vital.systolic = ''
            this.form_vital.diastolic = ''
            this.form_vital.pulse = ''
            this.form_vital.temperature = ''
            this.form_vital.respiratory_rate = ''
            this.form_vital.oxygen_saturation = ''
            this.form_vital.oxygen_type_id = ''
            this.form_vital.aware = ''
            this.form_vital.eye_score = ''
            this.form_vital.verbal_score = ''
            this.form_vital.motor_score = ''
            this.form_vital.skin_color = ''
            this.form_vital.behavior = ''
            this.form_vital.notes = ''
        },
        getEwsVariant(value) {
            let status = Number(value)
            if (status >= 1 && status <= 4) return 'primary-banner'
            if (status >= 5 && status <= 6) return 'warning-banner'
            if (status >= 7) return 'danger-banner'
            return 'neutral-banner'
        },
        getEwsLabel(value) {
            let status = Number(value)
            if (status >= 1 && status <= 4) return 'BIRU'
            if (status >= 5 && status <= 6) return 'KUNING'
            if (status >= 7) return 'MERAH'
            return '-'
        },

        getData(reg_des_id) {
            try {
                this.$store.dispatch('registration/getById', reg_des_id).then(data => {
                    this.regData = data[0]
                    // console.log('this.regData', this.regData);
                    this.isPews = this.pewsCriteria(this.regData?.birth_date)                    

                    // modal-heder
                    this.temp_header.alias_name = this.regData.alias_name
                    this.temp_header.name = this.regData.name
                    this.temp_header.norm = this.regData.norm
                    this.temp_header.birth_date = this.regData.birth_date
                    this.temp_header.weight = this.regData?.ttv?.weight
                    this.temp_header.height = this.regData?.ttv?.height

                    this.getVitalList(this.regData?.registration_id)  
                })
            } catch (error) {
                console.error('Error at: ', error)
            }
        },
        getVitalList(reg_id) {
            try {                   
                this.$store.dispatch('vitalsign/getListVitalSign', { registration_id: reg_id }).then((response) => {
                    this.vital_list = response.rows
                }, error => {
                    console.error('Error while GET vitalsign/getListVitalSign at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET vitalsign/getListVitalSign at: ', error);
            }
        },
    },
    created() {
        if (this.$route.params.id) {
            this.reg_des_id = this.$route.params.id   
            this.getData(this.$route.params.id)  
        }
    },
   
}
</script>
<style scoped>
.danger-banner {
    background: conic-gradient(from 315deg at 30% 90%, #ff1919, #ff0d0d, #ff0202, #f50000, #ea0000, #de0000, #d30000, #c70000);
    color: white;
}
.primary-banner {
    background: conic-gradient(from 315deg at 30% 90%, #2398fa, #2387f2, #2775ea, #2f63dd, #3651ce, #3941bb, #3a34a4, #37288a);
    color: white;
}
.warning-banner {
    background: conic-gradient(from 315deg at 30% 90%, #eefa1c, #f6ec0e, #fadf07, #fdd104, #ffc503, #ffb80d, #ffab09, #ff9e00);
    color: black;
}
.neutral-banner {
    background: conic-gradient(from 315deg at 30% 90%, #ffffff, #fefefe, #f8f9fb, #e8ecf1, #cdd6e0, #bdc8d6, #b7c4d3, #b6c3d2);
    color: black;
}
.wide-table >>> th,
.wide-table >>> td {
  min-width: 90px;
}
/* ===== Tab Monitoring ===== */
.monitoring-modal .modal-body {
    padding-top: 12px;
}
.monitoring-nav {
    border-bottom: 1px solid #edf0f4;
    padding-bottom: 8px;
    gap: 6px;
    flex-wrap: wrap;
}
.monitoring-nav .nav-link {
    font-size: 12px;
    font-weight: 600;
    color: #667085;
    background: #f9fafb;
    border-radius: 999px;
    padding: 6px 16px;
    border: 1px solid transparent;
}
.monitoring-nav .nav-link.active {
    background: #eef4ff;
    color: #3538cd;
    border-color: #d5dcfa;
}
.monitoring-content {
    min-height: 200px;
}
</style>
