<template>
    <b-modal ref="confirmTransferModal" size="lg" scrollable no-close-on-backdrop :hide-footer="vHideConfirm">
        <template #modal-header="{ close }">
            <div class="fs-13 font-weight-bold text-uppercase w-100">
                <template v-if="!vHideConfirm">
                    ANDA AKAN MELAKUKAN KONFIRMASI TRANSFER UNTUK 
                    <span class="text-primary">{{ temp_confirm.alias_name }}. {{ temp_confirm.name }}</span>
                </template>
                <template v-else>
                    RIWAYAT TRANSFER 
                    <span class="text-primary">{{ temp_confirm.alias_name }}. {{ temp_confirm.name }}</span>
                    DARI {{ temp_confirm?.selected?.past_health_facility_item_name }} KE {{ temp_confirm.room_abbreviation }} BED {{ temp_confirm.bed_name }}
                </template>
            </div>
            <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
        </template>
        
        <div class="fs-13">
            <!-- <b-button @click="triggerToNotification()">cobain</b-button> -->
            <!-- <b-button @click="checkSoapAlreadyExist()">cobain</b-button> -->
            <template v-if="!vHideConfirm">
                <div class="fs-13 p-12 bg-warning-200 font-italic">
                    Terima transfer dari {{ temp_confirm?.selected?.past_health_facility_item_name }} ke {{ temp_confirm.room_abbreviation }} bed {{ temp_confirm.bed_name }}
                </div>
                <div class="row mt-8">
                    <div class="col-lg-4">
                        <label class="fs-13 mb-0">Waktu pasien sampai dibangsal</label>
                        <b-form-input v-model="form_confirm.arrival_time" type="datetime-local" size="sm" />
                    </div>
                </div>
            </template>
            <div class="row" :class="{'mt-32': !vHideConfirm}">
                <div class="col-lg-4">
                    <ModernLabel label="PROSES TRANSFER" />
                    <table class="w-100 mt-8">
                        <tr>
                            <td class="align-top" style="width: 120px;">Waktu berangkat</td>
                            <td class="align-top text-center" style="width: 20px;">:</td>
                            <td class="align-top">{{ temp_confirm?.selected?.time_out || '-' }}</td>
                        </tr>
                        <tr>
                            <td class="align-top">Mobilisasi</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ getMobilizationName(temp_confirm?.selected?.mobilization || '-') }}</td>
                        </tr>
                        <tr>
                            <td class="align-top">Dokter PJ transfer</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ temp_confirm?.selected?.employee_doctor_name || '-' }}</td>
                        </tr>
                        <tr>
                            <td class="align-top">Perawat PJ transfer</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ temp_confirm?.selected?.employee_nurse_name || '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-lg-4 mt-16 mt-lg-0">
                    <ModernLabel label="DPJP" />
                    <table class="w-100 mt-8">
                        <tr v-if="temp_confirm?.selected?.doctor_in_charge_id">
                            <td class="align-top" style="width: 120px;">Dokter 1 (DPJP)</td>
                            <td class="align-top text-center" style="width: 20px;">:</td>
                            <td class="align-top">{{ temp_confirm?.selected?.doctor_in_charge_name || '-' }}</td>
                        </tr>
                        <tr v-if="temp_confirm?.selected?.doctor_in_charge_id_2">
                            <td class="align-top">Dokter 2</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ temp_confirm?.selected?.doctor_in_charge_name_2 || '-' }}</td>
                        </tr>
                        <tr v-if="temp_confirm?.selected?.doctor_in_charge_id_3">
                            <td class="align-top">Dokter 3</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ temp_confirm?.selected?.doctor_in_charge_name_3 || '-' }}</td>
                        </tr>
                        <tr v-if="temp_confirm?.selected?.doctor_in_charge_id_4">
                            <td class="align-top">Dokter 4</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ temp_confirm?.selected?.doctor_in_charge_name_4 || '-' }}</td>
                        </tr>
                        <tr v-if="temp_confirm?.selected?.doctor_in_charge_id_5">
                            <td class="align-top">Dokter 5</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ temp_confirm?.selected?.doctor_in_charge_name_5 || '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div v-if="temp_confirm?.selected?.past_health_facility_id != hfi_inpatient" class="col-lg-4 mt-16 mt-lg-0">
                    <ModernLabel label="CHECKOUT" />
                    <table class="w-100 mt-8">
                        <tr>
                            <td class="align-top" style="width: 120px;">Kriteria Pasien</td>
                            <td class="align-top text-center" style="width: 20px;">:</td>
                            <td class="align-top">{{ getTypeEmergencyName(temp_confirm?.selected?.type_emergency_id || '-') }}</td>
                        </tr>
                        <tr>
                            <td class="align-top">Jenis Kunjungan</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ getTypeVisitName(temp_confirm?.selected?.type_visit_id || '-') }}</td>
                        </tr>
                    </table>
                </div>
                <div v-if="temp_confirm?.selected?.past_health_facility_id != hfi_inpatient" class="col-12 mt-16">
                    <ModernLabel label="SPRI" />
                    <table class="w-100 mt-8">
                        <tr>
                            <td class="align-top" style="width: 120px;">Indikasi Rawat Inap</td>
                            <td class="align-top text-center" style="width: 20px;">:</td>
                            <td class="align-top">{{ getIndicationName(temp_confirm?.selected?.indication || '-') }}</td>
                        </tr>
                        <tr>
                            <td class="align-top">Keadaan Umum Pasien</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ getConditionName(temp_confirm?.selected?.condition || '-') }}</td>
                        </tr>
                        <tr>
                            <td class="align-top">Rencana Tindakan</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">
                                <div v-html="parsedBody(temp_confirm?.selected?.plan || '-')"></div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-12 mt-16">
                    <ModernLabel label="TTV TERAKHIR SAAT TRANSFER" />
                    <div class="row mt-8">
                        <div class="col-lg-3">
                            <table class="w-100">
                                <tr>
                                    <td class="align-top" style="width: 120px;">Systolic</td>
                                    <td class="align-top text-center" style="width: 20px;">:</td>
                                    <td class="align-top">{{ temp_confirm?.selected?.systolic }} mmHg</td>
                                </tr>
                                <tr>
                                    <td class="align-top">Diastolic</td>
                                    <td class="align-top text-center">:</td>
                                    <td class="align-top">{{ temp_confirm?.selected?.diastolic }} mmHg</td>
                                </tr>
                                <tr>
                                    <td class="align-top">Frekuensi Nadi</td>
                                    <td class="align-top text-center">:</td>
                                    <td class="align-top">{{ temp_confirm?.selected?.pulse_frequency }} x/mnt</td>
                                </tr>
                                <tr>
                                    <td class="align-top">Frekuensi Nafas</td>
                                    <td class="align-top text-center">:</td>
                                    <td class="align-top">{{ temp_confirm?.selected?.breath_frequency }} x/mnt</td>
                                </tr>
                                <tr>
                                    <td class="align-top">Saturasi Oksigen</td>
                                    <td class="align-top text-center">:</td>
                                    <td class="align-top">{{ temp_confirm?.selected?.oxygen_saturation }} %</td>
                                </tr>
                                <tr v-if="isPews">
                                    <td class="align-top">Warna Kulit</td>
                                    <td class="align-top text-center">:</td>
                                    <td class="align-top">{{ getColorSkinName(temp_confirm?.selected?.skin_color) }} </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-lg-3 mt-8 mt-lg-0">
                            <table class="w-100">
                                <tr>
                                    <td class="align-top" style="width: 100px;">Suhu</td>
                                    <td class="align-top text-center" style="width: 20px;">:</td>
                                    <td class="align-top">{{ temp_confirm?.selected?.temperature }} ᵒC</td>
                                </tr>
                                <tr>
                                    <td class="align-top">Oksigenasi</td>
                                    <td class="align-top text-center">:</td>
                                    <td class="align-top">{{ getOxygenName(temp_confirm?.selected?.oxygen_type_id || '-') }} </td>
                                </tr>
                                <tr>
                                    <td class="align-top">Kesadaran</td>
                                    <td class="align-top text-center">:</td>
                                    <td class="align-top">{{ getAwareName(temp_confirm?.selected?.aware) }} </td>
                                </tr>
                                <tr>
                                    <td class="align-top">Berat Badan</td>
                                    <td class="align-top text-center">:</td>
                                    <td class="align-top">{{ temp_confirm?.selected?.weight }} kg</td>
                                </tr>
                                <tr>
                                    <td class="align-top">Tinggi Badan</td>
                                    <td class="align-top text-center">:</td>
                                    <td class="align-top">{{ temp_confirm?.selected?.height }} cm</td>
                                </tr>
                                <tr v-if="isPews">
                                    <td class="align-top">Perilaku</td>
                                    <td class="align-top text-center">:</td>
                                    <td class="align-top">{{ getBehaviorName(temp_confirm?.selected?.behavior) }} </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-lg-6 mt-8 mt-lg-0">
                            <table class="w-100">
                                <tr>
                                    <td class="align-top" style="width: 100px;">GCS Mata</td>
                                    <td class="align-top text-center" style="width: 20px;">:</td>
                                    <td class="align-top">{{ getGcsEyeName(temp_confirm?.selected?.eye_score || '-') }} </td>
                                </tr>
                                <tr>
                                    <td class="align-top">GCS verbal</td>
                                    <td class="align-top text-center">:</td>
                                    <td class="align-top">{{ getGcsVerbalName(temp_confirm?.selected?.verbal_score) }} </td>
                                </tr>
                                <tr>
                                    <td class="align-top">GCS Motorik</td>
                                    <td class="align-top text-center">:</td>
                                    <td class="align-top">{{ getGcsMotoricName(temp_confirm?.selected?.motor_score) }} </td>
                                </tr>
                                <tr>
                                    <td class="align-top">Skor EWS</td>
                                    <td class="align-top text-center">:</td>
                                    <td class="align-top">{{ temp_confirm?.selected?.ews_downe_score }} </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <template #modal-footer>
            <div class="d-flex justify-content-end m-0">
                <b-button @click="confirmTransfer()" :disabled="active.disabled.confirm_transfer" size="sm" variant="w3green">KONFIRMASI TRANSFER</b-button>
            </div>
        </template>
    </b-modal>
</template>

<script>
import moment from 'moment'
import dateMixin from '@/mixins/dateMixin';
import optionMixin from '@/mixins/optionMixin';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import io from 'socket.io-client';

export default {
    mixins: [dateMixin, optionMixin],

    data() {
        return {
            socket: io(process.env.VUE_APP_SOCKET_IO_NOTIF, { path: '/socket.io', transports: ['websocket', 'polling', 'flashsocket'], withCredentials: true}),
            hfi_inpatient: '40f06534-07ec-4221-ad9d-dea08196079f',
            hfi_outpatient: '3cb626f5-9148-4fb1-8663-3d7f028b01a6',
            hfi_emergency: '444b3134-5b75-4b67-af72-a558d46716d7',
            
            isPews: false,
            active: {
                disabled: {
                    confirm_transfer: false,
                },
            },

            form_confirm: {
                registration_destination_id: '',
                arrival_time: moment(new Date()).format('YYYY-MM-DD HH:mm')
            },

            temp_confirm: {
                registration_destination_id: '',
                doctor_in_charge_id: '',
                alias_name: '',
                name: '',
                room_abbreviation: '',
                bed_name: '',
                selected: {}
            },

            form_vital: {
                soap_id: '',
                doctor_in_charge_id: '',
                registration_destination_id: '',
                is_initial_inpatient: 1,
                is_hemodialysis: 0,
                is_medical_rehabilitation: 0,
                is_medical_check_up: 0,
                systolic_blood_pressure: '',
                diastolic_blood_pressure: '',
                pulse_frequency: '',
                breath_frequency: '',
                oximetry_frequency: '',
                temperature: '',
                oxygen_type_id: '',
                aware: '',
                eye_score: '',
                verbal_score: '',
                motor_score: '',
                skin_color: '',
                behavior: '',
                weight: '',
                height: '',
                imt: '',
                head_circumference: '',
                chest_circumference: '',
                arm_circumference: '',
                stomach_circumference: '',
                knee_circumference: ''
            },
        }
    },

    props: {
        placement: {
            type: String,
            default: 'list_fast'
        },

        vHideConfirm: {
            type: Boolean,
            default: false
        },
    },

    methods: {
        async openConfirm(obj) {
            // console.log(obj);
            await this.resetConfirmForm()
            await this.getTransferData(obj?.registration_destination_id)
            this.isPews = this.pewsCriteria(obj?.birth_date)

            // temp
            this.form_confirm.registration_destination_id = obj?.registration_destination_id
            this.temp_confirm.doctor_in_charge_id = obj?.doctor_in_charge_id     
            this.temp_confirm.alias_name = obj?.alias_name
            this.temp_confirm.name = obj?.name
            this.temp_confirm.room_abbreviation = obj?.room_abbreviation
            this.temp_confirm.bed_name = obj?.bed_name     

            this.$refs.confirmTransferModal.show();
        },

        async getTransferData(regDesId) {
            try {                
                this.$store.dispatch('registration/getTreatmentRoom', { registration_destination_id: regDesId })
                .then((response) => {
                    this.temp_confirm.selected = response[0] 
                    // console.log('respnse: ', response);
                }, error => {
                    console.error('Error while GET registration/getTreatmentRoom at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET registration/getTreatmentRoom at: ', error);
            }
        },

        async resetConfirmForm() {
            this.form_confirm.registration_destination_id = ''
            this.temp_confirm.doctor_in_charge_id = ''
            this.temp_confirm.alias_name = ''
            this.temp_confirm.name = ''
            this.temp_confirm.room_abbreviation = ''
            this.temp_confirm.bed_name = ''
            this.temp_confirm.selected = ''
        },

        confirmTransfer() {
            try {
                this.active.disabled.confirm_transfer = true
                this.$store.dispatch('registration/putConfirmTransferV2', this.form_confirm)
                .then(
                    async res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.$refs.confirmTransferModal.hide();
                        this.triggerToParent()
                        this.triggerToNotification()
                        await this.checkSoapAlreadyExist()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)    
            } finally {
                this.active.disabled.confirm_transfer = false
            }
        },

        triggerToParent() {
            let message = 'initial dulu'
            if(this.placement == 'list_fast') return this.$emit("refreshRegdata", message)
            if(this.placement == 'ward') return this.$emit("refreshWard")
        },

        triggerToNotification() {
            let params = {
                health_facility_item_id: this.temp_confirm.selected.to_unit
            }

            try {
                this.$store.dispatch('position/getPosition', params)
                .then(
                    async res => {
                        // console.log('res', res);
                        if (res?.length > 0) {
                            this.socket.emit('SEND_SBAR', {
                                type: 'REFRESH_DATA',
                                position_id: res[0].id,
                            })
                        }
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)    
            }
        },

        async checkSoapAlreadyExist() {
            await this.clearForVital()
            let params = {
                registration_destination_id: this.form_confirm.registration_destination_id,
                is_initial_inpatient: 1,
            }

            try {
                const response = await this.$store.dispatch('soap/getDetail', params)
                if (response.length > 0) {
                    this.form_vital.soap_id = response[0]?.id || ''
                    this.form_vital.head_circumference = response[0]?.head_circumference
                    this.form_vital.chest_circumference = response[0]?.chest_circumference
                    this.form_vital.arm_circumference = response[0]?.arm_circumference
                    this.form_vital.stomach_circumference = response[0]?.stomach_circumference
                    this.form_vital.knee_circumference = response[0]?.knee_circumference
                }

                this.form_vital.doctor_in_charge_id = this.temp_confirm.doctor_in_charge_id
                this.form_vital.registration_destination_id = this.form_confirm.registration_destination_id
                this.form_vital.is_initial_inpatient = 1
                this.form_vital.is_hemodialysis = 0
                this.form_vital.is_medical_rehabilitation = 0
                this.form_vital.is_medical_check_up = 0
                this.form_vital.systolic_blood_pressure = this.temp_confirm?.selected?.systolic
                this.form_vital.diastolic_blood_pressure = this.temp_confirm?.selected?.diastolic
                this.form_vital.pulse_frequency = this.temp_confirm?.selected?.pulse_frequency
                this.form_vital.breath_frequency = this.temp_confirm?.selected?.breath_frequency
                this.form_vital.oximetry_frequency = this.temp_confirm?.selected?.oxygen_saturation
                this.form_vital.temperature = this.temp_confirm?.selected?.temperature
                this.form_vital.oxygen_type_id = this.temp_confirm?.selected?.oxygen_type_id || ''
                this.form_vital.aware = this.temp_confirm?.selected?.aware
                this.form_vital.eye_score = this.temp_confirm?.selected?.eye_score || ''
                this.form_vital.verbal_score = this.temp_confirm?.selected?.verbal_score || ''
                this.form_vital.motor_score = this.temp_confirm?.selected?.motor_score || ''
                this.form_vital.skin_color = this.temp_confirm?.selected?.skin_color || ''
                this.form_vital.behavior = this.temp_confirm?.selected?.behavior || ''
                this.form_vital.weight = this.temp_confirm?.selected?.weight
                this.form_vital.height = this.temp_confirm?.selected?.height
                this.form_vital.imt = this.imtCalculation(this.form_vital?.weight, this.form_vital?.height)

                // console.log(this.form_vital);
                this.saveVital()
            } catch (error) {
                console.error('Error catch while GET soap/getDetail at: ', error);
            }
        },

        async clearForVital() {
            this.form_vital.soap_id = ''
            this.form_vital.doctor_in_charge_id = ''
            this.form_vital.registration_destination_id = ''
            this.form_vital.is_initial_inpatient = ''
            this.form_vital.is_hemodialysis = ''
            this.form_vital.is_medical_rehabilitation = ''
            this.form_vital.is_medical_check_up = ''
            this.form_vital.systolic_blood_pressure = ''
            this.form_vital.diastolic_blood_pressure = ''
            this.form_vital.pulse_frequency = ''
            this.form_vital.breath_frequency = ''
            this.form_vital.oximetry_frequency = ''
            this.form_vital.temperature = ''
            this.form_vital.oxygen_type_id = ''
            this.form_vital.aware = ''
            this.form_vital.eye_score = ''
            this.form_vital.verbal_score = ''
            this.form_vital.motor_score = ''
            this.form_vital.skin_color = ''
            this.form_vital.behavior = ''
            this.form_vital.weight = ''
            this.form_vital.height = ''
            this.form_vital.imt = ''
            this.form_vital.head_circumference = ''
            this.form_vital.chest_circumference = ''
            this.form_vital.arm_circumference = ''
            this.form_vital.stomach_circumference = ''
            this.form_vital.knee_circumference = ''
        },

        saveVital() {
            let fixed_data = {
                exist: 'VITAL',
                data: this.form_vital   
            }

            // console.log('fixdata', fixed_data);
            try {
                this.$store.dispatch('soap/putSections', fixed_data)
                .then(
                    async (res) => {
                        // console.info('Berhasil menyimpan tanda vital')
                        await this.resetConfirmForm()
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('gagal menyimpan tanda vital: ', error)
                })
            } catch (error) {
                console.error('Error catch at: ', error)                
            }
        },

        imtCalculation(weight, height) {
            const brt_badan = parseFloat(weight) || 0
            const tng_badan = parseFloat(height) || 0
            let imtResult = ''
            if (brt_badan && tng_badan) {
                const heightInMeters = tng_badan / 100
                const imt = brt_badan / (heightInMeters ** 2)
                imtResult = imt.toFixed(1)
            } 
            return imtResult
        },

        parsedBody(text){
            return text ? text.replace(/\n/g,"<br>") : ''
        }, 

        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
    },
}
</script>