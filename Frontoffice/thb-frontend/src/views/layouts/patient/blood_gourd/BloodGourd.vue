<template>
    <div>
        <patientMenu selectedMenu="menu10" />
        <div class="label-head my-16 my-lg-24">
            <span class="px-16 text-bold-24 text-neutral-700">Labu Darah</span>
        </div>
        <!-- ////////  -->
        <div class="card-body shadow-sm rounded">
            <div class="row mx-0">
                <div class="col-12 col-lg">
                    <div class="row">
                        <div class="col-12 col-lg-3">
                            <img src="@/@core/assets/images/pendaftaran/user.png" alt="your-photo"
                                class="rounded-circle shadow-sm c-img">
                        </div>
                        <div class="col-12 col-lg">
                            <span class="text-bold-18 text-indigo">
                                <img src="@/@core/assets/icons/nurse/user.svg" alt="user-icon">{{this.regData.norm}}
                            </span>
                            <p class="mb-0 mt-12 text-semi-24 text-neutral-800">
                                <span>({{this.regData.alias_name}}) </span>
                                <span>{{this.regData.name}}</span>
                            </p>
                            <div class="row mx-0 mt-16">
                                <b-badge class="rounded text-neutral-500 text-uppercase border border-success-200 px-10 py-1" :variant="this.regData.is_new == 'N' ? 'success-200' : 'info-200'">{{ this.regData.is_new == 'N' ? 'Pasien Lama' : 'pasien Baru' }}</b-badge>
                                <b-badge
                                    class="rounded text-neutral-500 text-uppercase border border-success-500 px-10 py-1 mx-lg-12 "
                                    variant="success-200">{{this.regData.health_facility_name}}</b-badge>
                                <b-badge
                                    class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1 "
                                    variant="warning-200">{{this.regData.health_facility_item_name}}</b-badge>
                                <div v-if="this.regData.registration_insurance">
                                    <div v-for="list in this.regData.registration_insurance">
                                        <b-badge v-if="list.is_cob == 1"class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1" variant="warning-200">COB</b-badge>
                                    </div>
                                </div>
                            </div>
                            <p class="text-neutral-800 mt-8 mt-lg-16">
                                <span class="text-regular-14 mr-2">Status:</span>
                                <span class="text-bold-14">Dalam Penanganan Medis</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 mt-16 mt-lg-0">
                    <div class="d-flex flex-column align-items-lg-end h-100">
                        <div class="row mx-0">
                            <b-button pill variant="outline-indigo" size="sm" class="mr-2">
                                <b-icon icon="arrow-left" /> <span>Kembali</span>
                            </b-button>
                            <administration />
                        </div>
                        <div class="mt-2 mt-lg-auto">
                            <b-button pill variant="outline-indigo" @click="refresh()" size="sm">
                                <b-icon icon="arrow-clockwise" /> <span>Refresh</span>
                            </b-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-24">
            <b-modal v-model="open_modal.blood" title="Tambah Rincian" hide-footer>
                <div>
                    <div class="mt-12">
                        <label class="c-form-label">Labu Darah</label>
                        <v-select v-model="form_transaction.costing_id" aria-readonly="true" :options="OptionsCosting" :reduce="OptionsCosting => OptionsCosting" label="title" placeholder="-- Labu Darah --"></v-select>
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
                    <b-icon icon="plus" /> <span>Tambah Labu Darah</span>
                </b-button>
                <b-table-simple bordered hover resposive>
                    <b-thead>
                        <b-tr>
                            <b-th>Tanggal & Jam</b-th>
                            <b-th>Labu Darah</b-th>
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
                blood: false,
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
            items: state => state.blood.items,
            itemsTransaction: state => state.blood.itemstransaction,
            fieldsTransaction: state => state.blood.fieldstransaction,
            OptionsCosting: state => state.costing.options.costing
        })
    },
    methods: {
        rowOnDelete (obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus tindakan ${obj.costing_name} tanggal ${obj.date_added} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                this.$store.dispatch('blood/remove', obj.id)
                .then(response => {
                    if (response.status === 200) {
                        this.$store.dispatch('blood/getByIdTransaction', this.$route.params.id)
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
                this.$store.dispatch('blood/formPostPutTransaction', formData)
                .then(res => {
                    this.modalShow = false
                    this.open_modal.blood = !this.open_modal.blood
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.$store.dispatch('blood/getByIdTransaction', this.$route.params.id)
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
                this.open_modal.blood = !this.open_modal.blood
                this.form_transaction.registration_destination_id = this.regData.registration_destination_id
                this.form_transaction.view = view
            } else {
                this.open_modal.blood = !this.open_modal.blood
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
        this.$store.dispatch('blood/getByIdTransaction', this.$route.params.id)
        this.$store.dispatch('costing/getParam', {type_costing_id:9})
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
