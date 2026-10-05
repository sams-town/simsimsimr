<template>
    <div>
        <b-modal v-model="active.modal_order" size="lg" body-class="p-0" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="d-flex justify-content-between w-100">
                    <div class="text-uppercase font-weight-bold fs-13 pr-16">
                        Order kamar operasi untuk pasien 
                        <span class="text-primary-500">{{ regData.alias_name }}.{{ regData.name }} | {{ regData.norm }} | {{localDate(regData.birth_date)}} ({{ parsedAge(regData.birth_date) }})</span>
                    </div>
                    <feather-icon icon="XIcon" @click="close()" class="pointer"/>
                </div>
            </template>
            <b-button
                @click="toggleCalendar"
                :variant="showCalendar ? 'danger' : 'success'"
                size="sm"
                class="px-3 ml-3 mt-3 mb-2"
            >
                <feather-icon :icon="showCalendar ? 'EyeOffIcon' : 'EyeIcon'" />
                {{ showCalendar ? 'Sembunyikan Kalender Operasi' : 'Lihat Kalender Operasi' }}
            </b-button>
            <div class="p-16" v-if="showCalendar">
                <div class="text-center mb-1">
                <h2 class="mb-0">Kalender Operasi</h2>
                    <div class="text-muted" style="font-size: 15px;">
                        Berikut adalah kalender operasi sesuai schedule yang telah terkonfirmasi
                    </div>
                </div>
                <div class="calendar-header d-flex justify-content-between align-items-center mb-2" style="max-width:1000px; margin:0 auto;">
                    <div></div> <!-- Empty div to push legend to the right -->
                    <div class="calendar-legend">
                        <span style="background:#ff4d4f;display:inline-block;width:16px;height:16px;border-radius:3px;margin-right:4px;"></span> CITO
                        <span style="background:#1890ff;display:inline-block;width:16px;height:16px;border-radius:3px;margin:0 4px 0 16px;"></span> ELECTIVE
                        <span style="background:#52c41a;display:inline-block;width:16px;height:16px;border-radius:3px;margin:0 4px 0 16px;"></span> ODC
                    </div>
                </div>
                <EventCalendar v-if="showCalendar"
                ref="eventCalendar"
                :events="calendarEvents"
                :initial-view="'dayGridMonth'"
                :header-toolbar="{
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                }"
                @goto-day="onGotoDay"
                @dates-set="onCalendarDatesSet"
                />
            </div>
            <section class="p-16 bg-warning-100">
                <table class="fs-13">
                    <tr>
                        <th class="align-top" style="width: 150px;">Diagnosis ICD10</th>
                        <th class="align-top px-1" style="width: 20px;">:</th>
                        <td class="align-top">
                            <template v-if="getICD10.icd10.length > 0">
                                <li v-for="(item, index) in getICD10.icd10" :key="index">{{ item.code }} {{ item.name }}</li>
                            </template>
                            <div v-else>-</div>
                        </td>
                    </tr>
                    <tr>
                        <th class="align-top">Catatan ICD10</th>
                        <th class="align-top px-1">:</th>
                        <td class="align-top">{{ getICD10.icd10_notes ? getICD10.icd10_notes : '-' }}</td>
                    </tr>
                    <tr>
                        <th class="align-top">Diagnosis Non-ICD10</th>
                        <th class="align-top px-1">:</th>
                        <td class="align-top">
                            <template v-if="getICD10.non_icd10.length > 0">
                                <li v-for="(item, index) in getICD10.non_icd10" :key="index">{{ item }}</li>
                            </template>
                            <div v-else>-</div>
                        </td>
                    </tr>
                </table>
            </section>
            <div class="p-16">
                <div class="row">
                    <div class="col-12 mt-8 mt-lg-0">
                        <label class="fs-13 mb-0">Tindakan</label>
                        <textarea v-model.lazy="form_order.therapy_plan" class="form-control form-control-sm" placeholder="Tindakan" autocomplete="off" />
                    </div>
                    <div class="col-lg-4 mt-8">
                        <label class="fs-13 mb-0">Tanggal Operasi</label>
                        <b-form-input v-model="form_order.operation_date" type="datetime-local" size="sm" />
                    </div>
                    <div class="col-lg-4 mt-8">
                        <label class="fs-13 mb-0">Operator</label>
                        <v-select v-model="form_order.main_operator_id" :options="doctor_list" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="pilih" />
                    </div>
                    <div class="col-lg-4 mt-8">
                        <label class="fs-13 mb-0">Dokter Anestesi</label>
                        <v-select v-model="form_order.main_anesthesia_id" :options="anesthesiologist_list" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="pilih" />
                    </div>
                    <div class="col-lg-4 mt-8">
                        <label class="fs-13 mb-0">Durasi (menit)</label>
                        <b-input-group append="menit">
                            <b-form-input
                                v-model.number="form_order.duration"
                                type="number"
                                min="0"
                                step="1"
                                class="text-right"
                            />
                        </b-input-group>
                    </div>
                    <div class="col-lg-4 mt-8">
                        <label class="fs-13 mb-0">Jenis Operasi</label>
                        <v-select v-model="form_order.operation_type_id" :options="optOperationType" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="pilih" />
                    </div>
                    <div class="col-lg-4 mt-8">
                        <label class="fs-13 mb-0">Infectious</label>
                        <v-select v-model="form_order.infectious_type_id" :options="optInfectious" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="pilih" />
                    </div>
                </div>
            </div>
            <template #modal-footer>
                <div class="w-100 d-flex justify-content-end m-0">
                    <b-button @click="validationOrder" :disabled="disabled.order" variant="primary" size="sm" class="px-3">
                        <feather-icon icon="SaveIcon" /> ORDER SEKARANG
                    </b-button>
                </div>
            </template>
        </b-modal>

        <b-modal scrollable size="md" v-model="active.modal_info" hide-footer body-class="p-0">
            <template #modal-header="{ close }">
                <div class="d-flex justify-content-between w-100">
                    <div class="text-uppercase font-weight-bold fs-13 pr-16">
                        Informasi Operasi 
                        <span class="text-primary-500">{{ regData.alias_name }} {{ regData.name }} | {{ regData.norm }} | {{localDate(regData.birth_date)}} ({{ parsedAge(regData.birth_date) }})</span>
                    </div>
                    <feather-icon icon="XIcon" @click="close()" class="pointer"/>
                </div>
            </template>

            <b-table-simple responsive class="w-100 fs-13 mb-1">
                <b-tr>
                    <b-th class="align-top" style="width: 150px">Nama</b-th>
                    <b-td class="align-top px-1" style="width: 20px">:</b-td>
                    <b-td class="align-top">{{ information?.patient_name || '-' }}</b-td>
                </b-tr>
                <b-tr>
                    <b-th class="align-top">No RM</b-th>
                    <b-td class="align-top px-1">:</b-td>
                    <b-td class="align-top">{{ information?.norm || '-' }}</b-td>
                </b-tr>
                <b-tr>
                    <b-th class="align-top">Tanggl Lahir</b-th>
                    <b-td class="align-top px-1">:</b-td>
                    <b-td class="align-top">{{ information?.birth_date || '-' }}</b-td>
                </b-tr>
                <b-tr>
                    <b-th class="align-top">Operator & Anestesi</b-th>
                    <b-td class="align-top text-center">:</b-td>
                    <b-td class="align-top">
                        <template v-if="information && information.operators">
                            <li v-for="(item, index) in information.operators" :key="index">{{ item.operator_name }}</li>
                        </template>
                        <div v-else>-</div>
                    </b-td>
                </b-tr>
                <b-tr>
                    <b-th class="align-top">Tindakan</b-th>
                    <b-td class="align-top text-center">:</b-td>
                    <b-td class="align-top">
                        {{ information?.therapy_plan || '-' }}
                    </b-td>
                </b-tr>
                <b-tr>
                    <b-th class="align-top">Diagnosa ICD10</b-th>
                    <b-td class="align-top px-1">:</b-td>
                    <b-td class="align-top">
                        <template v-if="information?.icd10?.length > 0">
                            <li v-for="(item, index) in information.icd10" :key="index">{{ item.icd10_code }} {{ item.icd10_name }}</li>
                        </template>
                        <div v-else>-</div>
                    </b-td>
                </b-tr>
                <b-tr>
                    <b-th class="align-top">Tindakan ICD9CM</b-th>
                    <b-td class="align-top px-1">:</b-td>
                    <b-td class="align-top">
                        <template v-if="information?.icd9cm?.length > 0">
                            <li v-for="(item, index) in information.icd9cm" :key="index">{{ item.icd9cm_code }} {{ item.icd9cm_name }}</li>
                        </template>
                        <div v-else>-</div>
                    </b-td>
                </b-tr>
                <b-tr>
                    <b-th class="align-top">Rencana Operasi</b-th>
                    <b-td class="align-top px-1">:</b-td>
                    <b-td class="align-top">{{ information?.operation_approved_date || '-' }}</b-td>
                </b-tr>
                <b-tr>
                    <b-th class="align-top">Ruang Operasi</b-th>
                    <b-td class="align-top px-1">:</b-td>
                    <b-td class="align-top">{{ information?.room_name || '-' }} {{ information?.bed_name }}</b-td>
                </b-tr>
            </b-table-simple>
        </b-modal>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import dateMixin from "@/mixins/dateMixin";
import moment from 'moment'
import EventCalendar from '@/@core/components/calendar/EventCalendar.vue'

export default {
    mixins: [dateMixin],
    components: {
        EventCalendar
    },
    data() {
        return {
            calendarEvents: [],
            hfi_inpatient: '40f06534-07ec-4221-ad9d-dea08196079f',
            hfi_outpatient: '3cb626f5-9148-4fb1-8663-3d7f028b01a6',
            hfi_emergency: '444b3134-5b75-4b67-af72-a558d46716d7',

            showCalendar:false,

            global_gate : '',
            active: {
                modal_order: false,
                modal_info: false,
            },
            disabled: {
                order: false,
            },

            regData: {},
            soapData: {},
            doctor_list: [],

            form_order: {
                id: '',
                registration_destination_id: '',
                soap_id: '',
                main_operator_id: '',
                main_anesthesia_id:'',
                duration:'',
                operation_date: '',
                infectious_type_id: 0,
                operation_type_id: 0,
                icd9cm: [],
                therapy_plan: '',
                // accomodation_arry: [],
            },

            getICD10: {
                icd10: [],
                icd10_notes: '',
                non_icd10: [],
            },
            information: {},
        }
    },
    computed: {
        ...mapState({ 
            optOperationType: state => state.frontoptions.general.operatie.operation_type,
            optInfectious: state => state.frontoptions.general.operatie.infectious,   
        }),
        anesthesiologist_list() {
            const anesthesiologists = this.doctor_list.filter(item => {
                const cleanName = item?.title?.replace(/\s+/g, ' ').replace(/\s*,\s*/g, ',').trim(); 
                if(cleanName){
                    const parts = cleanName.split(',Sp.');
                    if (parts.length < 2) return false;

                    const specialization = parts[1].trim().toLowerCase();
                    return specialization.includes('an');
                }
            });

            return anesthesiologists
        },
    },
    methods: {
        seeCaledar () {
            const now = new Date();
            const start = new Date(now.getFullYear(), now.getMonth(), 1);
            const end = new Date(now.getFullYear(), now.getMonth() + 1, 1); // exclusive
            this.getCalendarOperatingList(start, end);
            this.showCalendar = true
        },

        toggleCalendar() {
            if (this.showCalendar) {
                this.showCalendar = false;
            } else {
                this.seeCaledar();
            }
        },

        async parentTrigger(message) {
            // console.log('message: ', message);
            this.global_gate = message?.gate
            this.regData = message?.regData
            this.doctor_list = message?.doctor_list
            
            
            if (message?.gate == 'main_gate' || message?.gate == 'edit_gate') {
                await this.assignSoap(message?.regData?.registration_destination_id)
                this.openMainModal(message?.rowData)
            } else if (message?.gate == 'info_gate') {
                this.onModalInfo(message?.rowData)
            } else if (message?.gate == 'cancel_gate') {
                this.cancelOrder(message?.rowData)
            }
        },
        
        async assignSoap(reg_des_id) {
            let params = {
                registration_destination_id: reg_des_id,
                ...(this.regData.health_facility_id == this.hfi_inpatient && { is_initial_inpatient: 1 }),
                ...(this.regData.health_facility_id == this.hfi_outpatient && { is_booking: 0 })
            };

            try {                
                this.$store.dispatch('soap/getDetail', params).then(async response => {
                    if (response?.length > 0) {
                        this.soapData = response[0]
                        if (this.global_gate == 'main_gate') {
                            this.form_order.soap_id = this.soapData.id
                        }

                        this.getICD10.icd10 = this.soapData?.icd10 || []
                        this.getICD10.icd10_notes = this.soapData?.icd10_notes || ''
                        this.getICD10.non_icd10 = this.soapData?.non_icd10 || []

                        // if (this.soapData?.icd9cm) {
                        //     const mappedIcd9cm = this.soapData.icd9cm.map(item => {
                        //         return {
                        //             icd9cm_id: item.icd9cm_id,
                        //             value: item.icd9cm_id,
                        //             title: `${item.code} ${item.name}`, 
                        //             action: "action"
                        //         };
                        //     });                
                        //     this.form_order.icd9cm = mappedIcd9cm;
                        // }
                        // this.form_order.therapy_plan = this.soapData?.therapy_plan
                    }
                }, error => {
                    console.error('Error while soap/getDetail at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET soap/getDetail at: ', error);
            } 
        },

        async openMainModal(obj){
            this.active.modal_order = true
            await this.resetOrderForm()

            if (obj && this.global_gate == 'edit_gate') this.fillOkForm(obj)
        },

        mapOperatingListToCalendarEvents(list) {
            const colorMap = {
                CITO: '#ff4d4f',      // Red for CITO
                ELECTIVE: '#1890ff',  // Blue for ELECTIVE
                ODC: '#52c41a',       // Green for One Day Care
                '-': '#888'           // Default/gray
            };
        
            return list.map(item => {
                // Determine operation type label
                const typeLabel = item.operation_type_id === 1
                    ? 'CITO'
                    : item.operation_type_id === 2
                        ? 'ELECTIVE'
                        : item.operation_type_id === 3
                            ? 'ODC'
                            : '-';
                // Join all operator names, if any, for display
                const operatorNames = Array.isArray(item.operator_name) && item.operator_name.length
                    ? item.operator_name.join(', ')
                    : '';

                // Build concise event title (type is now color-coded, not in title)
                const label =
                    `[${item.alias_name}] ${item.patient_name}` +
                    (item.therapy_plan ? ` — ${item.therapy_plan}` : '') +
                    (operatorNames ? ` — ${operatorNames}` : '');

                // Parse duration in minutes
                let durationMinutes = null;
                if (item.duration) {
                    if (typeof item.duration === 'string') {
                        const match = item.duration.match(/\d+/);
                        durationMinutes = match ? parseInt(match[0], 10) : null;
                    } else if (typeof item.duration === 'number') {
                        durationMinutes = item.duration;
                    }
                }
        
                // Format start as ISO string
                let start = item.operation_approved_date
                    ? item.operation_approved_date.replace(' ', 'T')
                    : null;
        
                // Calculate end as ISO string
                let end = null;
                if (start && durationMinutes) {
                    const startDate = new Date(start);
                    if (!isNaN(startDate.getTime())) {
                        const endDate = new Date(startDate.getTime() + durationMinutes * 60000);
                        const pad = n => n.toString().padStart(2, '0');
                        end = `${endDate.getFullYear()}-${pad(endDate.getMonth() + 1)}-${pad(endDate.getDate())}T${pad(endDate.getHours())}:${pad(endDate.getMinutes())}:${pad(endDate.getSeconds())}`;
                    }
                }
        
                // Build and return the event object for FullCalendar
                return {
                    title: '',
                    start: start,
                    end: end,
                    resourceId: item.room_number, 
                    backgroundColor: colorMap[typeLabel],
                    borderColor: colorMap[typeLabel],
                    extendedProps: {
                        ...item,
                        eventLabel: label,
                        operator_names: operatorNames,
                        birth_date: item.birth_date,
                        therapy_plan: item.therapy_plan,
                        operation_type: typeLabel,
                        patient_name: item.patient_name,
                        alias_name: item.alias_name,
                        // add more if needed
                    }
                };
            });
        },


        fillOkForm(obj) {
            this.form_order.id = obj.id
            this.form_order.registration_destination_id = obj.registration_destination_id
            this.form_order.soap_id = obj.soap_id
            this.form_order.main_operator_id = obj.main_operator_id
            this.form_order.operation_date = obj.operation_date
            this.form_order.infectious_type_id = obj.infectious_type_id
            this.form_order.operation_type_id = obj.operation_type_id
            // this.form_order.accomodation_arry = obj.accomodation_arry

            if (obj.icd9cm) {
                const mappedIcd9cm = obj.icd9cm.map(item => {
                    return {
                        icd9cm_id: item.icd9cm_id,
                        value: item.icd9cm_id,
                        title: `${item.icd9cm_code} ${item.icd9cm_name}`, 
                        action: "action"
                    };
                });                
                this.form_order.icd9cm = mappedIcd9cm;
            }
            this.form_order.therapy_plan = obj.therapy_plan
        },

        async resetOrderForm() {
            this.form_order.id = ''
            this.form_order.registration_destination_id = ''
            this.form_order.soap_id = ''
            this.form_order.main_operator_id = ''
            this.form_order.operation_date = ''
            this.form_order.infectious_type_id = 0
            this.form_order.operation_type_id = 0
            this.form_order.icd9cm = []
            this.form_order.therapy_plan = ''
            // this.form_order.accomodation_arry = []
        },

        async validationOrder() {
            this.form_order.registration_destination_id ||= this.regData.registration_destination_id
            // console.log('this.form_order: ', this.form_order);
            
            try {
                this.disabled.order = true
                await this.$store.dispatch('operatie/postPutOrder', this.form_order)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_order = false
                        this.resetOrderForm()
                        this.refreshOkList()
                    } 
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error at: ', error);
            } finally {
                this.disabled.order = false
            }
        },

        cancelOrder(obj){
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin membatalkan operasi ${obj.alias_name}.${obj.patient_name} dengan nomor order ${obj.no_order} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, BATALKAN ORDER', cancelTitle: 'TIDAK', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    try {
                        this.$store.dispatch('operatie/delRemoveOrder', obj.id) 
                        .then(
                            res => {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.refreshOkList()
                            } 
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })                        
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.log('Error at: ', error);
                    }
                }
            })
        },

        onModalInfo(obj) {
            this.information = ''
            this.active.modal_info = true
            this.information = { ...obj }
        },

        refreshOkList() {
            this.$emit("refreshOk", 'initial-message');                        
        },

        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        onGotoDay(date) {
            // console.log('onGotoDay called with date:', date);
            this.$refs.eventCalendar.gotoDay(date);
        },

        onCalendarDatesSet(arg) {
            // fetch by the visible range (not just the month)
            // this.getCalendarOperatingList(arg.start, arg.end);
            if (arg.view.type !== 'timeGridDay') {
                this.getCalendarOperatingList(arg.start, arg.end);
            }
        },
        getOperatingList (){
            try {
                this.$store.dispatch('operatie/getAllList', this.params).then(response => {
                    this.operating_list = response.rows
                    // this.getData()
                    // console.log('Operating List: ', this.operating_list);
                    this.totalRows = response.iTotal
                    this.isDisabledFilter = false
                    // console.log('Calendar Events: ', this.calendarEvents);
                })
            } catch (error) {
                console.log('Error while GET DATA at: ', error);
            }
        },

        getCalendarOperatingList(start, end) {
            const params = {
            date_type: 'approved_date_estimate', // <-- change this if needed
            date_start: moment(start).format('YYYY-MM-DD'),
            date_end: moment(end).subtract(1, 'days').format('YYYY-MM-DD'), // end is exclusive
            limit: 1000,
            offset: 0,
            };

            this.$store.dispatch('operatie/getAllList', params).then(response => {
            response.rows.forEach(item => {
                if (Array.isArray(item.operators)) {
                    item.operator_name = item.operators
                        .filter(op => this.getRoleName(op.operator_role_type_id) === 'Operator')
                        .map(op => op.operator_name);
                } else {
                    item.operator_name = [];
                }
            });
            this.calendarEvents = this.mapOperatingListToCalendarEvents(response.rows);

            });
        },
        getRoleName(item){
            if(item == 1){
               return 'Operator'
            }
            if(item == 2){
                return 'Co-operator'
            }
            if(item == 3){
                return 'Instrumen'
            }
            if(item == 4){
                return 'Circular'
            }
            if(item == 5){
                return 'Anastesi'
            }
        }
    },
    created() {
        // const now = new Date();
        // const start = new Date(now.getFullYear(), now.getMonth(), 1);
        // const end = new Date(now.getFullYear(), now.getMonth() + 1, 1); // exclusive
        // this.getCalendarOperatingList(start, end);
        // this.getOperatingList()
        
    },
}
</script>
