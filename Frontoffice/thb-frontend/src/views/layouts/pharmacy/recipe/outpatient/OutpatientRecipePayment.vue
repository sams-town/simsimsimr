<template>
    <div>
        <div class="card border-0 bg-neutral-100 mt-32">
            <div class="card-body">
                <p class="text-bold-18">Data Rincian Tagihan Pembelian Obat Bebas</p>
                <div class="row mt-24">
                    <div class="col col-lg-6 px-0 px-lg-4">
                        <b-table-simple class="text-bold-14">
                            <b-tr>
                                <b-td class="border-0" style="width: 30%;">No Resep</b-td>
                                <b-td class="border-0 text-center" style="width: 10%;">:</b-td>
                                <b-td class="border-0" style="display:none">{{form_transaction}}</b-td>
                                <b-td class="border-0">{{form_transaction.no_order}}</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td class="border-0" style="width: 30%;">Nama</b-td>
                                <b-td class="border-0 text-center" style="width: 10%;">:</b-td>
                                <b-td class="border-0">{{form_transaction.fullname}}</b-td>
                            </b-tr>
                        </b-table-simple>
                    </div>
                    <div class="col col-lg-6 px-0 px-lg-4">
                        <b-table-simple class="text-semi-14">
                            <b-tr>
                                <b-td class="border-0" style="width: 30%;">Tanggal</b-td>
                                <b-td class="border-0 text-center" style="width: 10%;">:</b-td>
                                <b-td class="border-0">{{form_transaction.date_added}}</b-td>
                            </b-tr>
                        </b-table-simple>
                    </div>
                </div>
                <div class="mt-20">
                    <b-table-simple responsive bordered>
                        <b-thead class="bg-neutral-100 text-bold-14 text-center">
                            <b-tr>
                                <b-th style="vertical-align: middle; min-width: 400px;">Obat/Alkes</b-th>
                                <b-th style="vertical-align: middle; min-width: 120px;">Harga</b-th>
                                <b-th style="vertical-align: middle; min-width: 5px;">Qty</b-th>
                                <b-th style="vertical-align: middle; min-width: 120px;">Diskon Item</b-th>
                                <b-th style="vertical-align: middle; min-width: 120px;">Total</b-th>
                                <b-th style="vertical-align: middle; min-width: 120px;"> 
                                    <div>Buat Tagihan</div>
                                    <input type="checkbox" @change="toggleSelectAll($event)" />
                                </b-th>
                            </b-tr>
                        </b-thead>
                        <b-tbody>
                            <template v-for="(item, index) in combinedItems">
                                <!-- <b-tr :key="index" :class="item.checked ? 'bg-warning-200' : '' " >                                
                                    <b-td :rowspan="item.transaction_from === 'itemsPres' ? 2 : 1" class="text-center">{{ item.date_added }}</b-td>
                                    <b-td>{{ item.transaction_from !== 'itemsPres' ? item.costing_name : item.no_order }}</b-td>
                                    <b-td class="text-right">{{ item.transaction_from === 'itemsNurse' ? item.var_2 : item.var_1 | currency }}</b-td>
                                    <b-td class="text-center">{{ item.transaction_from === 'itemsDoctor' ? (item.transaction_from === 'itemsBed' || item.transaction_from === 'itemsNurse' ? item.var_1 : item.var_2) :  1 }}</b-td>
                                    <b-td class="text-right">0,00</b-td>
                                    <b-td class="text-right">{{ item.transaction_from === 'itemsDoctor'|| item.transaction_from === 'itemsBed' || item.transaction_from === 'itemsNurse' ? (item.transaction_from === 'itemsBed' ? item.var_1*item.var_2  : item.var_3) : item.var_1 | currency }}</b-td>
                                    <b-td :rowspan="item.transaction_from === 'itemsPres' ? 2 : 1" class="text-center">
                                        <input type="checkbox" v-model="item.checked" @change="updateTotal" />
                                    </b-td>
                                </b-tr> -->
                                <b-tr :key="index" class="text-right" :class="item.checked ? 'bg-warning-200' : '' " v-if="item.is_retur_full !== 1 || item.pharmacy_catalog_id_retur">
                                    <b-td class="text-left">
                                        <div class="row">
                                            <div class="col-lg">{{item.pharmacy_catalog_name}}</div>
                                        </div>
                                    </b-td>
                                    <b-td>{{ item.var_1 | currency }}</b-td>
                                    <b-td>{{ item.qty-(item.is_retur_full === 1 && item.pharmacy_catalog_id_retur || item.pharmacy_catalog_id_ref ? 0 : item.qty_retur) }}</b-td>
                                    <b-td>
                                        <b-input-group prepend="%">
                                            <b-form-input style="width:100px" type="number" @change="vChanged($event, index)" v-model="item.var_3" />
                                        </b-input-group>
                                    </b-td>
                                    <b-td>{{ (item.var_1-item.var_4)*(item.qty-(item.is_retur_full === 1 && item.pharmacy_catalog_id_retur || item.pharmacy_catalog_id_ref ? 0 : item.qty_retur)) | currency }}</b-td>
                                    <b-td class="text-center">
                                        <!-- <b-td :rowspan="item.transaction_from === 'itemsPres' ? 2 : 1" class="text-center"> -->
                                        <input type="checkbox" v-model="item.checked" @change="updateTotal" />
                                    </b-td>
                                </b-tr>
                                <!-- <b-tr v-if="item.transaction_from === 'itemsPres'" :class="item.checked ? 'bg-warning-200' : '' ">
                                    <td colspan="5">
                                        <div>
                                            <b-table-simple responsive bordered>
                                                <b-thead class="bg-neutral-100 text-bold-14 text-center">
                                                    <b-tr :class="item.checked ? 'bg-warning-500' : '' ">
                                                        <b-th style="min-width: 120px;">Obat/Alkes</b-th>
                                                        <b-th style="min-width: 120px;">Harga</b-th>
                                                        <b-th style="min-width: 5px;">Qty</b-th>
                                                        <b-th style="min-width: 120px;">Diskon</b-th>
                                                        <b-th style="min-width: 120px;">Total</b-th>
                                                    </b-tr>
                                                </b-thead>
                                                <b-tbody class="text-right text-medium-14">
                                                    <template v-for="(pres_item,index) in item.form_prescription" >
                                                        <b-tr v-if="pres_item.is_retur_full !== 1 || pres_item.pharmacy_catalog_id_retur">
                                                            <b-td class="text-left">
                                                                <div class="row">
                                                                    <div class="col-lg">{{pres_item.pharmacy_catalog_name}}</div>
                                                                </div>
                                                            </b-td>
                                                            <b-td>{{ pres_item.var_1 | currency }}</b-td>
                                                            <b-td>{{ pres_item.qty-(pres_item.is_retur_full === 1 && pres_item.pharmacy_catalog_id_retur || pres_item.pharmacy_catalog_id_ref ? 0 : pres_item.qty_retur) }}</b-td>
                                                            <b-td>
                                                                <b-form-input style="width:100px" type="number" @change="vChanged($event, index)" v-model="pres_item.var_3" />
                                                            </b-td>
                                                            <b-td>{{ (pres_item.var_1-pres_item.var_4)*(pres_item.qty-(pres_item.is_retur_full === 1 && pres_item.pharmacy_catalog_id_retur || pres_item.pharmacy_catalog_id_ref ? 0 : pres_item.qty_retur)) | currency }}</b-td>
                                                        </b-tr>
                                                    </template>
                                                </b-tbody>
                                            </b-table-simple>
                                        </div>
                                    </td>
                                </b-tr> -->
                            </template>
                            <b-tr class="text-right font-weight-bold">
                                <b-td colspan="4">Total Item yang Dipilih</b-td>
                                <b-td colspan="2">{{toInvoice.grand_total | currency}}</b-td>
                            </b-tr>
                            <b-tr class="text-right font-weight-bold">
                                <b-td colspan="4">Total</b-td>
                                <b-td colspan="2">{{toInvoice.grand_total | currency}}</b-td>
                            </b-tr>
                        </b-tbody>
                    </b-table-simple>

                    <div class="d-flex justify-content-lg-end mt-32">
                        <div>
                            <b-button @click="openModalInvoice" pill variant="primary" class="w-100 white-space-nowrap">Buat Invoice</b-button>
                            <b-modal v-model="open_modal.invoice" @ok="handleOk" title="Perhatian">
                                Apakah Anda yakin akan membuat invoice dengan item yang dipilih?
                            </b-modal>
                        </div>
                    </div>
                    <b-table small bordered responsive head-variant="light" :fields="invoiceFields" :items="invoiceItems"
                        class="mt-20 text-center" style="vertical-align: middle;">
                        <template #cell(code)="data">
                            <div class="px-3">
                                <span class="text-regular-14">{{ data.value }}</span>
                            </div>
                        </template>
                        <template #cell(var_3)="data">
                            <div class="px-3" style="min-width: 200px;">
                                <p class="text-regular-14 text-right">{{ data.value | currency }}</p>
                            </div>
                        </template>
                        <template #cell(var_2)="data">
                            <div class="px-3" style="min-width: 200px;">
                                <p class="text-regular-14 text-right">{{ data.value | currency }}</p>
                            </div>
                        </template>
                        <template #cell(remain)="data">
                            <div class="px-3" style="min-width: 200px;">
                                <p class="text-regular-14 text-right">{{ data.value }}</p>
                            </div>
                        </template>
                        <template #cell(time_pay)="data">
                            <div class="px-3" style="min-width: 200px;">
                                <span class="text-regular-14">{{ data.value }}</span>
                            </div>
                        </template>
                        <template #cell(action_pay)="row">
                            <div class="px-3 text-left" style="min-width: 280px;">
                                <!-- <router-link :to="{ name: 'PatientPaymentRepaymentDetailView' }">
                                    <b-button size="sm" variant="indigo" class="m-1"><b-icon icon="eye" /> Lihat</b-button>
                                </router-link> -->
                                <router-link :to="{ path: '/payment/repaymentfree/'+ row.item.id}" v-if="row.item.payment_id === null">
                                    <b-button size="sm" variant="orange-500" class="m-1"><b-icon icon="pencil" /> Pelunasan</b-button>
                                </router-link>
                                <b-button size="sm" @click="deleteInvoice(row.item.id)" variant="danger-500" class="m-1" v-if="row.item.payment_id === null"> <b-icon icon="x" /> Batal Invoice</b-button>
                                <!-- <b-button size="sm" variant="success-600" class="m-1"><b-icon icon="check" />
                                    Verifikasi</b-button> -->
                                <!-- <b-button size="sm" variant="info-800" class="m-1"><b-icon icon="printer" /> Cetak Invoice
                                    Kop</b-button> -->
                                <router-link :to="{ path: '/payment/invoice_free/'+ row.item.id}" target="_blank" >
                                    <b-button size="sm" variant="info-800" class="m-1"><b-icon icon="printer" /> Cetak Invoice</b-button>
                                </router-link>
                                <router-link :to="{ path: '/payment/kwitansi/'+ row.item.id}" target="_blank" v-if="row.item.payment_id !== null">
                                    <b-button size="sm" variant="success-600" class="m-1"><b-icon icon="printer" /> Kwitansi</b-button>
                                </router-link>
                                <b-button size="sm" @click="deletePayment(row.item.id)" variant="danger-500" class="m-1" v-if="row.item.payment_id !== null"> <b-icon icon="x" /> Batal Pembayaran</b-button>
                                <!-- <b-button size="sm" variant="success-600" class="m-1"><b-icon icon="journal-text" /> Export
                                    Excel</b-button> -->
                            </div>
                        </template>
                    </b-table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment'
import TopPharmacyMenu from '@/@core/components/top-menu/TopPharmacyMenu.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import vSelect from 'vue-select'
// import PharmacyReportMenu from '@/@core/components/tab-menu/PharmacyReportMenu.vue';
import { mapState } from 'vuex'
import router from '@/router/index.js'

export default {
    name: 'OutpatientRecipeDetail',
    components: {
        TopPharmacyMenu, vSelect, ModernLabel //PharmacyReportMenu, 
    },
    data() {
        return {
            handle:false,
            invoiceItems:[],
            toInvoice: {
                registration_id: '',  
                grand_total: 0,
            },
            form_transaction: {
                id:'',
                view:'add',
                soap_id:'',
                pharmacy_depo_id:'',
                norm:'',
                flag:0,
                health_facility_item_id:'',
                form_prescription_parent:[],
                form_prescription:[]
            },
            form_transaction_subs: {
                id:'',
                pharmacy_catalog_id_ref:'',
                pharmacy_catalog_id_sub:'',
                doctor_in_charge_id:'',
                user   :'d77142c4-4777-47d8-bd0c-fd7dfd134af4',
                notes   :'',
                used   :''
            },
            open_modal: {
                invoice: false,
                details: false,
            },
            itemsPres:[],
        }
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'IDR' }).format(value)
        }
    },
    computed: {
        ...mapState({ 
            invoiceFields: state => state.payment.invoice.fieldsInvoice
        }),
        combinedItems() {
            return [
                ...(this.itemsPres.length ? this.itemsPres[0].form_prescription.map(item => ({ ...item, checked: false, transaction_from: 'itemsPres' })) : []),
            ];
        },

    },
    methods: {
        vChanged($event, index){
            let disc = $event
            let var_1 = this.itemsPres[0].form_prescription[index].var_1
            this.itemsPres[0].form_prescription[index].var_4 = (var_1*disc)/100
            // this.itemsPres.form_prescription[index].var_2_show = this.currency(qty * var_1)
        },
        deleteInvoice (obj) { // tab 2
            this.$bvModal
                .msgBoxConfirm(<b-form-input vModel={this.purpose} />, {
                title: 'Batal Invoice', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    // console.log('selected code: ', obj.code + ' | id: ' + obj.id)
                    this.$store.dispatch('payment/deleteInvoice', { id: obj, purpose: 'cancel', reason: this.purpose })
                    .then(response => {
                        this.$store.dispatch('payment/getByAllInvoiceFree', this.$route.params.id).then(res => {
                            this.invoiceItems = res
                        })
                        this.getData(this.$route.params.id)
                        this.notification('Success','CheckIcon','success','You have successfully')
                    },
                    error => {
                        this.$store.dispatch('payment/getByAllInvoiceFree', this.$route.params.id).then(res => {
                            this.invoiceItems = res
                        })
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.log(error);
                    }) 
                }
            })
        },
        deletePayment (obj) { // tab 2
            this.$bvModal
                .msgBoxConfirm(<b-form-input vModel={this.purpose} />, {
                title: 'Batal Pembayaran', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    // console.log('selected code: ', obj.code + ' | id: ' + obj.id)
                    this.$store.dispatch('payment/deletePayment', { id: obj, purpose: 'cancel', reason: this.purpose })
                    .then(response => {
                        this.$store.dispatch('payment/getByAllInvoiceFree', this.$route.params.id).then(res => {
                            this.invoiceItems = res
                        })
                        this.getData(this.$route.params.id)
                        this.notification('Success','CheckIcon','success','You have successfully')
                    },
                    error => {
                        this.$store.dispatch('payment/getByAllInvoiceFree', this.$route.params.id).then(res => {
                            this.invoiceItems = res
                        })
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.log(error);
                    }) 
                }
            })
        },
        updateTotal() {
            this.toInvoice.grand_total = this.combinedItems.reduce((sum, item) => {
                if (item.transaction_from == 'itemsDoctor' || item.transaction_from == 'itemsNurse') {
                    return item.checked ? sum + item.var_3 : sum;
                } else if (item.transaction_from == 'itemsBed' ) {
                    return item.checked ? sum + item.var_1*item.var_2 : sum;
                } else if (item.transaction_from == 'itemsPres' ) {
                    console.log('test from:', (item.var_1-item.var_4)*(item.qty-(item.is_retur_full === 1 && item.pharmacy_catalog_id_retur || item.pharmacy_catalog_id_ref ? 0 : item.qty_retur)) )
                    return item.checked ? sum + (item.var_1-item.var_4)*(item.qty-(item.is_retur_full === 1 && item.pharmacy_catalog_id_retur || item.pharmacy_catalog_id_ref ? 0 : item.qty_retur)) : sum;
                }
                return item.checked ? sum + item.var_1 : sum;
            }, 0);
        },
        toggleSelectAll(event) {
            const isChecked = event.target.checked;
            this.combinedItems.forEach(item => {
                item.checked = isChecked;
            });
            this.updateTotal();
        },
        openModalInvoice() {
            if (this.toInvoice.grand_total === 0) {
                this.notification('Gagal membuat invoice','CloseIcon','warning','Silakan pilih transaksi yang akan dibuat')
            } else {
                this.open_modal.invoice = true
            }
        },
        handleOk(bvModalEvent) {
            bvModalEvent.preventDefault()
            console.log('test')
            console.log(this.handle)
            if (this.handle === false){
                this.handleSubmit()
            }
        },
        handleSubmit () {
            this.handle = true

            let formData = {}
            formData = {
                toInvoice: this.toInvoice,
                prescription: this.combinedItems,
            }

            this.$store.dispatch('payment/formPostPutInvoiceFree', formData)
            .then(res => {
                this.open_modal.invoice = false
                this.handle = false
                this.getData(this.$route.params.id)
                this.$store.dispatch('payment/getByAllInvoiceFree', this.$route.params.id).then(res => {
                    this.invoiceItems = res
                })
                this.active.tab = 'tab2'
                this.toInvoice.grand_total = 0

            }, 
            err => {
                this.handle = false
                console.error('ERR GOING HERE')
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                this.open_modal.invoice = false
            })
        },
        localDateTime(input) {
            return moment(input).locale("id").format('DD MMMM YYYY HH:MM')
        },
        getData (id) {
			this.$store.dispatch('prescription/getById', id).then(data => {
                this.prescData = data[0]
                this.openPrescription(this.prescData)
                this.itemsPres = data
                this.$store.dispatch('payment/getByAllInvoiceFree', this.$route.params.id).then(res => {
                    this.invoiceItems = res
                })
                // this.form_nurse.employee_nurse_id = this.userData.employee_id
                // this.form_nurse.registration_destination_id = id
                // this.show = false
            })
        },
        currency(value) {
            return new Intl.NumberFormat("en-US", { style: "currency", currency: "IDR" }).format(value)
        },
        openPrescription (data) {
            this.form_transaction.form_prescription_parent = []
            this.form_transaction.form_prescription = []
            this.form_transaction.id = data.id
            this.form_transaction.norm = data.norm
            this.form_transaction.fullname = data.fullname
            this.form_transaction.date_added = moment(data.date_added).format('YYYY-MM-DD')
            this.form_transaction.no_order = data.no_order
            this.form_transaction.flag = data.flag
            this.form_transaction.var_1_show = this.currency(data.var_1)
            this.form_transaction.var_2_show = this.currency(data.var_2)
            this.form_transaction.var_3_show = this.currency(data.var_3)
            this.form_transaction.doctor_in_charge_id = data.doctor_in_charge_id
            this.form_transaction.doctor_in_charge_name = data.doctor_in_charge_name
            this.form_transaction.health_facility_item_name = data.health_facility_item_name
            this.form_transaction.depo_name = data.depo_name
            this.form_transaction.patient_name = data.patient_name
            this.form_transaction.patient_birth_date = data.patient_birth_date
            this.form_transaction.patient_birth_place = data.patient_birth_place
            this.form_transaction.company_name = data.company_name
            this.form_transaction.number_insurance = data.number_insurance
            this.form_transaction.insurance_name = data.insurance_name
            let prescription_type = {value:1,title:'OBAT SATUAN'}
            this.prescription = prescription_type
            this.prescription_type = prescription_type
            this.$store.dispatch('prescription/getPrescriptionItem', data.id).then(res => {
                for (let index = 0; index < res.length; index++) {
                    this.form_transaction.form_prescription.push(
                        { 
                            code:res[index].pharmacy_catalog_code,
                            name:res[index].pharmacy_catalog_name,
                            pharmacy_catalog_id:res[index].pharmacy_catalog_id, 
                            pharmacy_catalog_id_sub:res[index].pharmacy_catalog_id_sub, 
                            package_id:res[index].package_id,
                            dose:res[index].dose,
                            sum_dose:res[index].dose+' '+res[index].dose_name, 
                            dose_name:res[index].dose_name,
                            package_name:res[index].package_name,
                            prescription_type_id:res[index].prescription_item_type_id,
                            prescription_type_name:this.prescription_type.title,
                            hja:res[index].hja,
                            hp:res[index].hp,
                            hna:res[index].hna,
                            stock:res[index].stock,
                            var_1:res[index].hja,
                            var_2:res[index].hja*res[index].qty,
                            var_1_show:this.currency(res[index].hja),
                            var_2_show:this.currency(res[index].hja*res[index].qty),
                            value_doctor:res[index].qty_doctor,
                            qty:res[index].qty,
                            used:res[index].used,
                            desc:res[index].desc,
                            sort:res[index].sort, 
                        }
                    )
                }
                this.form_transaction.form_prescription_parent.push({
                    id:1,
                    prescription:'Obat Satuan',
                    used:'',
                    value:0
                },{
                    id:2,
                    prescription:'Racik 1',
                    used:'',
                    value:0
                },{
                    id:3,
                    prescription:'Racik 2',
                    used:'',
                    value:0
                },{
                    id:4,
                    prescription:'Racik 3',
                    used:'',
                    value:0
                },{
                    id:5,
                    prescription:'Racik 4',
                    used:'',
                    value:0
                }) 
            })
            this.form_transaction.health_facility_item_id = data.health_facility_item_id
            this.form_transaction.pharmacy_depo_id = data.pharmacy_depo_id
            this.form_transaction.soap_id = data.soap_id
            this.form_transaction.view = 'edit'

            // console.log('this.form_transaction', this.form_transaction);
        },
        submit () {
            // this.$refs.simpleRules.validate()
            // .then(success => {
                //     if (success) {
                this.isDisabled = true
                const formData = this.form_transaction
                this.$store.dispatch('prescription/formPostPutTransaction', formData)
                .then(res => {
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.getData(this.$route.params.id)
                    this.$store.dispatch('prescription/getOrder',this.params)
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
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        }
    },
    mounted () {
        if (this.$route.params.id) {
            this.form_transaction.id = this.$route.params.id
            this.getData(this.$route.params.id)
        }
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>