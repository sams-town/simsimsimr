<template>
    <div>
        <section class="card">
            <div class="card-header bg-indigo-200">
                <span class="text-bold-18">Data Dasar</span>
            </div>
            <div class="card-body">
                <div class="row" style="font-size: 14px">
                    <div class="col-lg-4">
                        <div class="font-weight-bold">Nama</div>
                        <div>{{ regData.name }} ({{ parsedAge(regData.birth_date) }})</div>
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
                <b-button @click="openModalVital" pill variant="primary" class="px-3">
                    <feather-icon icon="PlusIcon" class="mr-1" /> Tambah
                </b-button>
            </div>
            <b-table bordered head-variant="light" :items="vital_list" :fields="fields" class="mt-12" style="font-size: 13px;">
                <template #cell(user)="row">
                    <div>
                        <div class="font-weight-bold">{{ row.item.employee_nurse_name }}</div>
                        <div class="">{{ row.item.date_updated }}</div>
                    </div>
                    <div class="mt-10">
                        <div class="font-weight-bold">Tanggal observasi:</div>
                        <div>{{ row.item.observation_date }}</div>
                    </div>
                </template>

                <template #cell(monitoring)="row">
                    <div class="row">
                        <div class="col-lg-6">
                            <table class="custom-border w-100">
                                <tr>
                                    <td style="width: 120px;">Systolic</td>
                                    <td style="width: 20px;">:</td>
                                    <td>{{ row.item.systolic }} mmHg</td>
                                </tr>
                                <tr>
                                    <td>Diastolic</td>
                                    <td>:</td>
                                    <td>{{ row.item.diastolic }} mmHg</td>
                                </tr>
                                <tr>
                                    <td>Frekuensi Nadi</td>
                                    <td>:</td>
                                    <td>{{ row.item.pulse }} x/mnt</td>
                                </tr>
                                <tr>
                                    <td>Suhu</td>
                                    <td>:</td>
                                    <td>{{ row.item.temperature }} ᵒC</td>
                                </tr>
                                <tr>
                                    <td>Frekuensi Nafas</td>
                                    <td>:</td>
                                    <td>{{ row.item.respiratory_rate }} x/mnt</td>
                                </tr>
                                <tr>
                                    <td>Saturasi Oksigen</td>
                                    <td>:</td>
                                    <td>{{ row.item.oxygen_saturation }} %</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-lg-6">
                            <table class="custom-border w-100">
                                <tr>
                                    <td style="width: 100px;">Oksigen</td>
                                    <td style="width: 20px;">:</td>
                                    <td>{{ getOxygenName(row.item.oxygen_type_id || '-') }}</td>
                                </tr>
                                <tr>
                                    <td>Kesadaran</td>
                                    <td>:</td>
                                    <td>{{ getAwareName(row.item.aware) }}</td>
                                </tr>
                                <tr>
                                    <td>GCS Mata</td>
                                    <td>:</td>
                                    <td>{{ getGcsEyeName(row.item.eye_score || '-') }}</td>
                                </tr>
                                <tr>
                                    <td>GCS Verbal</td>
                                    <td>:</td>
                                    <td>{{ getGcsVerbalName(row.item.verbal_score || '-') }}</td>
                                </tr>
                                <tr>
                                    <td>GCS Motorik</td>
                                    <td>:</td>
                                    <td>{{ getGcsMotoricName(row.item.motor_score || '-') }}</td>
                                </tr>
                                <tr>
                                    <td>Warna Kulit</td>
                                    <td>:</td>
                                    <td>{{ getColorSkinName(row.item.skin_color || '-') }}</td>
                                </tr>
                                <tr>
                                    <td>Perilaku</td>
                                    <td>:</td>
                                    <td>{{ getBehaviorName(row.item.behavior || '-') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <table class="custom-border w-100">
                        <tr>
                            <td style="width: 120px;">Keterangan</td>
                            <td style="width: 20px;">:</td>
                            <td>
                                <div v-html="parsedBody(row.item.notes)"></div>
                            </td>
                        </tr>
                    </table>
                </template>

                <template #cell(action)="row">
                    <div class="d-flex">
                        <b-button @click="onEditVital(row.item)" size="sm" variant="warning-500" class="mr-1">
                            <feather-icon icon="EditIcon" />
                        </b-button>
                        <b-button @click="removeVital(row.item)" size="sm" variant="danger-500">
                            <feather-icon icon="TrashIcon" />
                        </b-button>
                    </div>
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

        <b-modal v-model="active.modal_vital" title="Observasi Tanda Vital" size="lg" no-close-on-backdrop>
            <div class="row">
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Tanggal</label>
                    <b-form-input v-model="form_vital.observation_date" size="sm" type="datetime-local" />
                </div>
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Berat Badan</label>
                    <b-input-group append="kg" size="sm">
                        <b-form-input type="number" v-model="form_vital.weight" />
                    </b-input-group>
                </div>
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Tinggi Badan</label>
                    <b-input-group append="cm" size="sm">
                        <b-form-input type="number" v-model="form_vital.height" />
                    </b-input-group>
                </div>
                <div class="col-lg-3 mt-8 mt-lg-0">
                    <label class="fs-13 mb-0">Systolic</label>
                    <b-input-group append="mmHg" size="sm">
                        <b-form-input type="number" autocomplete="off" v-model="form_vital.systolic"></b-form-input>
                    </b-input-group>
                </div>
                <div class="col-lg-3 mt-8">
                    <label class="fs-13 mb-0">Diastolic</label>
                    <b-input-group append="mmHg" size="sm">
                        <b-form-input type="number" autocomplete="off" v-model="form_vital.diastolic"></b-form-input>
                    </b-input-group>
                </div>
                <div class="col-lg-3 mt-8">
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
                    <label class="fs-13 mb-0">Oksigen</label>
                    <v-select v-model="form_vital.oxygen_type_id" :options="OptionsOxygen" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm"></v-select>
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
    </div>
</template>
<script>
import { mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import moment from 'moment'
import dateMixin from '@/mixins/dateMixin';
import optionMixin from '@/mixins/optionMixin';

export default {
    name: 'PatientInpatient',
    mixins: [dateMixin, optionMixin],
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            reg_des_id: '',
            regData: {},
            vital_list: [],
            active: {
                modal_vital: false,
                disabledVital: false,
            },
            isPews: false,
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
            form_vital: {
                id: '',
                registration_destination_id: '',
                employee_nurse_id: '',
                observation_date: moment(new Date()).format('YYYY-MM-DD HH:MM'),
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
            fields: [
                { key: 'user', label: 'Petugas', thStyle: 'width: 300px' },
                // { key: 'observation_date', label: 'Tanggal Observasi', thStyle: 'width: 200px' },
                { key: 'monitoring', label: 'Monitoring' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 150px' },
            ],
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
    watch: {
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.getVitalList()
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.getVitalList()
            }
        },
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

        getData(regDesId) {
			this.$store.dispatch('registration/getById', regDesId).then(data => {
                this.regData = data[0]
                this.isPews = this.pewsCriteria(this.regData?.birth_date)                                    
            })
        },

        openModalVital() {
            this.resetFormVital()
            this.active.modal_vital = true
        },

        resetFormVital() {
            this.form_vital.id = ''
            this.form_vital.registration_destination_id = ''
            this.form_vital.employee_nurse_id = ''
            this.form_vital.observation_date = moment(new Date()).format('YYYY-MM-DD HH:MM')
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
                        this.getVitalList()
                    } 
                )
                .catch(({error, response}) => {
                    this.notification('Failed','CloseIcon','danger',`${error}`)
                    this.active.disabledVital = false
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
                this.active.disabledVital = false
            }
        },

        getVitalList() {
            this.params.registration_destination_id = this.reg_des_id

            try {                
                this.$store.dispatch('vitalsign/getListVitalSign', this.params).then((response) => {
                    this.vital_list = response.rows
                    this.totalRows = response.iTotal
                    // console.log('this.vital_list: ', this.vital_list);
                }, error => {
                    console.error('Error while GET vitalsign/getListVitalSign at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET vitalsign/getListVitalSign at: ', error);
            }
        },

        onEditVital(obj) {
            this.openModalVital()

            this.form_vital.id = obj?.id || ''
            this.form_vital.registration_destination_id = obj?.registration_destination_id || ''
            this.form_vital.employee_nurse_id = obj?.employee_nurse_id || ''
            this.form_vital.observation_date = obj?.observation_date || ''
            this.form_vital.weight = obj?.weight
            this.form_vital.height = obj?.height
            this.form_vital.systolic = obj?.systolic || ''
            this.form_vital.diastolic = obj?.diastolic || ''
            this.form_vital.pulse = obj?.pulse || ''
            this.form_vital.temperature = obj?.temperature || ''
            this.form_vital.respiratory_rate = obj?.respiratory_rate || ''
            this.form_vital.oxygen_saturation = obj?.oxygen_saturation || ''
            this.form_vital.oxygen_type_id = obj?.oxygen_type_id
            this.form_vital.aware = obj?.aware
            this.form_vital.eye_score = obj?.eye_score || ''
            this.form_vital.verbal_score = obj?.verbal_score || ''
            this.form_vital.motor_score = obj?.motor_score || ''
            this.form_vital.skin_color = obj?.skin_color || ''
            this.form_vital.behavior = obj?.behavior || ''
            this.form_vital.notes = obj?.notes || ''
        },

        removeVital(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus observasi tanggal ${obj.observation_date} dari ${obj.employee_nurse_name} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('vitalsign/deleteVitalSign', obj.id)
                    .then(
                        response => {
                            if (response.status === 200 || response.status === 201) {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getVitalList()
                            } else {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            }
                        },
                        error => {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            console.error('Error While Delete Using vitalsign/deleteVitalSign at: ', error)
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
        if (this.$route.params.id) {
            this.reg_des_id = this.$route.params.id
            this.getData(this.$route.params.id)
            this.getVitalList()
        }
    }
}
</script>
<style scoped>
    table.custom-border tr td {
        border: 0px;
        padding: 4px 12px;
    }
</style>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>