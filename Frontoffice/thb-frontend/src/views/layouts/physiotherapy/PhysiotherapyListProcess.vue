<template>
    <div>
        <div class="col label-head">
            <span class="px-16 text-medium-24 text-neutral-500">DATA PERMINTAAN FISIOTERAPI PASIEN</span>
        </div>
        <section class="col mt-32">
            <b-table-simple responsive class="text-medium-14">
                <b-tbody>
                    <b-tr>
                        <b-td style="width: 10%;">No. Reg / RM</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{regData.code}} / {{regData.norm}}</b-td>
                        <b-td style="width: 10%;">Unit</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">FISIOTERAPI</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width: 10%;">Nama Pasien</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{regData.name}}</b-td>
                        <b-td style="width: 10%;">No. Order</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{orderData.no_request}}</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width: 10%;">Tanggal Lahir</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{regData.birth_date}}</b-td>
                        <b-td style="width: 10%;">Tgl. Permintaan</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{orderData.date_request}}</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width: 10%;">Kelamin</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{regData.sex_name}}</b-td>
                        <b-td style="width: 10%;">Dokter Pengirim</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{regData.doctor_in_charge_name}}</b-td>
                    </b-tr>
                </b-tbody>
            </b-table-simple>
        </section>
        <section class="col mt-40">
            <b-table bordered responsive head-variant="light" hover small :fields="physiotherapy_action.field"
                :items="orderData.physiotherapy_request_item" tbody-tr-class="text-medium-14" class="mt-12 text-center">
                <template #cell(print_all)="data">
                    <div>
                        <b-form-checkbox />
                    </div>
                </template>
                <template #cell(costing_name)="data">
                    <div style="min-width: 140px;">
                        <span>{{ data.value }}</span>
                    </div>
                </template>
                <template #cell(var_1)="data">
                    <div style="min-width: 140px;">
                        <span>{{ data.value | currency }}</span>
                    </div>
                </template>
                <template #cell(status)="row">
                    <div style="min-width: 140px;">
                        <template v-if="row.item.status == '1'">
                            Permintaan Baru
                        </template>
                        <template v-if="row.item.status == '2'">
                            <router-link :to="{ path: '/physiotherapy/list/process/result/'+ orderData.id+'/'+row.item.id}">
                                <b-button size="sm" variant="orange-400" class="text-white">
                                    <feather-icon icon="EyeIcon" /> Lihat Hasil Pemeriksaan
                                </b-button>
                            </router-link>
                        </template>
                    </div>
                </template>
                <template #cell(patient_type)="data">
                    <div style="min-width: 140px;">
                        <span>{{ data.value }}</span>
                    </div>
                </template>
                <template #cell(bill_status)="data">
                    <div style="min-width: 140px;">
                        <b-badge :variant="data.value === 'Sudah Melunasi Pembayaran' ? 'success-500' : 'warning-500'" class="p-1">
                            <feather-icon icon="CheckSquareIcon" /> {{ data.value }}
                        </b-badge>
                    </div>
                </template>
            </b-table>
        </section>
        <section class="col mt-24" v-if="orderData.status == '2' || orderData.status == '3'">
            <div class="mt-24">
                <div>
                    <label class="c-form-label">Kesimpulan:</label>
                    <b-textarea v-model="form.conclusion" :disabled="orderData.status == 3 ? true : false" rows="2"></b-textarea>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Catatan :</label>
                    <b-textarea v-model="form.notes" :disabled="orderData.status == 3 ? true : false" rows="2"></b-textarea>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Saran :</label>
                    <b-textarea v-model="form.advice" :disabled="orderData.status == 3 ? true : false" rows="2"></b-textarea>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Pemeriksa Fisioterapi:</label>
                    <v-select v-model="form.user_lab_id" aria-readonly="true" :options="OptionsLabor" :disabled="orderData.status == 3 ? true : false" :reduce="(OptionsLabor) => OptionsLabor" label="title" placeholder="-- Dokter --"></v-select>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Verifikator:</label>
                    <v-select v-model="form.doctor_lab_id" aria-readonly="true" :options="OptionsDoctor" :disabled="orderData.status == 3 ? true : false" :reduce="(OptionsDoctor) => OptionsDoctor" label="title" placeholder="-- Dokter --"></v-select>
                </div>
            </div>
        </section>
        <section class="col mt-24">
            <div class="row mx-0">
                <!-- <b-button size="sm" variant="primary-500" class="mr-2">
                    <feather-icon icon="PrinterIcon" /> Print All
                </b-button> -->
                <router-link :to="{ path: '/patient/physiotherapy/'+ regData.registration_destination_id}" class="rounded-pill" >
                    <b-button size="sm" variant="outline-secondary" class="mr-2">
                        <feather-icon icon="FileTextIcon" /> Tambah Tindakan
                    </b-button>
                </router-link>
                <template v-if="orderData.status == '1'">
                    <b-button size="sm" :disabled="isDisabled"  @click.prevent="validationForm(id)" variant="orange-400" class="text-white">
                        <feather-icon icon="SaveIcon" /> Prosess
                    </b-button>
                </template>
                <template v-if="orderData.status == '2'">
                    <b-button size="sm" :disabled="isDisabled"  @click.prevent="validationFormVerifikasi(id)" variant="orange-400" class="text-white">
                        <feather-icon icon="SaveIcon" /> Verifikasi
                    </b-button>
                </template>
                <template v-if="orderData.status == '3'">
                    <router-link :to="{ path: '/physiotherapy/print/'+ id}">
                        <b-button size="sm" variant="outline-secondary" class="mr-2">
                            <feather-icon icon="PrinterIcon" /> Cetak
                        </b-button>
                    </router-link>
                </template>
                <!-- <router-link :to="{ name: 'physiotherapyListProcessOrderView' }">
                    <b-button size="sm" variant="outline-secondary">
                        <feather-icon icon="PrinterIcon" /> Cetak Order
                    </b-button>
                </router-link> -->
            </div>
        </section>
    </div>
</template>

<script>
import { mapState } from 'vuex';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import vSelect from 'vue-select'
export default {
    components: {
      vSelect, ToastificationContent
    },
    data() {
        return {
            physiotherapy_action: {
                field: [
                    { key: 'no', label: 'NO' },
                    { key: 'date_request', label: 'TGL. PERMINTAAN' },
                    { key: 'costing_name', label: 'TINDAKAN FISIOTERAPI' },
                    { key: 'var_1', label: 'HARGA TINDAKAN' },
                    { key: 'status', label: 'ACTION' },
                    { key: 'bill_status', label: 'STATUS BILLING' },
                ],
                // item: [
                //     {
                //         request_date: '24-03-2023 11:04:05',
                //         costing_name: 'Hematologi Lengkap',
                //         var_1: '104.500,00',
                //         bill_status: 'Sudah Melunasi Pembayaran',
                //     },
                // ],
            },
            form:{
                id:'',
                user_lab_id:'',
                doctor_lab_id:'',
                conclusion:'',
                notes:'',
                advice:''
            },
            orderData:{},
            id:'',
            isDisabled:false,
            regData:{}
        }
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat("en-US", { style: "currency", currency: "IDR" }).format(value)
        }
    },
  computed: {
    ...mapState({
      OptionsDoctor: state => state.employee.options.employee,
      OptionsLabor: state => state.employee.options.labor,
    })
  },
    methods: {
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        refresh () {
            router.go(router.currentRoute)
        },
        getData (id) {
			this.$store.dispatch('physiotherapy/getByIdOrder', id).then(data => {
                this.orderData = data[0]
                this.regData = data[0].regData
                this.form.conclusion = this.orderData.conclusion
                this.form.notes = this.orderData.notes
                this.form.advice = this.orderData.advice
                this.form.user_lab_id = {value:this.orderData.user_lab_id,title:this.orderData.user_lab_name}
                this.form.doctor_lab_id = {value:this.orderData.doctor_lab_id,title:this.orderData.doctor_lab_name}
            })
        },
        validationForm (id) {
            this.isDisabled = true
            // this.$refs.simpleRules.validate()
            // .then(success => {
            //     if (success) {
                const formData = {id:id}
                this.$store.dispatch('physiotherapy/formPostPutProcess', formData)
                .then(res => {
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.getData(this.$route.params.id)
                    this.isDisabled = false
                }, 
                err => {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    this.isDisabled = false
                })
                // } else {
                // this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                // }
            // })
        },
        validationFormVerifikasi (id) {
            this.isDisabled = true
            // this.$refs.simpleRules.validate()
            // .then(success => {
            //     if (success) {
                const formData = this.form
                this.$store.dispatch('physiotherapy/formPostPutVerify', formData)
                .then(res => {
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.getData(this.$route.params.id)
                    this.isDisabled = false
                }, 
                err => {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    this.isDisabled = false
                })
                // } else {
                // this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                // }
            // })
        }
    },
    mounted () {
        if (this.$route.params.id) {
            // this.show = true
            this.id = this.$route.params.id
            this.form.id = this.$route.params.id
            this.getData(this.$route.params.id)
        }
        this.$store.dispatch('employee/getParamSpecialistLab')
        this.$store.dispatch('employee/getParamLabor')
    }
}
</script>
<style scoped>
.label-head::before {
    content: '';
    border-left: 6px solid #648DE1;
}
</style>
<style lang="scss">
@import "@core/scss/vue/libs/vue-select.scss";
</style>