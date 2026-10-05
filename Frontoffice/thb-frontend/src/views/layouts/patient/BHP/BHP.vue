<template>
    <div>
        <patientMenu selectedMenu="menu11" />
        <div class="label-head my-16">
            <span class="px-16 text-bold-24 text-neutral-700">Sewa Kamar</span>
        </div>
        <PatientBanner />
        <div class="mt-24">
            <b-modal v-model="open_modal.bhp" title="Tambah Rincian" hide-footer>
                <div>
                    <div class="mt-12">
                        <label class="c-form-label">Sewa Kamar</label>
                        <v-select v-model="form_transaction.costing_id" aria-readonly="true" :options="OptionsCosting" :reduce="OptionsCosting => OptionsCosting" label="title" placeholder="-- Sewa Kamar --"></v-select>
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
                    <b-icon icon="plus" /> <span>Tambah</span>
                </b-button>
                <b-table-simple bordered hover resposive>
                    <b-thead>
                        <b-tr>
                            <b-th>Tanggal & Jam</b-th>
                            <b-th>Sewa Kamar</b-th>
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
                                <b-td>{{ itemsResult.var_1 | currency }}</b-td>
                                <b-td>
                                    <b-button @click="openModalTransaction('edit', itemsResult)" size="sm" variant="warning-500" class="mr-2">
                                        <feather-icon icon="EditIcon" />
                                    </b-button>
                                    <b-button @click="rowOnDelete(result.id)" size="sm" variant="danger-700">
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
                bhp: false,
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
                var_2:'',
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
            items: state => state.bhp.items, 
            itemsTransaction: state => state.bhp.itemstransaction, 
            fieldsTransaction: state => state.bhp.fieldstransaction, 
            OptionsCosting: state => state.costing.options.costing
        })
    },
    methods: {
        refresh () {
            router.go(router.currentRoute)
        },
        getData (id) {
			this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]
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
                    var_2:'0',
                    var_3:'0'
                }
                this.$store.dispatch('bhp/formPostPutTransaction', formData)
                .then(res => {
                    this.modalShow = false
                    this.open_modal.bhp = !this.open_modal.bhp
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.$store.dispatch('bhp/getByIdTransaction', this.$route.params.id)
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
                this.open_modal.bhp = !this.open_modal.bhp
                this.form_transaction.registration_destination_id = this.regData.registration_destination_id
                this.form_transaction.view = view
            } else {
                this.open_modal.bhp = !this.open_modal.bhp
                this.form_transaction.id = id.id
                this.form_transaction.view = view
                this.form_transaction.costing_id = {
                    value: id.costing_id,
                    title: id.costing_name,
                    costing: id.var_2
                }
                this.form_transaction.notes = id.notes
                this.form_transaction.var_1 = id.var_1
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
        this.$store.dispatch('bhp/getByIdTransaction', this.$route.params.id)
        this.$store.dispatch('costing/getParam', 10)
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