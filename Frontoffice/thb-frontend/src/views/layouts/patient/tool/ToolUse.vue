<template>
    <div>
        <patientMenu selectedMenu="menu9" />
        <div class="label-head my-16">
            <span class="px-16 text-bold-24 text-neutral-700">Pemakaian Alat Medis</span>
        </div>
        <PatientBanner />
        <div class="mt-24">
            <b-modal v-model="open_modal.tool" title="Tambah Rincian" hide-footer>
                <div>
                    <div class="mt-12">
                        <label class="c-form-label">Alat Medis</label>
                        <v-select v-model="form_transaction.costing_id" aria-readonly="true" :options="OptionsCosting" :reduce="OptionsCosting => OptionsCosting" label="title" placeholder="-- Alat Medis --"></v-select>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Jumlah</label>
                        <b-form-input type="number" v-model="form_transaction.var_2" />
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Keterangan</label>
                        <b-textarea v-model="form_transaction.notes" rows="3"></b-textarea>
                    </div>
                    <div class="d-flex justify-content-end mt-24">
                        <b-button variant="indigo-500" @click.prevent="validationFormTransaction" class="px-3">Save</b-button>
                    </div>
                </div>
            </b-modal>
        </div>
        <div class="mt-32">
            <div class="mt-16 mt-lg-24">
                <b-button @click="openModalTransaction('add')" pill variant="outline-neutral-400" size="sm" class="px-16 mb-20 ml-lg-auto">
                    <b-icon icon="plus" /> <span>Tambah Alat Medis</span>
                </b-button>
                <b-table-simple bordered hover resposive>
                    <b-thead>
                        <b-tr>
                            <b-th>Tanggal & Jam</b-th>
                            <b-th>Alat Medis</b-th>
                            <b-th>Keterangan</b-th>
                            <b-th>Harga</b-th>
                            <b-th>Aksi</b-th>
                        </b-tr>
                    </b-thead>
                    <b-tbody>
                        <template v-for="(itemsResult,i) in itemsTransaction" >
                            <b-tr>
                                <b-td>{{ itemsResult.date_added }}</b-td>
                                <b-td>{{ itemsResult.costing_name }}</b-td>
                                <b-td>{{ itemsResult.notes }}</b-td>
                                <b-td>{{ itemsResult.var_1 | currency}}x{{itemsResult.var_2}}&nbsp;&nbsp;{{ itemsResult.var_3 | currency }}</b-td>
                                <b-td>
                                    <b-button @click="openModalTransaction('edit', itemsResult)" size="sm" variant="warning-500" class="mr-2">
                                        <feather-icon icon="EditIcon" />
                                    </b-button>
                                    <b-button @click="rowOnDelete(itemsResult)" size="sm" variant="danger-700">
                                        <feather-icon icon="TrashIcon" />
                                    </b-button>
                                </b-td>
                            </b-tr>
                        </template>
                    </b-tbody>
                </b-table-simple>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import patientMenu from '@/@core/components/tab-menu/patientMenu.vue';
import administration from '@/views/layouts/patient/doctor/administration.vue';
// import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import router from '@/router/index.js'
import vSelect from 'vue-select'

export default {
    name: 'DoctorStudies',
    components: {
        patientMenu, administration, vSelect
    },
    data() {
        return {
            open_modal: {
                tool: false,
                add_list: false,
                edit_list: false,
                remove_list: false,
            },
            userData: JSON.parse(localStorage.getItem('userData')),
            regData: {},
            doctor: {
                fields: [
                    { key: 'tanggal', label: 'Tanggal & Jam' },
                    { key: 'tindakan', label: 'Tindakan Dokter' },
                    { key: 'notes', label: 'Keterangan' },
                    { key: 'action', label: 'Aksi' }
                ],
                items: []
            },
            form_transaction: {
                id:'',
                view:'add',
                costing_id:'',
                registration_destination_id:'',
                notes:'',
                var_1:'',
                var_2:1,
                var_3:''
            }
        }
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat("en-US", { style: "currency", currency: "IDR" }).format(value)
        }
    },
    computed: {
        ...mapState({ 
            items: state => state.tool.items, 
            itemsTransaction: state => state.tool.itemstransaction, 
            fieldsTransaction: state => state.tool.fieldstransaction, 
            OptionsCosting: state => state.costing.options.costing
        })
    },
    methods: {
        refresh () {
            router.go(router.currentRoute)
        },

        rowOnDelete (obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus tindakan ${obj.costing_name} tanggal ${obj.date_added} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                this.$store.dispatch('tool/remove', obj.id)
                .then(response => {
                    if (response.status === 200) {
                        this.$store.dispatch('tool/getByIdTransaction', this.$route.params.id)
                        this.notification('Success','CheckIcon','success','You have successfully')
                    } else {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    }
                },
                error => {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                }) 
                }
            })
        },

        getData (id) {
			this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]
                // if (this.regData.health_facility_id === "40f06534-07ec-4221-ad9d-dea08196079f") {
                //     this.$store.dispatch('costing/getParam', {type_costing_id:3,is_nurse:true,service_class_id:this.regData.service_class_id,health_facility_item_id:'d158a64e-6246-444a-97b7-8d0a2d90e53a'})
                // } else {
                //     this.$store.dispatch('costing/getParam', {type_costing_id:3,is_nurse:true,service_class_id:this.regData.service_class_id})
                // }
                const inpatient_id = "40f06534-07ec-4221-ad9d-dea08196079f"
                const inpatient_item_id = "d158a64e-6246-444a-97b7-8d0a2d90e53a"
                let company_insurance_id = "e5a1e978-af77-4901-9ef8-4ebaeee77dfa"
                let service_class = ""
                let paramsInsuranceServiceClassDoctor = {}

                console.log(this.regData.registration_insurance[0].company_id)

                if (this.regData.registration_insurance[0].company_id === '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8') {
                    company_insurance_id = this.regData.registration_insurance[0].company_id
                } else if (this.regData.registration_insurance[0].company_id === 'bb0d633f-99e1-4990-901b-0417a14e3cb6') {
                    company_insurance_id = this.regData.registration_insurance[0].company_id
                } else {
                    company_insurance_id = "e5a1e978-af77-4901-9ef8-4ebaeee77dfa"
                }
                // list.is_primary === 1 && list.company_id === '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8'"

                if (this.regData.health_facility_id === inpatient_id) {
                    paramsInsuranceServiceClassDoctor = {
                        type_costing_id: 8,
                        company_insurance_id: company_insurance_id,
                        service_class_id: this.regData.registration_insurance[0].service_class_id
                    }
                } else {
                    if (this.regData.health_facility_id === '444b3134-5b75-4b67-af72-a558d46716d7') {
                        paramsInsuranceServiceClassDoctor = {
                            type_costing_id: 8,
                            company_insurance_id: company_insurance_id,
                            service_class_id: 'e7ce65ba-3d51-41aa-add7-df8f6a40e2b4',
                            health_facility_item_id: this.regData.health_facility_item_id
                        }
                    } else {
                        paramsInsuranceServiceClassDoctor = {
                            type_costing_id: 8,
                            service_class_id: 'e7ce65ba-3d51-41aa-add7-df8f6a40e2b4',
                            company_insurance_id: company_insurance_id,
                            health_facility_item_id: this.regData.health_facility_item_id
                        }
                    }
                }
                console.log(paramsInsuranceServiceClassDoctor)

                this.$store.dispatch('costing/getParam', paramsInsuranceServiceClassDoctor)
                // this.form_nurse.employee_nurse_id = this.userData.employee_id
                // this.form_nurse.registration_destination_id = id
                // this.show = false
            })
        },
        validationFormTransaction (id) {
            // this.$refs.simpleRules.validate()
            // .then(success => {
            //     if (success) {
                const formData = {
                    id:this.form_transaction.id,
                    view:this.form_transaction.view,
                    costing_id:this.form_transaction.costing_id.value,
                    registration_destination_id:this.form_transaction.registration_destination_id,
                    notes:this.form_transaction.notes,
                    var_1:this.form_transaction.costing_id.costing,
                    var_2:this.form_transaction.var_2,
                    var_3:this.form_transaction.costing_id.costing*this.form_transaction.var_2
                }
                this.$store.dispatch('tool/formPostPutTransaction', formData)
                .then(res => {
                    this.modalShow = false
                    this.open_modal.tool = !this.open_modal.tool
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.$store.dispatch('tool/getByIdTransaction', this.$route.params.id)
                }, 
                err => {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                })
                // } else {
                // this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                // }
            // })
        },
        openModalTransaction (view) {
            if (view === 'add') {
                this.open_modal.tool = !this.open_modal.tool
                this.form_transaction.registration_destination_id = this.regData.registration_destination_id
                this.form_transaction.view = view
            } else {
                this.open_modal.tool = !this.open_modal.tool
                this.form_transaction.id = id.id
                this.form_transaction.view = view
                this.form_transaction.costing_id = {
                    value: id.costing_id,
                    title: id.costing_name,
                    costing: id.var_2
                }
                this.form_transaction.notes = id.notes
                this.form_transaction.var_1 = id.var_1
                this.form_transaction.var_2 = id.var_2
                this.form_transaction.var_3 = id.var_3
            }
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
    },
    mounted () {
        this.$store.dispatch('tool/getByIdTransaction', this.$route.params.id)
        // this.$store.dispatch('costing/getParam', 8)
        if (this.$route.params.id) {
            console.log(this.items)
            // this.show = true
            this.id = this.$route.params.id
            this.getData(this.$route.params.id)
        }
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>
<style scoped>
@import '../../../../@core/css/utility.css';

.label-head::before {
    content: '';
    border-left: 6px solid #648DE1;
}

.c-img {
    aspect-ratio: 1/1;
    object-fit: cover;
    max-width: 150px;
    max-height: 150px;
    border: 10px solid white;
}
</style>