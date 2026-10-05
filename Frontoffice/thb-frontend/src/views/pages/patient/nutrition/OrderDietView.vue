<template>
    <div>
        <b-modal v-model="active.modal_main" no-close-on-backdrop body-class="p-0">
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ORDER DIET 
                    <span class="text-primary">{{ regData.alias_name }}. {{ regData.name }} | {{localDate(regData.birth_date)}} ({{ parsedAge(regData.birth_date) }}) | {{ regData.norm }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>

            <template v-if="regData.patient_allergy?.length > 0">
                <div class="bg-warning-200 px-16 py-8 mb-8">
                    <div class="fs-14 font-weight-bold">ALERGI</div>
                    <li v-for="item in regData.patient_allergy" class="fs-13">{{ item.name }}</li>
                </div>
            </template>

            <div class="px-16 pb-8">
                <div class="row">
                    <div class="col-lg-4">
                        <label class="fs-13 mb-0">Tanggal</label>
                        <b-form-input type="date" v-model="form_nutrition.date" size="sm" />
                    </div>
                    <div class="col-lg-4">
                        <label class="fs-13 mb-0">Pilihan Diet</label>
                        <v-select v-model="form_nutrition.diet_id" :options="optionsDiet" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm"/>                            
                    </div>
                    <div class="col-lg-4">
                        <label class="fs-13 mb-0">Waktu</label>
                        <b-form-checkbox-group v-model="form_nutrition.time_arr" size="sm">
                            <b-form-checkbox value="1">Pagi</b-form-checkbox>
                            <b-form-checkbox value="2">Siang</b-form-checkbox>
                            <b-form-checkbox value="3">Sore</b-form-checkbox>
                        </b-form-checkbox-group>
                    </div>
                    <div class="col-12 mt-10">
                        <label class="fs-13 mb-0">Catatan</label>
                        <b-form-textarea rows="4" size="sm" v-model="form_nutrition.notes" autocomplete="off" />
                    </div>
                </div>
            </div>

            <template #modal-footer>
                <div class="d-flex justify-content-end w-100 m-0">
                    <b-button @click="validationForm" :disabled="active.disabled_main" size="sm" variant="primary-500">ORDER DIET SEKARANG</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal_edit" title="Tambah Order" no-close-on-backdrop body-class="p-0">
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    PERBARUI ORDER DIET 
                    <span class="text-primary">{{ regData.alias_name }}. {{ regData.name }} | {{localDate(regData.birth_date)}} ({{ parsedAge(regData.birth_date) }}) | {{ regData.norm }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>

            <template v-if="regData.patient_allergy?.length > 0">
                <div class="bg-warning-200 px-16 py-8 mb-8">
                    <div class="fs-14 font-weight-bold">ALERGI</div>
                    <li v-for="item in regData.patient_allergy" class="fs-13">{{ item.name }}</li>
                </div>
            </template>

            <div class="px-16 pb-8">
                <div class="row">
                    <div class="col-lg-4">
                        <label class="fs-13 mb-0">Tanggal</label>
                        <b-form-input type="date" v-model="form_nutrition.date" size="sm" />
                    </div>
                    <div class="col-lg-4">
                        <label class="fs-13 mb-0">Waktu</label>
                        <v-select v-model="form_nutrition.time_type_id" :options="optionsTime" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                            
                    </div>
                    <div class="col-lg-4">
                        <label class="fs-13 mb-0">Pilihan Diet</label>
                        <v-select v-model="form_nutrition.diet_id" :options="optionsDiet" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                            
                    </div>
                    <div class="col-12 mt-10">
                        <label class="fs-13 mb-0">Catatan</label>
                        <b-form-textarea rows="4" size="sm" v-model="form_nutrition.notes" autocomplete="off" />
                    </div>
                </div>
            </div>

            <template #modal-footer>
                <div class="d-flex justify-content-end w-100">
                    <b-button @click="validationForm" :disabled="active.disabled_main" size="sm" variant="primary-500">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>
<script>
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import moment from 'moment'
import dateMixin from "@/mixins/dateMixin";

export default {
    mixins: [dateMixin],
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            active: {
                modal_main: false,
                modal_edit: false,
                disabled_main: false,
            },
            regData: {},
            form_nutrition: {
                id: '',
                registration_destination_id: '',
                health_facility_item_id: '',
                employee_nurse_id: '',
                room_id: '',
                diet_id: '',
                date: moment(new Date()).format('YYYY-MM-DD'),
                time_arr: [],
                time_type_id: 0,
                notes: '',
                status: 0,
            },
            optionsTime: [
                { value : 1, title: 'Pagi' },
                { value : 2, title: 'Siang' },
                { value : 3, title: 'Sore' },
            ],
            optionsDiet: [],
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
            // console.log('message', message);
            
            if (message.gate == 'add_gate') {
                this.regData = message.regData
                this.optionsDiet = message.options
                this.openMainModal()
            } else if (message.gate == 'edit_gate') {
                this.regData = message.regData
                this.optionsDiet = message.options
                this.onEditNutrition(message.nutritionData)
            } else if (message.gate == 'delete_gate') {
                this.removeNutrition(message.nutritionData)
            }
        }, 

        openMainModal() {
            this.active.modal_main = true
            this.resetForm()
        },

        onEditNutrition(obj) {
            this.resetForm()
            this.active.modal_edit = true
            
            this.form_nutrition.id = obj.id
            this.form_nutrition.registration_destination_id = obj.registration_destination_id
            this.form_nutrition.health_facility_item_id = obj.health_facility_item_id
            this.form_nutrition.employee_nurse_id = obj.employee_nurse_id
            this.form_nutrition.room_id = obj.room_id
            this.form_nutrition.diet_id = obj.diet_id
            this.form_nutrition.date = obj.date
            this.form_nutrition.time_type_id = obj.time_type_id
            this.form_nutrition.notes = obj.notes
            this.form_nutrition.status = 0
        },

        removeNutrition(obj) {            
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus order gizi tanggal ${obj.date} ${this.getTimeName(obj.time_type_id)}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('nutrition/deleteNutrition', obj.id)
                    .then(
                        response => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.triggerToParent()
                        },
                        error => {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            console.error('Error While Delete Using nutrition/deleteNutritionl at: ', error)
                        }
                    )
                    .catch(({error, response}) => {
                        this.notification('Failed','CloseIcon','danger',`${error}`)
                    })
                }
            })
        },

        async validationForm() {
            if (this.form_nutrition.id === '') {
                this.form_nutrition.registration_destination_id = this.regData.registration_destination_id
                this.form_nutrition.health_facility_item_id = this.regData.health_facility_item_id
                this.form_nutrition.employee_nurse_id = this.userData.employee_id
                this.form_nutrition.room_id = this.regData.room_id
            }

            try {
                this.active.disabled_main = true
                await this.$store.dispatch('nutrition/postPutNutrition', this.form_nutrition)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_main = false
                        this.active.modal_edit = false
                        this.resetForm()
                        this.triggerToParent()
                    } 
                )
                .catch(({error, response}) => {
                    this.notification('Failed','CloseIcon','danger',`${error}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally{
                this.active.disabled_main = false
            }
        },

        resetForm() {
            this.form_nutrition.id = ''
            this.form_nutrition.registration_destination_id = ''
            this.form_nutrition.health_facility_item_id = ''
            this.form_nutrition.employee_nurse_id = ''
            this.form_nutrition.room_id = ''
            this.form_nutrition.diet_id = ''
            this.form_nutrition.date = moment(new Date()).format('YYYY-MM-DD')
            this.form_nutrition.time_type_id = ''
            this.form_nutrition.time_arr = []
            this.form_nutrition.notes = ''
            this.form_nutrition.status = 0
        },

        getTimeName(time_id) {
            if (time_id == 1) return 'Pagi'
            if (time_id == 2) return 'Siang'
            if (time_id == 3) return 'Sore'
            return '-'
        },

        triggerToParent() {
            const data = { message: "barangkali tar mau kirim data" };
            this.$emit("childTrigger", data);
        },
    },
}
</script>