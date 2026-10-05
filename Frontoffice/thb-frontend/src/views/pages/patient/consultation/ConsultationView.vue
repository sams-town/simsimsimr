<template>
    <div>
        <b-modal v-model="active.modal_consultation" size="lg" title="Lembar konsultasi" no-close-on-backdrop hide-footer>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI LEMBAR KONSULTASI
                    <span class="text-primary"> {{ regData.alias_name }}.{{ regData.name }} | {{ regData.norm }} | {{localDate(regData.birth_date)}} ({{ parsedAge(regData.birth_date) }})</span>
                </div>
                <feather-icon icon="XIcon" @click="close('close')" size="18" class="pointer" />
            </template>
    
            <!-- <template v-if="userData.employee_id === this.regData.doctor_in_charge_id || this.userData.employee_id === this.admin_id"> -->
            <template v-if="isInCharge(regData.doctor_in_charge_id)">
                <div class="d-flex mb-16">
                    <b-button @click="changeConsultTab('LIST')" pill size="sm" :variant="active.tabConsult == 'LIST' ? 'primary' : 'outline-primary'" class="px-16 mr-2">List Konsultasi</b-button>
                    <b-button @click="changeConsultTab('ADD')" pill  size="sm" :variant="active.tabConsult == 'ADD' ? 'primary' : 'outline-primary'" class="px-16">Tambah Konsultasi</b-button>
                </div>
            </template>
    
            <section v-if="active.tabConsult == 'LIST'" class="row">
                <div class="col">
                    <b-table fixed bordered :fields="consultFields" :items="consultation_list" small thead-class="text-bold-14" tbody-tr-class="text-regular-12">
                        <template #cell(no)="row">
                            <div class="text-center">{{ row.index + 1 }}</div>
                        </template>

                        <template #cell(consultation_type_id)="row">
                            <div class="mb-2 px-2 py-1" style="font-weight: 600;" :class="getContVariant(row.value)">{{ getConsultationTypeName(row.value) }}</div>
                            <div>{{ row.item.doctor_in_charge_name }} ke</div>
                            <div>{{ row.item.consultant_name }}</div>
                        </template>

                        <template #cell(icd10)="row">
                            <div class="mb-1" v-if="row.value?.length > 0">
                                <li v-for="list in row.value">{{ list.icd10_code }} {{ list.icd10_name }}</li>
                            </div>
                            <div>{{ row.item.clinical_history }}</div>
                        </template>

                        <template #cell(cppt_id)="row">
                            <div class="text-center">
                                <template v-if="row.value">
                                    <i @click="answerCppt(row.item)" style="color: blue; text-decoration: underline; cursor: pointer;">Lihat balasan</i>
                                </template>
                                <template v-else>
                                    <i>Belum ada balasan</i>
                                </template>
                            </div>
                        </template>

                        <template #cell(action)="row">
                            <div v-if="row.item.is_cancel == 0">
                                <div v-if="isColaborator(row.item.consultant_id)" class="text-center">
                                    <template v-if="row.item.cppt_id">
                                        <template v-if="row.item.status == 0">   
                                            <template v-if="row.item.consultation_type_id == 1">                            
                                                <b-button @click="onDoneConfirmation(row.item)" size="sm" variant="orange-500" class="mr-1" v-b-popover.hover.top="'Konfirmasi perawatan bersama'">
                                                    <feather-icon icon="CheckSquareIcon" />
                                                </b-button>
                                            </template>

                                            <template v-if="row.item.consultation_type_id == 2">
                                                <b-button @click="onShiftConfirmation(row.item)" size="sm" variant="primary-500" class="mr-1" v-b-popover.hover.top="'Konfirmasi / tolak alih rawat'">
                                                    <feather-icon icon="CheckSquareIcon" />
                                                </b-button>                            
                                            </template>

                                            <template v-if="row.item.consultation_type_id == 3">  
                                                <b-button @click="doneConsultation(row.item)" size="sm" variant="orange-500" class="mr-1" v-b-popover.hover.top="'Selesaikan konsultasi'">
                                                    <feather-icon icon="CheckSquareIcon" />
                                                </b-button>                          
                                            </template>
                                        </template>
                                        <template v-else>{{ getLiveConsultStatus({ consultation_type_id: row.item.consultation_type_id, status: row.item.status }) }}</template>
                                    </template>

                                    <template v-else>                            
                                        <b-button @click="answerCppt(row.item)" size="sm" variant="w3green" class="mr-1">
                                            <feather-icon icon="SendIcon" />
                                        </b-button>
                                    </template>
                                </div>

                                <div v-if="isInCharge(row.item.doctor_in_charge_id)" class="text-center">
                                    <template v-if="row.item?.cppt_id">{{ getLiveConsultStatus({ consultation_type_id: row.item.consultation_type_id, status: row.item.status }) }}</template>
                                    <template v-else>
                                        <b-button @click="onEditConsultation(row.item)" size="sm" variant="warning-500" class="mr-1">
                                            <feather-icon icon="EditIcon"/>
                                        </b-button>
                                        <b-button @click="cancelConsultation(row.item)" size="sm" variant="danger-500" class="mr-1">
                                            <feather-icon icon="XSquareIcon" />
                                        </b-button>
                                    </template>
                                </div>         
                            </div>
                            <div v-if="row.item.is_cancel == 1" class="text-center">Dibatalkan</div>
                        </template>
                    </b-table>
                </div>
            </section>
            
            <section v-if="active.tabConsult == 'ADD'" class="row">
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Dokter Kolaborator</label>
                    <v-select v-model="form_consultation.consultant_id" :disabled="disabledConsType" :options="doctor_list" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                </div>
    
                <div class="col-lg-3 mt-8 mt-lg-0">
                    <label class="fs-13 mb-0">Konsultasi yang diminta</label>
                    <v-select v-model="form_consultation.consultation_type_id" :disabled="disabledConsType" :options="optCunsultType" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm"/>
                </div>
    
                <!-- <div class="col-lg-3 mt-8 mt-lg-0">
                    <label class="fs-13 mb-0">Tanggal</label>
                    <b-form-input v-model="form_consultation.date" type="date" size="sm" />
                </div> -->
    
                <div class="col-12 w-100 mt-16">
                    <ModernLabel label="Diagnosa (ICD10)" />
                    <template v-if="form_consultation.icd10.length > 0">
                        <b-table responsive bordered :fields="icd10Fields" :items="form_consultation.icd10" class="mt-12 text-center" style="font-size: 14px;" >
                            <template #cell(no)="row">{{ row.index+1 }}</template>
                            <template #cell(title)="row">
                                <div class="text-left">{{ row.value }}</div>
                            </template>
                            <template #cell(action)="row">
                                <b-button variant="danger-600" size="sm" @click="deleteConsult10(row.index)" class="mr-2">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </template>
                        </b-table>
                    </template>                                    
                    <v-select v-model="icdConsult10_id" @input="onAddConsult10($event)" aria-readonly="true" @search="onSearchConsult10" :options="ConsultICD10" :reduce="item => item" label="title" placeholder="-- Diagnosa --" class="select-size-sm mt-12" />
                </div>
                <div class="col-12 mt-10">
                    <label class="fs-13 mb-0">Pesan</label>
                    <textarea v-model.lazy="form_consultation.clinical_history" rows="4" class="form-control form-control-sm" autocomplete="off" />                
                </div>
    
                <div class="d-flex justify-content-end w-100 mt-24 px-3">
                    <b-button variant="primary" @click="validationConsultation()" :disabled="active.disabledConsultation" size="sm" class="px-32">SIMPAN</b-button>
                </div>
            </section>
    
            <!-- <section v-if="active.tabConsult == 'ANSWER'" class="mt-16">
                <table class="custom-border" style="font-size: 12px;">
                    <tr>
                        <td class="align-top" style="width: 150px;">Dokter DPJP</td>
                        <td class="align-top" style="width: 20px;">:</td>
                        <td class="align-top">{{ form_consultation.doctor_in_charge_name }}</td>
                    </tr>
                    <tr>
                        <td class="align-top">ICD10</td>
                        <td class="align-top">:</td>
                        <td class="align-top">
                            <template v-if="form_consultation.icd10?.length > 0">
                                <li v-for="list in form_consultation.icd10">{{ list.title }}</li>
                            </template>
                        </td>
                    </tr>
                    <tr>
                        <td class="align-top">Pesan</td>
                        <td class="align-top">:</td>
                        <td class="align-top">{{ parsedBody(form_consultation.clinical_history) }}</td>
                    </tr>
                </table>
                <div class="col-12 px-0 mt-10">
                    <label class="fs-13 mb-0">Jawaban Konsultasi</label>
                    <textarea v-model.lazy="form_consultation.consultation_answer" rows="4" class="form-control form-control-sm" autocomplete="off" />                
                </div>
                <div class="d-flex justify-content-end w-100 mt-24">
                    <b-button variant="outline-primary" @click="changeConsultTab('LIST')" class="mr-1" size="sm">BATAL</b-button>
                    <b-button variant="primary" @click="validationAnswer" :disabled="active.disabledAnswer" size="sm" class="px-32">SIMPAN</b-button>
                </div>
            </section> -->
        </b-modal>   

        <b-modal ref="confirmShiftModal" size="sm">
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase text-center w-100">Konfirmasi Perubahan Dokter Penanggung Jawab (DPJP)</div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div class="text-center fs-13">
                <div>Anda akan melakukan alih rawat dari:</div>
                <div class="font-weight-bold">{{ temp_shift.doctor_in_charge_name }}</div>
                <div>ke: <span class="font-weight-bold">{{ temp_shift.consultant_name }}</span></div>
                
                <div class="mt-16">Apakah Anda setuju dengan perubahan ini?</div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-center w-100">
                    <b-button @click="proceedShift(temp_shift.consult_row, 2)" size="sm" variant="outline-danger-500" class="mr-2 px-16">Tolak</b-button>
                    <b-button @click="proceedShift(temp_shift.consult_row, 1)" size="sm" variant="w3green" class="px-16">Ya, Setuju</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal ref="confirmDoneModal" size="sm">
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase text-center w-100">KONFIRMASI PERAWATAN BERSAMA</div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div class="text-center fs-13">
                <div>Anda akan mengonfirmasi perawatan bersama antara:</div>
                <div class="font-weight-bold">{{ temp_done.doctor_in_charge_name }}</div>
                <div>dan <span class="font-weight-bold">{{ temp_done.consultant_name }}</span></div>
                
                <div class="mt-16">Setujui kolaborasi perawatan?</div>
                <!-- <div class="mt-16">Setujui kolaborasi perawatan antara {{ temp_done.doctor_in_charge_name }} dan {{ temp_done.consultant_name }}?</div> -->
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-center w-100">
                    <b-button @click="proceedDone(temp_done.consult_row, 2)" size="sm" variant="outline-danger-500" class="mr-2 px-16">Tolak</b-button>
                    <b-button @click="proceedDone(temp_done.consult_row, 1)" size="sm" variant="w3green" class="px-16">Ya, Setuju</b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>

<script>
import { mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import dateMixin from '@/mixins/dateMixin'
import optionMixin from '@/mixins/optionMixin'
// import moment from 'moment'

export default {
    mixins: [dateMixin, optionMixin],
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            admin_id: 'd77142c4-4777-47d8-bd0c-fd7dfd134af4',
            position_general_doctor: 'd93bd445-df1e-451a-bf95-5fc10546c133',
            nurse_group_id: '29644626-5b54-4d54-ab69-22146a631277',
            regData: {},
            consultation_list: [],
            lastCpptData: {},
            active: {
                modal_consultation: false,
                tabConsult: 'LIST',
                disabledConsultation: false,
                disabledConsType: false,
                disabledAnswer: false,
                is_consultation: false,
            },

            form_consultation: {
                id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                consultant_id: '',
                // date: moment(new Date()).format('YYYY-MM-DD'),
                icd10: [],
                clinical_history: '',
                consultation_type_id: '',
                consultation_answer: '',

                // optional
                doctor_in_charge_name: ''
            },

            icdConsult10_id:'',
            ConsultICD10:[],
            
            icd10Fields: [
                { key: 'no', label: 'No', thStyle: 'width: 60px' },
                { key: 'title', label: 'ICD10', thClass: 'text-left' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 100px' },
            ],
            doctor_list: [],
            consultFields: [
                { key: 'no', label: 'No', thStyle: { width: '50px', textAlign: 'center' }, },
                { key: 'consultation_type_id', label: 'Konsultasi', thStyle: { width: '280px' }, },
                { key: 'icd10', label: 'ICD10 & Pesan' },
                { key: 'cppt_id', label: 'Balasan Konsultasi', thStyle: { width: '180px', textAlign: 'center' }, },
                { key: 'action', label: 'Aksi', thStyle: { width: '150px', textAlign: 'center' }, },
            ],

            temp_shift: {
                doctor_in_charge_name: '',
                consultant_name: '',
                consult_row: {}
            }, 

            temp_done: {
                doctor_in_charge_name: '',
                consultant_name: '',
                consult_row: {}
            }, 
        }
    },

    computed: {
        ...mapState({ 
            optCunsultType: state => state.frontoptions.doctor.consultation.consultation_type,
        }),
    },

    methods: {
        parentTrigger(message) {
            // console.log('message: ', message);
            this.regData = message?.regData
            this.doctor_list = message?.doctor_list
            this.getConsultation()
            this.getLastCppt(message?.regData?.registration_destination_id)

            this.active.tabConsult = 'LIST'
            this.active.modal_consultation = true
        },

        getConsultation(){
            // console.log('masuk ke getConsultation');
            const param_consult = {
                registration_destination_id: this.regData?.registration_destination_id
            }

            try {                
                this.$store.dispatch('consultation/getListConsultation', param_consult).then((response) => {
                    let temp_data = response.rows
                    // console.log('dapet data: ', temp_data);                        

                    // if (this.userData.employee_id === this.regData.doctor_in_charge_id || this.userData.employee_id === this.admin_id) {
                    if (this.isInCharge(this.regData.doctor_in_charge_id)) {
                        this.consultation_list = temp_data
                    } else {
                        this.consultation_list = []
                        for (let i = 0; i < temp_data.length; i++) {
                            const el = temp_data[i];
                            if (this.userData.employee_id === el.consultant_id || this.userData.user_group.id === this.nurse_group_id ) {
                                this.consultation_list.push(el)
                            }
                        }
                    }

                    // console.log('consultation_list: ', this.consultation_list);
                }, error => {
                    console.error('Error while GET consultation/getListConsultation at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET consultation/getListConsultation at: ', error);
            }
        },

        getLastCppt(regDesId) {
            let params = {
                registration_destination_id: regDesId
            }

            try {                
                this.$store.dispatch('cppt/getCpptList', params).then((response) => {
                    if (response?.rows?.length > 0) {
                        this.lastCpptData = response.rows[0]
                    }
                    // console.log('lastCpptData: ', this.lastCpptData);
                }, error => {
                    console.error('Error while cppt/getCpptList at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET cppt/getCpptList at: ', error);
            }
        },

        changeConsultTab(tab){
            this.resetConsultation()
            this.active.tabConsult = tab

            if (tab == 'ADD') {
                const default_message = 'Mohon untuk melakukan asesmen dan tata laksana sesuai dengan bidang teman sejawat.'
                this.form_consultation.clinical_history = default_message

                let tempt_icd10 = this.lastCpptData?.icd10
                if (tempt_icd10) {
                    const mappedIcd10 = tempt_icd10.map(item => {
                        return {
                            icd10_id: item.icd10_id,
                            value: item.icd10_id,
                            title: `${item.icd10_code} ${item.icd10_name}`, 
                            action: "action"
                        };
                    });                
                    this.form_consultation.icd10 = mappedIcd10;
                }
            }
        },

        resetConsultation() {
            this.disabledConsType = false

            this.form_consultation.id = ''
            this.form_consultation.registration_destination_id = ''
            this.form_consultation.doctor_in_charge_id = ''
            this.form_consultation.consultant_id = ''
            // this.form_consultation.date = moment(new Date()).format('YYYY-MM-DD'),
            this.form_consultation.icd10 = []
            this.form_consultation.clinical_history = ''
            this.form_consultation.consultation_type_id = ''
            this.form_consultation.consultation_answer = ''
            this.form_consultation.doctor_in_charge_name = ''
        },
        
        validationConsultation() {
            // console.log('helooooo form_consult: ', this.form_consultation);
            if (this.form_consultation.id == '') {
                this.form_consultation.registration_destination_id = this.regData?.registration_destination_id
                this.form_consultation.doctor_in_charge_id = this.regData?.doctor_in_charge_id
            }

            try {
                this.active.disabledConsultation = true
                this.$store.dispatch('consultation/postPutConsultation', this.form_consultation)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.tabConsult = 'LIST'
                        this.resetConsultation()
                        this.getConsultation()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabledConsultation = false
            }
        },

        onEditConsultation(obj) {
            this.changeConsultTab('ADD')
            this.disabledConsType = true
            this.fillConsultation(obj)
        },
        
        // onAnswerConsultation(obj) {
        //     this.changeConsultTab('ANSWER')
        //     this.fillConsultation(obj)
        // },

        // validationAnswer() {
        //     try {
        //         this.active.disabledAnswer = true
        //         this.$store.dispatch('consultation/postPutConsultation', this.form_consultation)
        //         .then(
        //             res => {
        //                 this.notification('Success','CheckIcon','success','You have successfully')
        //                 this.active.tabConsult = 'LIST'
        //                 this.resetConsultation()
        //                 this.getConsultation()
        //             }
        //         )
        //         .catch(({message, debug, response}) => {
        //             this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
        //         })
        //     } catch (error) {
        //         this.notification('Failed','CloseIcon','danger','You have unsuccessful')
        //         console.error('Error catch at: ', error)                
        //     } finally {
        //         this.active.disabledAnswer = false
        //     }
        // },

        answerCppt(obj) {
            // console.log('obj:', obj);

            const message = {
                gate: obj?.cppt_id ? 'edit_gate' : 'main_gate', 
                registration_destination_id: obj?.registration_destination_id,
                cppt_id: obj?.cppt_id || null,
                triggerFrom: obj?.cppt_id ? null : 'consultation'
            }
            // console.log('message', message);
            this.$emit("openCPPT", message);
        },

        onShiftConfirmation(obj) {
            this.temp_shift.doctor_in_charge_name = obj?.doctor_in_charge_name
            this.temp_shift.consultant_name = obj?.consultant_name
            this.temp_shift.consult_row = obj
            
            this.$refs.confirmShiftModal.show()
        },

        // shiftConsultation(obj) {
        //     this.$bvModal
        //         .msgBoxConfirm(`Apakah anda ingin konfirmasi alih rawat DPJP dari ${obj.doctor_in_charge_name} menjadi ${obj.consultant_name}?`, {
        //         title: 'Konfirmasi Alih Rawat', size: 'sm', okVariant: 'w3green', okTitle: 'Ya, Konfirmasi', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
        //     })
        //     .then(value => {
        //         if (value === true){
        //             this.proceedShift(obj)                    
        //         }
        //     })
        // },

        proceedShift(obj, status) {
            const param = {
                consultation_id: obj.id,
                data: {
                    status: status
                }
            }
            // console.log('param: ', param);
            try {
                this.$store.dispatch('consultation/putShiftConsultation', param)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.getConsultation()
                        this.refreshRegistration()
                        this.$refs.confirmShiftModal.hide()                        
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')                        
                console.error('error at: ', error)
            }
        },

        cancelConsultation(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin membatalkan ${this.getConsultationTypeName(obj.consultation_type_id)} ke ${obj.consultant_name}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Batalkan', cancelTitle: 'Tidak', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    // this.proceedCancel(obj)
                    try {
                        this.$store.dispatch('consultation/cancelConsultation', obj.id)
                        .then(
                            res => {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getConsultation()
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('error at: ', error);                
                    }
                }
            })
        },

        // proceedCancel(obj) {
        //     try {
        //         this.$store.dispatch('consultation/cancelConsultation', obj.id)
        //         .then(
        //             res => {
        //                 this.notification('Success','CheckIcon','success','You have successfully')
        //                 this.getConsultation()
        //                 this.$refs.confirmShiftModal.hide()
        //             }
        //         )
        //         .catch(({message, debug, response}) => {
        //             this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
        //         })
        //     } catch (error) {
        //         this.notification('Failed','CloseIcon','danger','You have unsuccessful')
        //         console.error('error at: ', error);                
        //     }
        // },

        onDoneConfirmation(obj) {
            this.temp_done.doctor_in_charge_name = obj?.doctor_in_charge_name
            this.temp_done.consultant_name = obj?.consultant_name
            this.temp_done.consult_row = obj
            
            this.$refs.confirmDoneModal.show()
        },

        doneConsultation(obj) {            
            let groupMessage = `Anda akan mengakhiri sesi ${this.getConsultationTypeName(obj.consultation_type_id)} dengan ${obj.doctor_in_charge_name} untuk pasien ${this.regData.alias_name}.${this.regData.name}`

            this.$bvModal.msgBoxConfirm(groupMessage, {
                title: `SELESAIKAN KONSULTASI SEKARANG?`, titleClass: "fs-14 font-weight-bold", size: 'sm', okVariant: 'w3green', okTitle: `Ya, Selesaikan`, cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.proceedDone(obj, 1)
                }
            })
        },

        proceedDone(obj, status) {
            const param = {
                consultation_id: obj.id,
                data: {
                    status: status
                }
            }
            try {
                this.$store.dispatch('consultation/putDoneConsultation', param)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.$refs.confirmDoneModal.hide()                        
                        this.getConsultation()
                        this.refreshRegistration()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')                        
                console.error('error at: ', error)
            }
        },

        refreshRegistration() {
            let message = this.regData.registration_destination_id
            this.$emit("refreshRegistration", message);
        },
        
        fillConsultation(obj) {
            this.form_consultation.id = obj?.id || ''
            this.form_consultation.registration_destination_id = obj?.registration_destination_id || ''
            this.form_consultation.doctor_in_charge_id = obj?.doctor_in_charge_id || ''
            this.form_consultation.consultant_id = obj?.consultant_id || ''
            // this.form_consultation.date = obj?.date || ''

            if (obj.icd10) {
                const mappedIcd10 = obj.icd10.map(item => {
                    return {
                        icd10_id: item.icd10_id,
                        value: item.icd10_id,
                        title: `${item.icd10_code} ${item.icd10_name}`, 
                        action: "action"
                    };
                });                
                this.form_consultation.icd10 = mappedIcd10;
            } else {
                this.form_consultation.icd10 = []
            }

            this.form_consultation.clinical_history = obj?.clinical_history || ''
            this.form_consultation.consultation_type_id = obj?.consultation_type_id || ''
            this.form_consultation.consultation_answer = obj?.consultation_answer || ''
            this.form_consultation.doctor_in_charge_name = obj?.doctor_in_charge_name || ''
        },

        getContVariant(consultation_type_id) {
            if (consultation_type_id == 1) return 'bg-success-200'
            if (consultation_type_id == 2) return 'bg-info-200'
            if (consultation_type_id == 3) return 'bg-orange-200'
            return ''
        },

        isColaborator(consultant_id){
            if (this.userData.employee_id === consultant_id) {
                return true
            }
            return false
        },

        isInCharge(doctor_id){
            return this.userData.employee_id == doctor_id || this.userData?.employee_id == this.admin_id || this.userData?.position?.id == this.position_general_doctor
        },

        getLiveConsultStatus(obj) {
            if (obj.consultation_type_id == 3 && obj.status == 1) return 'Konsultasi selesai'
            if ((obj.consultation_type_id == 1 || obj.consultation_type_id == 2 || obj.consultation_type_id == 3) && obj.status == 0) return 'Menunggu Konfirmasi'
            if ((obj.consultation_type_id == 1 || obj.consultation_type_id == 2) && obj.status == 1) return 'Dikonfirmasi'
            if ((obj.consultation_type_id == 1 || obj.consultation_type_id == 2) && obj.status == 2) return 'Ditolak'
            return '-'
        },

        onSearchConsult10(search, loading) {
            if(search.length >= 3) {
                loading(true);
                this.$store.dispatch('icd10/getParam', search).then(data => {
                    this.ConsultICD10 = []
                    for (let i = 0; i < data.length; i++) {
                        const option = []
                        for (const key in data[i]) {
                            if (key === 'id') { option['value'] = data[i][key] } else if (key === 'name') { option['title'] = data[i]['code']+' '+data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
                        }
                        this.ConsultICD10.push(Object.assign({}, option))
                    }
                    loading(false)
                })
            }
        },

        onAddConsult10 (event) {
            this.icdConsult10_id = ''

            if (this.form_consultation.icd10.some(item => item.icd10_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD10 yang sama')
            } else {
                this.form_consultation.icd10.push({ icd10_id:event.value, title:event.title, value:event.value, action: 'action'})
            }

        },

        deleteConsult10 (index) {
            this.form_consultation.icd10.splice(index, 1)
        },
        
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        parsedBody(text){
            return text ? text.replace(/\n/g,"<br>") : ''
        },   
    },

}
</script>