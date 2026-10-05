<template>
    <div>
        <b-modal v-model="active.modal_main" size="sm" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA MENGISI PROSEDUR UNTUK PASIEN 
                    <span class="text-primary">{{ regData.alias_name }}. {{ regData.name }} | {{localDate(regData.birth_date)}} ({{ parsedAge(regData.birth_date) }}) | {{ regData.norm }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div>
                <div>
                    <label class="fs-13 mb-0">Jenis Tindakan</label>
                    <v-select v-model="type_costing_c_id" :options="optionsTyCosting" :reduce="item => item.value" label="title" placeholder="pilih jenis tindakan" class="select-size-sm" />
                </div>
                <div>
                    <label class="fs-13 mb-0">Tindakan Prosedur</label>
                    <v-select v-model="form_transaction.costing_id" :options="optionsCosting" :reduce="item => item" label="title" placeholder="pilih prosedur" class="select-size-sm" />
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Tanggal</label>
                    <b-form-input type="datetime-local" v-model="form_transaction.date_added" size="sm"></b-form-input>
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Dokter</label>
                    <v-select v-model="form_transaction.doctor_in_charge_id" :options="optionsDoctor" :reduce="item => item.value" label="title" placeholder="pilih dokter" class="select-size-sm" />
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Jumlah</label>
                    <b-form-input size="sm" type="number" min="1" v-model.number="form_transaction.var_2" />
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Keterangan</label>
                    <b-textarea size="sm" v-model="form_transaction.notes" />
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100 m-0">
                    <b-button @click="validationFormTransaction" :disabled="active.disabled_main" size="sm" variant="primary" class="px-16">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>
<script>
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import dateMixin from "@/mixins/dateMixin";
import moment from 'moment'

export default {
    mixins: [dateMixin],
    data() {
        return {
            hfi_inpatient: '40f06534-07ec-4221-ad9d-dea08196079f',
            regData: {},
            optionsDoctor: [],
            optionsCosting: [],
            form_transaction: {
                id: '',
                soap_id: '',
                registration_destination_id: '',
                costing_id: '',
                doctor_in_charge_id: '',
                var_1: '',
                var_2: 1,
                var_3: '',
                notes: '',
                date_added: moment(new Date()).format('YYYY-MM-DD HH:mm:ss'),
            },
            active: {
                modal_main: false,
                disabled_main: false,
            },
            type_costing_c_id: 16,
            optionsTyCosting: [],
        }
    },

    watch: {
        'type_costing_c_id'(value) {
            this.form_transaction.costing_id = ''
            this.onFilterCosting()
        },
    },

    methods: {
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        parentTrigger(message) {
            this.regData = message.regData
            this.optionsDoctor = message.optDoctor
            this.optionsTyCosting = message.optTyCosting
            // console.log(message);

            if (message.gate == 'add_gate') {
                this.onFilterCosting()
                this.openMainModal()
            } else if (message.gate == 'edit_gate') {
                this.onFilterCosting()
                this.onEditProcedure(message.procedureData)
            } else if (message.gate == 'delete_gate') {
                this.removeProcedure(message.procedureData)
            }
        }, 

        onFilterCosting() {
            const company_bpjs = '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8'
            const company_bpjstk = 'bb0d633f-99e1-4990-901b-0417a14e3cb6'
            const company_umum = 'e5a1e978-af77-4901-9ef8-4ebaeee77dfa'
            const intensive_classification = 19
            const active_company = this.regData.registration_insurance[0].company_id
            const active_service_class = this.regData.registration_insurance[0].service_class_id

            let param = {
                classification_one_type_id: this.type_costing_c_id,
                company_insurance_id: '',
                service_class_id: '',
                health_facility_item_id: '',
            }

            param.company_insurance_id = active_company == company_bpjs || active_company == company_bpjstk ? active_company : company_umum
            param.service_class_id = this.regData.health_facility_id == this.hfi_inpatient ? active_service_class : ''

            // Untuk Intensive (classification 19), kirim health_facility_item_id agar filter berdasarkan ruangan (ICU, dll)
            // Untuk lainnya (Rawat Inap, dll), jangan kirim health_facility_item_id jika rawat inap
            if (this.type_costing_c_id == intensive_classification) {
                param.health_facility_item_id = this.regData.health_facility_item_id
            } else {
                param.health_facility_item_id = this.regData.health_facility_id == this.hfi_inpatient ? '' : this.regData.health_facility_item_id
            }

            try {
                this.$store.dispatch('costing/getOptionCosting', param)
                .then(res => {
                    this.optionsCosting = res
                })
            } catch (error) {
                console.error('Error catch at: ', error)
            }
        },

        openMainModal() {
            this.resetForm()
            this.active.modal_main = true
        },

        async validationFormTransaction() {
            const formData = {
                id: this.form_transaction.id,
                soap_id: this.form_transaction.id == '' ? null: this.form_transaction.soap_id,
                registration_destination_id: this.form_transaction.id == '' ? this.regData.registration_destination_id : this.form_transaction.registration_destination_id,
                costing_id: this.form_transaction.costing_id.value,
                doctor_in_charge_id: this.form_transaction.doctor_in_charge_id,
                notes: this.form_transaction.notes,
                var_1: this.form_transaction.costing_id.price,
                var_2: this.form_transaction.var_2,
                var_3: this.form_transaction.costing_id.price*this.form_transaction.var_2,
                date_added: this.form_transaction.date_added
            }
            // console.log('formData', formData);
            if (Number.isInteger(this.form_transaction.var_2) && this.form_transaction.var_2 > 0 && this.form_transaction.costing_id) {
                try {
                    this.active.disabled_main = true
                    await this.$store.dispatch('soap/postPutTransaction', formData)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.active.modal_main = false
                            this.resetForm()
                            this.triggerToParent()
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
            } else {
                this.notification('Warning','CloseIcon','warning','Jumlah Minimal 1')
            }     
        },

        onEditProcedure(obj) {
            this.openMainModal()

            this.form_transaction.id = obj.id
            this.form_transaction.soap_id = obj.soap_id
            this.form_transaction.registration_destination_id = obj.registration_destination_id
            this.form_transaction.costing_id = {
                value: obj.costing_id,
                title: obj.costing_name,
                price: obj.var_1
            }
            this.form_transaction.doctor_in_charge_id = obj.doctor_in_charge_id
            this.form_transaction.notes = obj.notes
            this.form_transaction.var_1 = obj.var_1
            this.form_transaction.var_2 = obj.var_2
            this.form_transaction.date_added = obj.date_added
        },

        removeProcedure(obj) {   
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus prosedur ${obj.costing_name} tanggal ${obj.date_added}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    try {
                        this.$store.dispatch('soap/deleteTransaction', obj.id)
                        .then(
                            async res => {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.triggerToParent()
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },

        resetForm() {
            this.form_transaction.id = ''
            this.form_transaction.soap_id = null
            this.form_transaction.registration_destination_id = ''
            this.form_transaction.costing_id = ''
            this.form_transaction.doctor_in_charge_id = ''
            this.form_transaction.var_1 = ''
            this.form_transaction.var_2 = 1
            this.form_transaction.var_3 = ''
            this.form_transaction.notes = ''
            this.form_transaction.date_added = moment(new Date()).format('YYYY-MM-DD HH:mm:ss')
        },

        triggerToParent() {
            const data = { message: "barangkali tar mau kirim data" };
            this.$emit("childProcedureTrigger", data);
        },
    },
}
</script>