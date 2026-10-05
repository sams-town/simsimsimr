<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Data Rincian Tagihan Pasien' },
                { text: 'Deposit' }
            ]"
        >
            <section class="">
                <div>
                    <b-table-simple bordered>
                        <b-thead>
                            <b-tr>
                                <b-th style="vertical-align: middle;">No</b-th>
                                <b-th style="vertical-align: middle; width: 210px;">CARA BAYAR</b-th>
                                <b-th style="vertical-align: middle; width: 210px;">NOMINAL BAYAR</b-th>
                                <b-th style="vertical-align: middle; width: 210px;">AUTHORIZATION NUMBER</b-th>
                                <b-th style="vertical-align: middle; width: 210px;">BATCH NUMBER</b-th>
                                <b-th style="vertical-align: middle; width: 210px;">BIAYA KARTU KREDIT</b-th>
                                <b-th style="vertical-align: middle;">
                                    <b-button size="sm" variant="info-600" @click="addRow">
                                        <feather-icon icon="PlusIcon" />
                                    </b-button>
                                </b-th>
                            </b-tr>
                        </b-thead>
                        <b-tbody>
                            <b-tr v-for="(row, index) in form.transaction.downpayment_items" :key="index">
                                <b-td>{{ index + 1 }}</b-td>
                                <b-td>
                                    <v-select v-model="row.payment_method_id" aria-readonly="true" :options="PaymentMethod" :reduce="PaymentMethod => PaymentMethod.value" label="title" placeholder="-- Cara Bayar --"></v-select>
                                </b-td>
                                <!-- <b-td>
                                    <b-form-select v-model="row.payment_method_sub_id">
                                        <b-form-select-option v-if="row.payment_method_id === '3'" value="2B">Bank BCA</b-form-select-option>
                                        <b-form-select-option v-if="row.payment_method_id === '4'" value="3B">BPJS</b-form-select-option>
                                        <b-form-select-option v-if="row.payment_method_id === '3c476753-e0b1-497f-8a5c-c3a28a5e3901'" selected value="3c476753-e0b1-497f-8a5c-c3a28a5e3901">Tunai</b-form-select-option>
                                    </b-form-select>
                                </b-td> -->
                                <b-td><b-form-input type="number" v-model="row.var_1" @change="v_1Changed($event)"/></b-td>
                                <b-td><b-form-input type="text" v-model="row.insurance_id" /></b-td>
                                <b-td><b-form-input type="number" v-model="row.batchNumber" /></b-td>
                                <b-td><b-form-input type="number" v-model="row.biayaKartuKredit" /></b-td>
                                <b-td><b-button @click="deleteRow(index)" size="sm" variant="danger-700"><feather-icon icon="TrashIcon" /></b-button></b-td>
                            </b-tr>
                        </b-tbody>
                    </b-table-simple>
                    <b-table-simple bordered class="mt-20">
                        <b-tbody class="text-bold-14">
                            <b-tr>
                                <b-td style="vertical-align: middle;">NOMINAL BAYAR (Rp)</b-td>
                                <b-td style="width: 300px;">
                                    {{ form.transaction.var_1 | currency }}
                                </b-td>
                            </b-tr>
                        </b-tbody>
                    </b-table-simple>
                    <b-button variant="info-700" @click.prevent="validationForm" class="px-20">Simpan</b-button>
                </div>
            </section>
        </Layout>
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
            form: {
                payment:{
                    amount_remain:0
                },
                transaction:{
                    id:'',
                    cashier_id:'',
                    flag_status_id:'1',
                    var_1:0,
                    downpayment_items: [
                        { payment_method_id: '', payment_method_sub_id: '', subCaraBayar: '', var_1: '0', insurance_id: '', batchNumber: '', biayaKartuKredit: '' },
                    ],
                }
            },
            newRow: { payment_method_id: '', payment_method_sub_id: '', subCaraBayar: '', var_1: '0', insurance_id: '', batchNumber: '', biayaKartuKredit: '' }
        };
    },
    computed: {
        ...mapState({ 
            PaymentMethod: state => state.paymentmethod.options.paymentmethod,
        }),
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'IDR' }).format(value)
        }
    },
    methods: {
        addRow() {
            this.form.transaction.downpayment_items.push(this.newRow);
        },
        deleteRow(index) {
            this.form.transaction.downpayment_items.splice(index, 1);
        },
        vdChanged($event){
            let value_disc = $event
            let value_amount_remain = this.form.transaction.amount_remain
            this.form.transaction.downpayment_items[0].var_1 = value_amount_remain-value_disc
            this.form.transaction.amount_discount = parseFloat(value_disc)
            this.form.transaction.var_1 = value_amount_remain-value_disc
            this.form.transaction.amount_remain = value_amount_remain-value_disc
        },
        v_1Changed($event){
            let value = $event
            console.log(value)
            this.form.transaction.var_1 = 0
            this.form.transaction.var_1 += parseFloat(value)
        },
        validationForm () {
            this.show = true
            const formData = this.form.transaction
            console.log(this.form.transaction)
            this.$store.dispatch('payment/formPostPutDP', formData)
            .then(res => {
                this.notification('Success','CheckIcon','success','You have successfully')
            }, 
            err => {
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
        getData (id) {
            this.$store.dispatch('registration/getByRegistrationId', id).then(data => {
                this.regData = data[0]
                this.form.transaction.registration_id = data[0].registration_id
                this.form.transaction.cashier_id = '3ada9806-8d98-4887-8bf8-c510483f454c'
                this.form.transaction.downpayment_items[0].var_1 = data[0].var_1
                console.log(this.form.transaction)
                // this.show = false
            })
        }
    },
    mounted () {
        this.$store.dispatch('paymentmethod/getParam', 1)
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