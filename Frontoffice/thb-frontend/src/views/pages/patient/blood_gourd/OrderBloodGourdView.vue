<template>
    <div>
        <b-modal v-model="active.modal_main" size="sm" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    TRANSFUSI DARAH 
                    <span class="text-primary">{{ regData.alias_name }}. {{ regData.name }} | {{localDate(regData.birth_date)}} ({{ parsedAge(regData.birth_date) }}) | {{ regData.norm }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div>
                <div>
                    <label class="fs-13 mb-0">Transfusi Darah</label>
                    <v-select v-model="form_blood.costing_id" :options="optionsCosting" :reduce="item => item" label="title" placeholder="Transfusi Darah" class="select-size-sm" />
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Jumlah</label>
                    <b-form-input size="sm" type="number" min="1" v-model.number="form_blood.var_2" />
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Keterangan</label>
                    <b-textarea size="sm" v-model="form_blood.notes" />
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100 m-0">
                    <b-button @click="validationBlood" :disabled="active.disabled_main" size="sm" variant="primary" class="px-16">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>
<script>
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import dateMixin from "@/mixins/dateMixin";

export default {
    mixins: [dateMixin],
    data() {
        return {
            hfi_inpatient: '40f06534-07ec-4221-ad9d-dea08196079f',
            regData: {},
            optionsCosting: [],
            form_blood: {
                id: '',
                registration_destination_id: '',
                costing_id: '',
                var_1: '',
                var_2: 1,
                var_3: '',
                notes: '',
            },
            active: {
                modal_main: false,
                disabled_main: false,
            },
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

        parentTrigger(message) {
            this.regData = message.regData
            // console.log('message', this.regData);
            
            if (message.gate == 'add_gate') {
                this.getCostingOption(this.regData)
                this.openMainModal()
            } else if (message.gate == 'edit_gate') {
                this.getCostingOption(this.regData)
                this.onEditBlood(message.bloodData)
            } else if (message.gate == 'delete_gate') {
                this.removeBlood(message.bloodData)
            }
        }, 

        getCostingOption(obj){
            let temp_param = {
                type_costing_id: 9,
                // service_class_id: obj.service_class_id,
                // company_insurance_id: obj.registration_insurance[0].company_id,
                // health_facility_item_id: obj.health_facility_id == this.hfi_inpatient ? '' : obj.health_facility_item_id
            }

            try {
                this.$store.dispatch('costing/getOptionCosting', temp_param)
                .then(res => {
                    this.optionsCosting = res
                    // console.log('this.optionsCosting', this.optionsCosting);
                })
            } catch (error) {
                console.error('Error catch at: ', error) 
            }
        },

        openMainModal() {
            this.resetForm()
            this.active.modal_main = true
        },

        async validationBlood() {
            const formData = {
                id: this.form_blood.id,
                registration_destination_id: this.form_blood.id == '' ? this.regData.registration_destination_id : this.form_blood.registration_destination_id,
                costing_id: this.form_blood.costing_id.value,
                notes: this.form_blood.notes,
                var_1: this.form_blood.costing_id.price,
                var_2: this.form_blood.var_2,
                var_3: this.form_blood.costing_id.price*this.form_blood.var_2
            }

            if (!this.form_blood.costing_id) return this.notification('Warning','CloseIcon','warning','Alat Medis wajib diisi')

            if (Number.isInteger(this.form_blood.var_2) && this.form_blood.var_2 > 0) {
                try {
                    this.active.disabled_main = true
                    await this.$store.dispatch('blood/formPostPutTransaction', formData)
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

        onEditBlood(obj) {
            this.openMainModal()
            // console.log(obj)
            this.form_blood.id = obj?.id
            this.form_blood.registration_destination_id = obj?.registration_destination_id
            this.form_blood.costing_id = {
                value: obj?.costing_id,
                title: obj?.costing_name,
                price: obj?.var_1
            }
            this.form_blood.notes = obj?.notes
            this.form_blood.var_1 = obj?.var_1
            this.form_blood.var_2 = obj?.var_2
        },

        removeBlood(obj) {   
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus transfusi darah ${obj.costing_name} tanggal ${obj.date_added}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('blood/remove', obj.id)
                    .then(
                        response => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.triggerToParent()
                        },
                        error => {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            console.error('Error While Delete Using blood/remove at: ', error)
                        }
                    )
                    .catch(({error, response}) => {
                        this.notification('Failed','CloseIcon','danger',`${error}`)
                    })
                }
            })
        },

        resetForm() {
            this.form_blood.id = ''
            this.form_blood.costing_id = ''
            this.form_blood.registration_destination_id = ''
            this.form_blood.var_1 = ''
            this.form_blood.var_2 = 1
            this.form_blood.var_3 = ''
            this.form_blood.notes = ''
        },

        triggerToParent() {
            const data = { message: "barangkali tar mau kirim data" };
            this.$emit("childBloodTrigger", data);
        },
    },
}
</script>