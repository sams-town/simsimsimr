<template>
    <div>
        <b-modal v-model="active.modal_main" size="sm" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    TAMBAHKAN PEMAKAIAN ALAT DISINI 
                    <span class="text-primary">{{ regData.alias_name }}. {{ regData.name }} | {{localDate(regData.birth_date)}} ({{ parsedAge(regData.birth_date) }}) | {{ regData.norm }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div>
                <div>
                    <label class="fs-13 mb-0">Alat Medis</label>
                    <v-select v-model="form_tool.costing_id" :options="optionsCosting" :reduce="item => item" label="title" placeholder="alat medis" class="select-size-sm" />
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Jumlah</label>
                    <b-form-input size="sm" type="number" min="1" v-model.number="form_tool.var_2" />
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Keterangan</label>
                    <b-textarea size="sm" v-model="form_tool.notes" />
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100 m-0">
                    <b-button @click="validationTool" :disabled="active.disabled_main" size="sm" variant="primary" class="px-16">SIMPAN</b-button>
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
            form_tool: {
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
            console.log('message', this.regData);
            
            if (message.gate == 'add_gate') {
                this.getCostingOption(this.regData)
                this.openMainModal()
            } else if (message.gate == 'edit_gate') {
                this.getCostingOption(this.regData)
                this.onEditTool(message.toolData)
            } else if (message.gate == 'delete_gate') {
                this.removeTool(message.toolData)
            }
        }, 

        getCostingOption(obj){
            let temp_param = {
                type_costing_id: 8,
                service_class_id: obj.service_class_id,
                company_insurance_id: obj.registration_insurance[0].company_id,
                health_facility_item_id: obj.health_facility_id == this.hfi_inpatient ? '' : obj.health_facility_item_id
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

        validationTool() {
            const formData = {
                id: this.form_tool.id,
                registration_destination_id: this.form_tool.id == '' ? this.regData.registration_destination_id : this.form_tool.registration_destination_id,
                costing_id: this.form_tool.costing_id.value,
                notes: this.form_tool.notes,
                var_1: this.form_tool.costing_id.price,
                var_2: this.form_tool.var_2,
                var_3: this.form_tool.costing_id.price*this.form_tool.var_2
            }

            if (!this.form_tool.costing_id) return this.notification('Warning','CloseIcon','warning','Alat Medis wajib diisi')

            if (Number.isInteger(this.form_tool.var_2) && this.form_tool.var_2 > 0) {
                try {
                    this.active.disabled_main = true
                    this.$store.dispatch('tool/postPutTool', formData)
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

        onEditTool(obj) {
            this.openMainModal()

            this.form_tool.id = obj?.id
            this.form_tool.registration_destination_id = obj?.registration_destination_id
            this.form_tool.costing_id = {
                value: obj?.costing_id,
                title: obj?.costing_name,
                price: obj?.var_1
            }
            this.form_tool.notes = obj?.notes
            this.form_tool.var_1 = obj?.var_1
            this.form_tool.var_2 = obj?.var_2
        },

        removeTool(obj) {   
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus pemakaian alat ${obj.costing_name} tanggal ${obj.date_added}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('tool/removeTool', obj.id)
                    .then(
                        response => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.triggerToParent()
                        },
                        error => {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            console.error('Error While Delete Using tool/removeTool at: ', error)
                        }
                    )
                    .catch(({error, response}) => {
                        this.notification('Failed','CloseIcon','danger',`${error}`)
                    })
                }
            })
        },

        resetForm() {
            this.form_tool.id = ''
            this.form_tool.costing_id = ''
            this.form_tool.registration_destination_id = ''
            this.form_tool.var_1 = ''
            this.form_tool.var_2 = 1
            this.form_tool.var_3 = ''
            this.form_tool.notes = ''
        },

        triggerToParent() {
            const data = { message: "barangkali tar mau kirim data" };
            this.$emit("childToolTrigger", data);
        },
    },
}
</script>