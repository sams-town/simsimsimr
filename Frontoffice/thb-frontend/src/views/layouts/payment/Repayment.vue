<template>
    <div>
        <b-table-simple bordered>
            <b-thead>
                <b-tr>
                    <b-th style="vertical-align: middle;">No</b-th>
                    <b-th style="vertical-align: middle;">NOMINAL BAYAR</b-th>
                    <b-th style="vertical-align: middle;">CARA BAYAR</b-th>
                    <b-th style="vertical-align: middle;">METODE</b-th>
                    <b-th style="vertical-align: middle;">AUTHORIZATION NUMBER</b-th>
                    <b-th style="vertical-align: middle;">BATCH NUMBER</b-th>
                    <b-th style="vertical-align: middle;">BIAYA KARTU KREDIT</b-th>
                    <b-th style="vertical-align: middle;">
                        <b-button size="sm" variant="info-600" @click="addRow">
                            <feather-icon icon="PlusIcon" />
                        </b-button>
                    </b-th>
                </b-tr>
            </b-thead>
            <b-tbody>
                <b-tr v-for="(row, index) in form.transaction.payment_items" :key="index">
                    <b-td>{{ index + 1 }}</b-td>
                    <b-td><b-form-input type="number" v-model="row.var_3" /></b-td>
                    <b-td>
                        <v-select v-model="row.payment_method_id" aria-readonly="true" :options="PaymentMethod" :reduce="PaymentMethod => PaymentMethod.value" label="title" placeholder="-- Cara Bayar --"></v-select>
                    </b-td>
                    <b-td>
                        <v-select v-model="row.payment_method_type_id" aria-readonly="true" :options="PaymentMethodType" :reduce="PaymentMethodType => PaymentMethodType.value" label="title" placeholder="-- Cara Bayar --"></v-select>
                    </b-td>
                    <!-- <b-td>
                        <b-form-select v-model="row.payment_method_sub_id">
                            <b-form-select-option v-if="row.payment_method_id === '3'" value="2B">Bank BCA</b-form-select-option>
                            <b-form-select-option v-if="row.payment_method_id === '4'" value="3B">BPJS</b-form-select-option>
                            <b-form-select-option v-if="row.payment_method_id === '3c476753-e0b1-497f-8a5c-c3a28a5e3901'" selected value="3c476753-e0b1-497f-8a5c-c3a28a5e3901">Tunai</b-form-select-option>
                        </b-form-select>
                    </b-td> -->
                    <b-td><b-form-input type="text" v-model="row.oauth" /></b-td>
                    <b-td><b-form-input type="number" v-model="row.batchNumber" /></b-td>
                    <b-td><b-form-input type="number" v-model="row.biayaKartuKredit" /></b-td>
                    <b-td><b-button @click="deleteRow(index)" size="sm" variant="danger-700"><feather-icon icon="TrashIcon" /></b-button></b-td>
                </b-tr>
            </b-tbody>
        </b-table-simple>
        <b-table-simple bordered class="mt-20">
            <b-tbody class="text-bold-14">
                <b-tr>
                    <b-td style="vertical-align: middle;">DISKON</b-td>
                    <b-td style="width: 300px;">
                        <b-input-group prepend="Rp">
                            <b-form-input type="number" v-model="form.transaction.amount_discount" @change="vdChanged($event)"></b-form-input>
                        </b-input-group>
                    </b-td>
                </b-tr>
                <b-tr>
                    <b-td style="vertical-align: middle;">NILAI TERHUTANG</b-td>
                    <b-td style="width: 300px;">
                        <b-input-group prepend="Rp">
                            <b-form-input type="number" v-model="this.form.transaction.amount_remain" readonly></b-form-input>
                        </b-input-group>
                    </b-td>
                </b-tr>
                <b-tr>
                    <b-td style="vertical-align: middle;">NOMINAL BAYAR (Rp)</b-td>
                    <b-td style="width: 300px;">
                        <b-input-group prepend="Rp">
                            <b-form-input type="number" v-model="form.transaction.amount_payment" readonly></b-form-input>
                        </b-input-group>
                    </b-td>
                </b-tr>
                <!-- <b-tr>
                    <b-td style="vertical-align: middle;">PENGGUNAAN UANG MUKA</b-td>
                    <b-td style="width: 300px;">
                        <b-input-group prepend="Rp">
                            <b-form-input type="number" disabled></b-form-input>
                        </b-input-group>
                    </b-td>
                </b-tr>
                <b-tr>
                    <b-td style="vertical-align: middle;">NILAI TOTAL YANG DIBAYAR (+ UANG MUKA)</b-td>
                    <b-td style="width: 300px;">
                        <b-input-group size="lg">
                            <b-input-group-prepend>
                                <div class="input-group-text bg-success-200">Rp</div>
                            </b-input-group-prepend>
                            <b-form-input type="number" disabled></b-form-input>
                        </b-input-group>
                    </b-td>
                </b-tr>
                <b-tr>
                    <b-td style="vertical-align: middle;">SISA</b-td>
                    <b-td style="width: 300px;">
                        <b-input-group prepend="Rp">
                            <b-form-input type="number" disabled></b-form-input>
                        </b-input-group>
                    </b-td>
                </b-tr>
                <b-tr>
                    <b-td style="vertical-align: middle;"></b-td>
                    <b-td style="width: 300px;">
                        <b-input-group>
                            <b-input-group-prepend is-text>
                                <input type="checkbox" >
                            </b-input-group-prepend>
                            <b-form-input value="Pulang Jika Lunas" disabled></b-form-input>
                        </b-input-group>
                    </b-td>
                </b-tr> -->
            </b-tbody>
        </b-table-simple>
        <b-button variant="info-700" :disabled="isDisabled" @click.prevent="validationForm" class="px-20">Simpan</b-button>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import vSelect from 'vue-select'
export default {
    components: {
        vSelect
    },
    data() {
        return {
            isDisabled:false,
            form: {
                payment:{
                    amount_remain:0
                },
                transaction:{
                    id:'',
                    invoice_id:'',
                    cashier_id:'',
                    flag_status_id:'1',
                    amount_discount:0,
                    amount_payment:0,
                    amount_remain:0,
                    payment_items: [
                        { payment_method_id: '',payment_method_type_id: 0, payment_method_sub_id: '', subCaraBayar: '', var_3: '0', insurance_id: '1', oauth: '', batchNumber: '', biayaKartuKredit: '' },
                    ],
                }
            },
            newRow: 
                { payment_method_id: '',payment_method_type_id: 0, payment_method_sub_id: '', subCaraBayar: '', var_3: '0', insurance_id: '1', oauth: '', batchNumber: '', biayaKartuKredit: '' },
                // { payment_method_id: '', payment_method_sub_id: '', subCaraBayar: '', var_3: '0', insurance_id: '1', oauth: '', batchNumber: '', biayaKartuKredit: '' },
            PaymentMethodType:[
                {value:0,title:'-'},
                {value:1,title:'EDC'},
                {value:3,title:'QRIS'},
                {value:4,title:'TRANSFER BANK'}
            ]
            // {
            //     caraBayar: '',
            //     subCaraBayar: '',
            //     nominal: '',
            //     authorizationNumber: '',
            //     batchNumber: '',
            //     biayaKartuKredit: ''
            // }
        };
    },
    computed: {
        ...mapState({ 
            PaymentMethod: state => state.paymentmethod.options.paymentmethod,
        }),
    },
    methods: {
        vdChanged($event){
            let value_disc = $event
            let value_amount_remain = this.form.transaction.amount_remain
            this.form.transaction.payment_items[0].var_3 = value_amount_remain-value_disc
            this.form.transaction.amount_discount = parseFloat(value_disc)
            this.form.transaction.amount_payment = value_amount_remain-value_disc
            this.form.transaction.amount_remain = value_amount_remain-value_disc
        },
        validationForm () {
            this.isDisabled = true
            this.show = true
            const formData = this.form.transaction
            this.$store.dispatch('payment/formPostPutPayment', formData)
            .then(res => {
                this.isDisabled = false
                this.notification('Success','CheckIcon','success','You have successfully')
            }, 
            err => {
                this.isDisabled = false
                this.show = false
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
            })
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        addRow() {
            this.form.transaction.payment_items.push(this.newRow);
        },
        deleteRow(index) {
            this.payment_items.splice(index, 1);
        },
        getData (id) {
            console.log(id)
            this.$store.dispatch('payment/getByInvoiceId', id).then(data => {
                console.log(data[0])
                this.regData = data[0]
                this.form.transaction.registration_id = data[0].registration_id
                this.form.transaction.invoice_id = data[0].id
                this.form.transaction.amount_discount = 0
                this.form.transaction.amount_remain = data[0].var_3
                this.form.transaction.amount_payment = data[0].var_3
                this.form.transaction.payment_items[0].var_3 = data[0].var_3
                this.form.transaction.cashier_id = data[0].cashier_id
                console.log(this.form.transaction)
                // this.show = false
            })
        }
    },
    mounted () {
        this.$store.dispatch('paymentmethod/getParam', 1)
        this.$store.dispatch('payment/getByInvoiceId', this.$route.params.id)
        if (this.$route.params.id) {
            // this.show = true
            this.id = this.$route.params.id
            this.getData(this.$route.params.id)
        }
    }
};
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>