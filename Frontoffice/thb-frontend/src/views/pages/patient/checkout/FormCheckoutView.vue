<template>
    <div>
        <b-modal v-model="active.modal_main" size="lg" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    FORMULIR PASIEN PULANG
                    <span class="text-primary">{{ regData.alias_name }}. {{ regData.name }} | {{localDate(regData.birth_date)}} ({{ parsedAge(regData.birth_date) }}) | {{ regData.norm }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <ApexaSpinner :visible="isLoading" />
            <div v-show="!isLoading">
                <div v-if="has_noSEP" class="alert alert-warning" role="alert">
                    SEP belum di input silahkan untuk menghubungi Pendaftaran
                </div>
                <div v-if="is_onEditControlBPJS" class="fs-13 mb-2 d-flex justify-content-start">
                    <template v-if="controlBPJSData[0].noSep != null && controlBPJSData[0].noSep != '' && controlBPJSData[0].is_bpjs_non_active == 0">
                        <b-button
                        v-if="controlBPJSData[0].no_control"
                        :to="`/prints/control/${controlBPJSData[0].registration_destination_id}/${controlBPJSData[0].no_control}`"
                        target="_blank"
                        size="sm" variant="w3green">
                            <b-icon icon="printer" class="mr-1" /> Cetak Surat Perjanjian Kunjungan
                        </b-button>
                        <b-button
                        v-else
                        :to="`/prints/surat-perjanjian-kunjungan/${controlBPJSData[0].registration_destination_id}`"
                        target="_blank"
                        size="sm" variant="w3green">
                            <b-icon icon="printer" class="mr-1" /> Cetak Surat Perjanjian Kunjungan
                        </b-button>
                    </template>
                    <template v-else>
                        <b-button
                        :to="`/prints/surat-perjanjian-kunjungan/${controlBPJSData[0].registration_destination_id}`"
                        target="_blank"
                        size="sm" variant="w3green">
                            <b-icon icon="printer" class="mr-1" /> Cetak Surat Perjanjian Kunjungan
                        </b-button>
                    </template>

                </div>
                <div>
                    <label class="fs-13 fw-bold mb-0">Perawat : {{ userData.employee_name }}</label>
                    <!-- <v-select v-model="form_checkout.employee_nurse_id" :options="all_nurse" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" /> -->
                </div>
                <div class="mt-8">
                    <label class="fs-13 fw-bold mb-0">Tanggal Cut Off Rawat Inap</label>
                    <b-input-group>
                        <b-input type="date" v-model="temp.date" />
                        <b-input type="time" v-model="temp.time" />
                    </b-input-group>
                </div>
                <div class="mt-8">
                    <label class="fs-13 fw-bold mb-0">Penjamin : {{ form_checkout.is_bpjs ? 'BPJS' : 'Non BPJS' }}</label>
                </div>
                <div class="mt-8">
                    <label class="fs-13 fw-bold mb-0">Pasien Meninggal</label>
                    <b-form-checkbox value="1" v-model="form_checkout.is_death"
                    :true-value="1" 
                    :false-value="0">Ya
                    </b-form-checkbox>
                </div>

                <div class="mt-8" v-show="!form_checkout.is_death">
                <label class="fs-13 fw-bold">Kontrol</label>
                
                <!-- Show control checkbox and fields only when is_death == 0 -->
                <template v-show="form_checkout.is_control">
                    <b-form-checkbox 
                    v-model="form_checkout.is_control"
                    value="1"
                    :true-value="1" 
                    :false-value="0"
                    >Ya</b-form-checkbox>

                    <!-- Show input fields only when is_control is active -->
                    <template v-if="form_checkout.is_control == 1 || form_checkout.is_control == true">
                    <div>
                        <label class="fs-13 mb-0">Poli</label>
                        <v-select 
                        v-model="form_checkout.health_facility_item_id" 
                        aria-readonly="true" 
                        :options="OptionsHealthFacilityItem" 
                        :reduce="item => item.value" 
                        :disabled="has_noSEP"
                        label="title" 
                        placeholder="-- Poliklinik --" 
                        class="select-size-sm"/>
                    </div>
                    <div class="mt-8">
                        <label class="fs-13 mb-0">Dokter</label>
                        <v-select 
                        v-model="form_checkout.doctor_id" 
                        :options="OptionsDoctor" 
                        :reduce="item => item.value"
                        :disabled="has_noSEP"
                        label="title" 
                        placeholder="-- Dokter --" 
                        class="select-size-sm" />
                    </div>
                    <div class="mt-8">
                        <label class="fs-13 mb-0">Tanggal Kontrol</label>
                        <b-form-input 
                        type="date" 
                        :disabled="has_noSEP"
                        v-model="form_checkout.date_control">
                        </b-form-input>
                    </div>
                    </template>
                </template>
                </div>

                <div class="mt-8">
                    <label class="fs-13 fw-bold mb-0">Berkas-Berkas</label>
                    <b-form-checkbox-group
                        v-model="form_checkout.documents"
                        stacked
                        class="d-flex flex-wrap mt-2"
                    >
                        <div class="col-5 fs-14">
                            <b-form-checkbox value="1" disabled>SEP Rawat Inap</b-form-checkbox>
                            <div>No. SEP : {{ form_checkout.noSEP || '-' }}</div>
                        </div>
                        <!-- <b-form-checkbox class="col-5" value="2">Billing Dokter</b-form-checkbox> -->
                        <!-- <b-form-checkbox class="col-5" value="3">Billing Tindakan</b-form-checkbox> -->
                        <!-- <b-form-checkbox class="col-5" value="4">Retur Obat dan BMHP</b-form-checkbox> -->
                        <div class="col-5 fs-14">
                            <b-form-checkbox value="5" disabled>Obat Pulang</b-form-checkbox>
                            <div>{{ take_home_medications || '-' }}</div>
                        </div>
                        <div class="col-12 mt-2 fs-14">
                            <b-form-checkbox value="8" disabled>Resume Pulang</b-form-checkbox>
                            <div>DPJP : {{ items_resume?.doctor_in_charge_name || '-' }}</div>
                            <div v-if="items_resume?.doctor_in_charge_name">Tanggal Resume Dibuat : {{ regData.checkout_date ? localDate(regData.checkout_date) : '-' }}</div>
                            <div v-else>Tanggal Resume Dibuat : -</div>
                        </div>
                        <div class="col-12 mt-3 fs-14">
                            <b-form-checkbox 
                            value="6"
                            disabled
                            >Hasil Penunjang</b-form-checkbox>
                            <template>
                                <div>
                                    <!-- Lab Section -->
                                    <div v-if="history.laboratory?.length > 0">
                                        <label class="fs-13 fw-bold mb-2 mt-2">Laboratorium</label>
                                        <template 
                                        v-for="(item, index) in history.laboratory.filter(entry =>
                                            entry.laboratory_request_items &&
                                            entry.laboratory_request_items.filter(unit => unit.result?.length > 0).length > 0
                                        )">
                                            <section class="card" :class="{ 'mt-16': index > 0 }">
                                                <div class="card-header px-12 py-8">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="font-weight-bold">{{ item?.date_request }}</div>
                                                            <div>{{ item?.doctor_in_charge_name }} | {{ item.health_facility_item_name }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body p-12">
                                                    <template v-if="item.laboratory_request_items">
                                                        <li
                                                            v-for="(unit, unitIndex) in item.laboratory_request_items.filter(unit => unit.result?.length > 0)"
                                                            :class="unitIndex == 0 ? 'mb-2' : 'my-2'"
                                                            style="list-style-type: none;"
                                                        >
                                                            <feather-icon
                                                                icon="EyeIcon"
                                                                @click="triggerLabResult({
                                                                alias_name: regData.alias_name,
                                                                name: regData.name,
                                                                result: unit.result,
                                                                costing_name: unit.laboratory_package_name,
                                                                laboratory_request_id: unit.laboratory_request_id
                                                                })"
                                                                color="blue"
                                                            />
                                                            {{ unit.laboratory_package_name }}
                                                        </li>
                                                    </template>
                                                </div>
                                            </section>
                                        </template>
                                    </div>
                                    <!-- <div v-else class="text-center" style="font-style: italic;">Tidak ditemukan riwayat laboratorium</div> -->

                                    <!-- RAD Section -->
                                    <div v-if="history.radiology?.length > 0" class="mt-4">
                                        <label class="fs-13 fw-bold mb-2">Radiologi</label>
                                        <template v-for="(item, index) in history.radiology.filter(entry =>
                                            entry.radiology_request_items &&
                                            entry.radiology_request_items.filter(unit => unit.url?.length > 0).length > 0
                                        )">
                                            <section class="card" :class="{ 'mt-16': index > 0 }">
                                                <div class="card-header px-12 py-8">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="font-weight-bold">{{ item?.date_request }}</div>
                                                            <div>{{ item?.doctor_in_charge_name }} | {{ item.health_facility_item_name }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body p-12">
                                                    <template v-if="item.radiology_request_items">
                                                        <li
                                                            v-for="(unit, index) in item.radiology_request_items.filter(unit => unit.url?.length > 0)"
                                                            :class="index == 0 ? 'mb-2' : 'my-2'"
                                                            style="list-style-type: none;"
                                                        >
                                                            <!-- <template v-if="unit.url?.length > 0"> -->
                                                                <feather-icon
                                                                    icon="EyeIcon"
                                                                    @click="triggerRadResult({
                                                                    alias_name: regData.alias_name,
                                                                    name: regData.name,
                                                                    url: unit.url,
                                                                    costing_name: unit.radiology_package_name
                                                                    })"
                                                                    color="blue"
                                                                />
                                                            <!-- </template> -->
                                                            {{ unit.radiology_package_name }}
                                                        </li>
                                                    </template>
                                                </div>
                                            </section>
                                        </template>
                                    </div>
                                    <!-- <div v-else class="text-center" style="font-style:italic">Tidak ditemukan riwayat radiologi</div> -->

                                    <!-- Psy Section -->
                                    <div v-if="history.physiotherapy?.length > 0" class="mt-4">
                                        <label class="fs-13 fw-bold mb-2">Fisioterapi</label>
                                        <template v-for="(item, index) in history.physiotherapy">
                                            <section class="card" :class="{ 'mt-16': index > 0 }">
                                                <div class="card-header px-12 py-8">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="font-weight-bold">{{ item?.date_request }}</div>
                                                            <div>{{ item?.doctor_in_charge_name }} | {{ item.health_facility_item_name }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body p-12">
                                                    <li
                                                    v-for="(unit, index) in item.physiotherapy_request_items"
                                                    :class="index == 0 ? 'mb-2' : 'my-2'"
                                                    style="list-style-type: none;"
                                                    >
                                                        {{ unit.physiotherapy_package_name }}
                                                    </li>
                                                </div>
                                            </section>
                                        </template>
                                    </div>
                                    <!-- <div v-else class="text-center" style="font-style:italic">Tidak ditemukan riwayat fisioterapi</div> -->
                                    
                                    <!-- Examination Section -->
                                    <div v-if="history.examination?.length > 0" class="mt-4">
                                        <label class="fs-13 fw-bold mb-2">Penunjang Lainnya</label>
                                        <template v-for="(item, index) in history.examination">
                                            <section class="card" :class="{ 'mt-16': index > 0 }" v-if="item?.data?.files?.length > 0">
                                                <div class="card-header px-12 py-8 font-weight-bold">{{ item.category_examination_name }}</div>
                                                <div class="card-body p-12">
                                                    <!-- <template v-if="item?.data?.files?.length > 0"> -->
                                                        <li v-for="list in item.data.files" class="mb-2">
                                                            {{ list.examination_date }}
                                                            <div>
                                                                <b>Expertise: </b>
                                                                <span>{{ list.expertise || '-' }}</span>
                                                            </div>
                                                            <template v-if="isPdfFile(list.filename)">
                                                                <div>
                                                                    <span>Browser tidak mendukung preview PDF, </span>
                                                                    <a :href="urlAsset+list.url" download>Download</a>
                                                                </div>
                                                            </template>
                                                            <div v-else class="w-100" style="position: relative">
                                                                <b-img rounded :src="urlbe+list.url" class="w-100" />
                                                                <b-button @click="expandImage(list.url)" v-b-popover.hover.top="'Expand Gambar'" size="sm" variant="w3green" style="position: absolute; right: 0; top: 0;">
                                                                    <feather-icon icon="ExternalLinkIcon" />
                                                                </b-button>
                                                            </div>
                                                        </li>
                                                    <!-- </template> -->
                                                </div>
                                            </section>
                                        </template>
                                    </div>
                                    <!-- <div v-else class="text-center" style="font-style:italic">Tidak ditemukan riwayat penunjang lainnya</div> -->
                                </div>
                            </template>
                        </div>
                        <!-- <b-form-checkbox class="col-5" value="7">Laporan Operasi</b-form-checkbox> -->
                        <!-- <b-form-checkbox class="col-5" value="9">Surat Kontrol</b-form-checkbox> -->
                    </b-form-checkbox-group>
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100 m-0">
                    <b-button @click="validationFormCheckout" :disabled="active.disabled_main || isLoading" size="sm" variant="primary" class="px-16">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>
        <LabResultModalView ref="labModalResult"/>
        <RadResultModalView ref="radModalResult"/>
    </div>
</template>
<script>
import moment from 'moment'
import { mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import dateMixin from "@/mixins/dateMixin";
import LabResultModalView from '@/views/pages/patient/medical_support/LabResultModalView.vue';
import RadResultModalView from '@/views/pages/patient/medical_support/RadResultModalView.vue';
import ApexaSpinner from '@/@core/components/spinner/ApexaSpinner.vue'

export default {
    components: {
        RadResultModalView, LabResultModalView,ApexaSpinner
    },
    mixins: [dateMixin],
    data() {
        return {
            company_id: "1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8",
            company_id_sec: "819ed65f-1989-4b39-9bbd-cb307228dfe7",
            hfi_inpatient: '40f06534-07ec-4221-ad9d-dea08196079f',
            userData: JSON.parse(localStorage.getItem('userData')),
            urlbe:process.env.VUE_APP_IMAGE,
            urlAsset:process.env.VUE_APP_ASSETS_BE,
            regData: {},
            optionsCosting: [],
            active: {
                modal_main: false,
                disabled_main: false,
            },
            temp: {
                date: moment(new Date()).format('YYYY-MM-DD'),
                time: moment(new Date()).format('HH:mm'),
            },
            form_checkout:{
                employee_nurse_id:'',
                doctor_id:'',
                health_facility_item_id:'',
                checkout_date:'',
                date_control:'',
                is_bpjs: '',
                is_death: 0,
                is_control: 1,
                noSEP: '',
                noSuratKontrol:'',
                documents:[],
            },
            take_home_medications: '',
            all_nurse: [],
            cppt_list: [],

            history:{
                laboratory:[],
                radiology: [],
                physiotherapy: [],
                examination: [],
            },
            // untuk resume pulang
            items_resume:{},

            has_noSEP: false,

            //spinner
            isLoading:false,

            //history data kontrol BPJS
            controlBPJSData:[],

            //untuk penanda ada data kontrol yang sudah terbuat
            is_onEditControlBPJS: false,

            updatingIsDeathOrControl: false,
            prescription_list:[],

        }
    },

    computed: {
        ...mapState({
            // OptionsHealthFacility: state => state.healthfacility.options.healthfacility,
            OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
            OptionsDoctor: state => state.employee.options.employee,
        }),
    },

    watch: {
        // edit dpjp
        'form_checkout.health_facility_item_id'(val) {
            if (val) {
                return this.$store.dispatch('employee/getParam', val)
            }
        },
        'form_checkout.is_control'(val){
            if (val == 1 || val == 0 || val == false){
                // console.log('form_checkout.is_control', val)

                if (this.updatingIsDeathOrControl) return;
                this.updatingIsDeathOrControl = true;
                if (val == 1) {
                    this.form_checkout.is_death = 0;
                }
                this.updatingIsDeathOrControl = false;

                this.form_checkout.health_facility_item_id = ''
                this.form_checkout.doctor_id = ''
                this.form_checkout.date_control = ''
            }
        },
        'form_checkout.is_death'(val){
            if (val == 1 || val == 0 || val == false){
                // console.log('form_checkout.is_death', val)
                if (this.updatingIsDeathOrControl) return;
                
                this.updatingIsDeathOrControl = true;
                if (val == 1) {
                    this.form_checkout.is_control = 0;
                } 
                else {
                    this.form_checkout.is_control = 1;
                }
                this.updatingIsDeathOrControl = false;


                this.form_checkout.health_facility_item_id = ''
                this.form_checkout.doctor_id = ''
                this.form_checkout.date_control = ''
            }
        }
    },

    methods: {
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },

        triggerLabResult(message) {
            // console.log('triggerlabresult', message)
            this.$refs.labModalResult.seeLaboratoryResult(message);
        },

        triggerRadResult(message) {
            // console.log('triggerRadResult', message)
            this.$refs.radModalResult.seeRadiologyResult(message);
        },

        async parentTrigger(message) {
            this.regData = message.regData
            console.log('message', this.regData);
            
            if (message.gate == 'add_gate') {
                await this.getControlBPJS(this.regData.registration_destination_id)
                if(this.controlBPJSData.length > 0 ){
                    this.is_onEditControlBPJS = true
                    let obj = this.controlBPJSData[0]
                    this.onEditControlBPJS(obj)
                } else {
                    this.onAddControlBPJS()
                }  
            } 
        }, 

        checkBPJS() {
            if (
                Array.isArray(this.regData.registration_insurance) &&
                this.regData.registration_insurance.length > 0
            ) {
                for (const item of this.regData.registration_insurance) {
                    if (item.is_primary) {
                        if (item.company_id === this.company_id) {
                            this.form_checkout.is_bpjs = 1
                            this.form_checkout.noSEP = item.sep ? item.sep : ''
                        } else {
                            this.form_checkout.is_bpjs = 0
                            this.form_checkout.noSEP = ''
                        }

                        if(this.form_checkout.is_bpjs && !this.form_checkout.noSEP){
                            this.has_noSEP = true
                            this.active.disabled_main = true
                        }
                        return
                    }
                }
            } else {
                this.notification('Failed', 'CloseIcon', 'danger', 'Pasien belum mempunyai penjamin')
            }
        },

        precheckDocuments() {
            const docs = this.form_checkout.documents || [];

            if (this.form_checkout.noSEP && !docs.includes('1')) {
                docs.push('1');
            }

            if (this.take_home_medications && !docs.includes('5')) {
                docs.push('5');
            }

            if (this.items_resume?.doctor_in_charge_name && this.regData?.care_instruction_control_date && !docs.includes('8')) {
                docs.push('8');
            }

            if ((this.history.laboratory.length > 0 ||
                this.history.radiology.length > 0  ||
                this.history.physiotherapy.length > 0  ||
                this.history.examination.length > 0  ) &&
                !docs.includes('6')) {
                docs.push('6');
            }

            this.form_checkout.documents = docs;
        },

        async onEditControlBPJS(obj) {
            console.log(obj,'onEditControlBPJS')
            this.resetForm()
            this.openMainModal()
            this.form_checkout.doctor_id = obj?.doctor_id
            this.form_checkout.health_facility_item_id = obj?.health_facility_item_id
            this.form_checkout.noSEP = obj?.noSep
            this.form_checkout.date_control = obj?.tglRencanaKontrol
            this.form_checkout.noSuratKontrol = obj?.no_control
            if(this.regData?.checkout_date){
                this.form_checkout.checkout_date = this.regData?.checkout_date
                console.log(this.form_checkout.checkout_date, 'form_checkout')
                this.temp.date = moment(this.form_checkout.checkout_date).format('YYYY-MM-DD')
                this.temp.time = moment(this.form_checkout.checkout_date).format('HH:mm')    
            }
            await this.getData()
        },

        async onAddControlBPJS(){
            this.resetForm()
            this.openMainModal()
            await this.getData()
        },

        openMainModal() {
            this.active.modal_main = true
        },
        async getData(){
            this.isLoading = true
            let regDesID = ''

            if(this.is_onEditControlBPJS){
                regDesID = this.controlBPJSData[0].registration_destination_id
            } else {
                regDesID = this.regData.registration_destination_id
            }

            //get Cppt list
            // await this.getCpptList(regDesID)

            this.prescription_list = await this.getPrescriptionList(regDesID)
            
            //untuk penunjang lainnya
            await this.getLaboratoryHistory(regDesID)
            await this.getRadiologyHistory(regDesID)
            await this.getPhysiotherapyHistory(regDesID)
            await this.getExaminationList(regDesID)

            //untuk resume pulang
            await this.getResumeList(regDesID)

            //auto-fill
            this.fillTakeHomeMedic(this.prescription_list)   
            
            //validasi bpjs
            if(!this.is_onEditControlBPJS){
                this.checkBPJS()
            } else {
                if(this.controlBPJSData.length > 0){
                    if(this.controlBPJSData[0].noSep){
                        this.form_checkout.is_bpjs = 1
                    } else {
                        this.form_checkout.is_bpjs = 0
                    }
                }
            }

            //validasi pasien meninggal
            if(this.regData.exit_id == '4a6d28c4-e020-425f-91e1-d39699cf679b'){
                this.form_checkout.is_death = 1
            }

            this.precheckDocuments()

            this.isLoading = false
        },

        async getLaboratoryHistory(regDesID) {
            const param = { 
                registration_destination_id: regDesID,
                // limit: this.lab_load.limit_display
            }
            try {
                await this.$store.dispatch('laboratory/getLaboratoryHistoryV2', param)
                .then(response => {
                    // console.log(response, 'getlaboratoryhistory')
                    this.history.laboratory = response.rows                  
                })
                .catch(({message, debug, response}) => {
                    console.error('Error GET laboratory/getLaboratoryHistoryV2 at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET laboratory/getLaboratoryHistoryV2 at: ', error)
            }
        },

        async getRadiologyHistory(regDesID) {
            const param = { 
                registration_destination_id: regDesID,
                // limit: this.rad_load.limit_display
            }
            try {
                await this.$store.dispatch('radiology/getRadiologyHistoryV2', param)
                .then(response => {
                    // console.log(response, 'getRadiologyHistory')
                    this.history.radiology = response.rows                 
                })
                .catch(({message, debug, response}) => {
                    console.error('Error GET radiology/getRadiologyHistoryV2 at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET radiology/getRadiologyHistoryV2 at: ', error)
            }
        },

        async getPhysiotherapyHistory(regDesID) {
            const param = { 
                registration_destination_id: regDesID,
                // limit: this.phy_load.limit_display
            }
            try {
                await this.$store.dispatch('physiotherapy/getPhysiotherapyHistoryV2', param)
                .then(response => {
                    // console.log(response, 'getPhysiotherapyHistory')
                    this.history.physiotherapy = response.rows                    
                })
                .catch(({message, debug, response}) => {
                    console.error('Error GET physiotherapy/getPhysiotherapyHistoryV2 at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET physiotherapy/getPhysiotherapyHistoryV2 at: ', error)
            }
        },

        async getExaminationList(regDesID) {
            const param = {
                // norm: regDesID,
                registration_destination_id: regDesID,
                // limit: this.exa_load.limit_display,
            }

            try {
                await this.$store.dispatch('soap/getExaminationList', param)
                .then(response => {
                    this.history.examination = response.rows
                    // this.exa_load.count = response.iTotal                    
                })
                .catch(({message, debug, response}) => {
                    console.error('Error GET soap/getExaminationList at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET soap/getExaminationList at: ', error)
            }
        },

        isPdfFile(filename) {
            if (!filename || typeof filename !== 'string') return false;
            const last4Chars = filename.slice(-4).toLowerCase();
            return last4Chars === '.pdf';
        },

        expandImage(url){
            const param = this.urlbe + url;
            const newWindow = window.open('', '_blank');
            
            newWindow.document.write(`
                <html>
                    <head>
                        <title>Image Preview</title>
                        <style>
                            body, html {
                                margin: 0;
                                padding: 0;
                                height: 100%;
                                width: 100%;
                            }
                            img {
                                width: 100%;
                                height: auto;
                            }
                        </style>
                    </head>
                    <body>
                        <img src="${param}" alt="Image Preview">
                    </body>
                </html>
            `);
            newWindow.document.close();
        },

        async getResumeList(regDesId) {
            try {
                const response = await this.$store.dispatch('resume/getAllList', { registration_destination_id: regDesId });
                // console.log('resume list response: ', response);

                if (response?.rows?.length > 0) {
                    this.items_resume = response.rows[0];
                    // console.log('this.items_resume: ', this.items_resume);
                }
            } catch (error) {
                console.error('Error fetching resume list:', error);
            }
        },

        // async getCpptList(regdesID) {
        //     let param = { 
        //         registration_destination_id: regdesID 
        //     }
        //     try {                
        //         await this.$store.dispatch('cppt/getListCPPT', param).then((response) => {
        //             // console.log('getCpptList resp', response)
        //             this.cppt_list = response?.rows
        //         }, error => {
        //             console.error('Error while cppt/getListCPPT at: ', error);
        //         })
        //     } catch (error) {
        //         console.error('Error catch while GET cppt/getListCPPT at: ', error);
        //     }
        // },

        async getPrescriptionList(regdesID) {
            try {
                const response = await this.$store.dispatch('prescription/getDetail', {
                    registration_destination_id: regdesID,
                    //obat pulang
                    type_prescription_id: 2
                })
                return response?.length > 0 ? response : [];
            } catch (error) {
                console.error('Error fetching prescription:', error);
                return [];
            }
        },
        
        fillTakeHomeMedic(prescription_list){
            // console.log('fillTakeHomeMedic',prescription_list)
            if(Array.isArray(prescription_list) && prescription_list.length > 0 ){
                //reset obat pulang
                this.take_home_medications = ''

                let lastPrescription = prescription_list[0]
                this.copyPrescription(lastPrescription)

            }
        },
        copyPrescription(obj) {
            let takeHomeText = '';
            if (obj.form_prescription?.length > 0) {
                
                for (let i = 0; i < obj.form_prescription.length; i++) {
                    const prescription = obj.form_prescription[i];
                    const name = prescription.pharmacy_catalog_name || '';
                    // const qty = prescription.qty || '';
                    // const package_name =  prescription.package_name || '';
                    // const used =  prescription.used ? prescription.used : prescription.prescription_item_type_used || '';
                    // const desc =  prescription.desc || '';

                    takeHomeText += name + '\n';
                }
            }
    
            // console.log('initakehometext', takeHomeText)
            if(this.take_home_medications == '-'){
                this.take_home_medications = ''    
            }
            this.take_home_medications += takeHomeText;
            
        },

        async validationFormCheckout() {
            this.form_checkout.checkout_date = `${this.temp.date || moment(new Date()).format('YYYY-MM-DD')} ${this.temp.time || moment(new Date()).format('HH:mm')}`;
            if(this.form_checkout.is_control == true){
                this.form_checkout.is_control = 1
            } else {
                this.form_checkout.is_control = 0
            }

            if(this.form_checkout.is_death == true){
                this.form_checkout.is_death = 1
            } else {
                this.form_checkout.is_death = 0
            }
            
            const formData = {
                registration_destination_id: this.regData.registration_destination_id,
                checkout_date: this.form_checkout.checkout_date,
                date_control: this.form_checkout.date_control,
                doctor_id: this.form_checkout.doctor_id,
                health_facility_item_id: this.form_checkout.health_facility_item_id,
                is_bpjs: this.form_checkout.is_bpjs,
                noSEP: this.form_checkout.noSEP,
                is_death: this.form_checkout.is_death,
                is_control: this.form_checkout.is_control,
                noSuratKontrol:this.form_checkout.noSuratKontrol
            }

            if (!this.form_checkout.checkout_date) return this.notification('Warning','CloseIcon','warning','Tanggal Checkout Wajib diisi!')

            try {
                this.active.disabled_main = true
                await this.$store.dispatch('bed/formPostPutCheckout', formData)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','Sukses Menyimpan Formulir Pasien Pulang')
                        this.active.modal_main = false
                        this.resetForm()
                        // this.triggerToParent()
                    }
                )
                .catch(({message, debug, response}) => {
                    let errData = response?.data
                    let errMessage = errData?.data?.metaData?.message 
                                    || errData?.message 
                                    || 'Unknown error'
                    let errDebug = errData?.debug || 'No debug code'

                    this.notification(
                        `Failed ${errDebug}`,
                        'CloseIcon',
                        'danger',
                        errMessage
                    )
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','Gagal menyimpan Formulir Pasien Pulang')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled_main = false
            }   
        },

        resetForm() {
            this.form_checkout.checkout_date = ''
            this.form_checkout.date_control = ''
            this.form_checkout.doctor_id = ''
            this.form_checkout.health_facility_item_id = ''
            this.form_checkout.is_bpjs = ''
            this.form_checkout.noSEP = ''
            this.form_checkout.documents = []

            this.has_noSEP = false
            this.active.disabled_main = false
        },
        

        triggerToParent() {
            const data = { message: "barangkali tar mau kirim data" };
            this.$emit("childBloodTrigger", data);
        },

        getAllNurseList() {
            try {                
                this.$store.dispatch('employee/getNurseList').then((response) => {
                    this.all_nurse = response
                }, error => {
                    console.error('Error while GET employee/getNurseList at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET employee/getNurseList at: ', error);
            }
        },
        async getControlBPJS(regDesID){
            try {                
                await this.$store.dispatch('bed/getControlBPJSByRegistrationDesId', regDesID).then((response) => {
                    // console.log('bed/getControlBPJSByRegistrationDesId', response.data.data)
                    this.controlBPJSData = response.data.data
                }, error => {
                    console.error('Error while bed/getControlBPJSByRegistrationDesId at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET bed/getControlBPJSByRegistrationDesId at: ', error);
            }
            
        }
    },
    created() {
        this.getAllNurseList()
        this.$store.dispatch('healthfacility/getParam', 'healthfacility')
    }
}
</script>