<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Data Rincian Tagihan Pasien' },
                { text: 'Pelunasan' }
            ]"
        >
            <section class="">

                <!-- ==== TOGGLE DEPOSIT ==== -->
                <b-form-checkbox
                    v-model="form.transaction.use_deposit"
                    @change="onUseDepositChange"
                    class="mb-2"
                    :disabled="!hasDeposit"
                >
                    Bayar Sebagian/Seluruhnya dari Deposit
                    <span v-if="!hasDeposit" class="text-muted">(Tidak ada deposit tersedia)</span>
                    <span v-else class="text-muted">(Saldo: Rp {{ formatRupiah(depositBalance) }})</span>
                </b-form-checkbox>
                <small class="text-danger d-block mb-2" v-if="depositError">{{ depositError }}</small>

                <!-- ==== TABEL PEMBAYARAN (deposit + cara bayar lain jadi satu) ==== -->
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
                            <b-td>
                                <b-form-input
                                    type="number"
                                    v-model="row.var_3"
                                    :max="row.isDeposit ? maxDepositUsable : null"
                                    @change="row.isDeposit ? onDepositAmountChange($event) : recalcTotal()"
                                />
                            </b-td>
                            <b-td>
                                <span v-if="row.isDeposit" class="font-weight-bold">DEPOSIT</span>
                                <v-select
                                    v-else
                                    v-model="row.payment_method_id"
                                    aria-readonly="true"
                                    :options="PaymentMethod"
                                    :reduce="PaymentMethod => PaymentMethod.value"
                                    label="title"
                                    placeholder="-- Cara Bayar --"
                                ></v-select>
                            </b-td>
                            <b-td>
                                <span v-if="row.isDeposit">-</span>
                                <v-select
                                    v-else
                                    v-model="row.payment_method_type_id"
                                    aria-readonly="true"
                                    :options="PaymentMethodType"
                                    :reduce="PaymentMethodType => PaymentMethodType.value"
                                    label="title"
                                    placeholder="-- Cara Bayar --"
                                ></v-select>
                            </b-td>
                            <b-td><b-form-input type="text" v-model="row.oauth" :disabled="row.isDeposit" /></b-td>
                            <b-td><b-form-input type="number" v-model="row.batchNumber" :disabled="row.isDeposit" /></b-td>
                            <b-td><b-form-input type="number" v-model="row.biayaKartuKredit" :disabled="row.isDeposit" /></b-td>
                            <b-td>
                                <b-button
                                    @click="row.isDeposit ? removeDepositRow() : deleteRow(index)"
                                    size="sm"
                                    variant="danger-700"
                                >
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </b-td>
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
                    </b-tbody>
                </b-table-simple>
                <b-button variant="info-700" :disabled="isDisabled" @click.prevent="validationForm" class="px-20">Simpan</b-button>
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
            isDisabled:false,
            depositData: null,
            depositBalance: 0,
            depositError: '',
            originalAmount: 0, // nilai tagihan asli (sebelum diskon), dipakai supaya diskon tidak akumulasi berulang
            form: {
                payment:{
                    amount_remain:0
                },
                transaction:{
                    id:'',
                    invoice_id:'',
                    cashier_id:'',
                    flag_status_id:'1',
                    use_deposit: false,
                    amount_discount:0,
                    amount_payment:0,
                    amount_remain:0,
                    payment_items: [
                        { payment_method_id: '',payment_method_type_id: 0, payment_method_sub_id: '', subCaraBayar: '', var_3: '0', insurance_id: '1', oauth: '', batchNumber: '', biayaKartuKredit: '', isPrimary: true },
                    ],
                }
            },
            newRow: 
                { payment_method_id: '',payment_method_type_id: 0, payment_method_sub_id: '', subCaraBayar: '', var_3: '0', insurance_id: '1', oauth: '', batchNumber: '', biayaKartuKredit: '' },
            PaymentMethodType:[
                {value:0,title:'-'},
                {value:1,title:'EDC'},
                {value:3,title:'QRIS'},
                {value:4,title:'TRANSFER BANK'}
            ]
        };
    },
    computed: {
        ...mapState({ 
            PaymentMethod: state => state.paymentmethod.options.paymentmethod,
        }),
        hasDeposit () {
            return !!this.depositData && this.depositBalance > 0
        },
        maxDepositUsable () {
            return Math.min(this.depositBalance, this.form.transaction.amount_remain)
        }
    },
    methods: {
        formatRupiah (val) {
            return new Intl.NumberFormat('id-ID').format(val || 0)
        },
        getDeposit (id) {
            this.$store.dispatch('payment/getDepositByRegId', id).then(data => {
                this.depositData = data[0]
                this.depositBalance = data[0] && data[0].var_1 ? parseFloat(data[0].var_1) : 0
            }).catch(() => {
                this.depositData = null
                this.depositBalance = 0
            })
        },
        onUseDepositChange (checked) {
            this.depositError = ''
            if (checked) {
                if (!this.hasDeposit) {
                    this.notification('Failed','AlertTriangleIcon','danger','Deposit tidak tersedia untuk pasien ini')
                    this.form.transaction.use_deposit = false
                    return
                }
                this.addDepositRow()
            } else {
                this.removeDepositRow()
            }
        },
        addDepositRow () {
            // hapus dulu kalau sudah ada baris deposit sebelumnya
            this.form.transaction.payment_items = this.form.transaction.payment_items.filter(r => !r.isDeposit)
            const depositRow = {
                downpayment_id: this.depositData.id, // downpayment_id sebagai payment_method_id
                payment_method_id: 'd84d2b1d-9697-467c-b5f5-0dc2e96b8366', // downpayment_id sebagai payment_method_id
                payment_method_type_id: 0,
                payment_method_sub_id: '',
                subCaraBayar: '',
                var_3: this.maxDepositUsable,
                insurance_id: '1',
                oauth: '',
                batchNumber: '',
                biayaKartuKredit: '',
                isDeposit: true
            }
            this.form.transaction.payment_items.unshift(depositRow)
            this.updateRegularAmount()
        },
        removeDepositRow () {
            this.form.transaction.payment_items = this.form.transaction.payment_items.filter(r => !r.isDeposit)
            this.form.transaction.use_deposit = false
            this.updateRegularAmount()
        },
        onDepositAmountChange (val) {
            const dep = parseFloat(val) || 0
            const row = this.form.transaction.payment_items.find(r => r.isDeposit)
            if (!row) return
            if (dep > this.maxDepositUsable) {
                this.depositError = `Nominal melebihi batas maksimal (Rp ${this.formatRupiah(this.maxDepositUsable)})`
                row.var_3 = this.maxDepositUsable
            } else {
                this.depositError = ''
                row.var_3 = dep
            }
            this.updateRegularAmount()
        },
        // Hitung otomatis sisa yang harus dibayar di baris cara bayar utama:
        // sisa = NILAI TERHUTANG - DEPOSIT (tidak pernah negatif)
        updateRegularAmount () {
            const depositRow = this.form.transaction.payment_items.find(r => r.isDeposit)
            const depositAmount = depositRow ? (parseFloat(depositRow.var_3) || 0) : 0

            const sisa = Math.max(0, (parseFloat(this.form.transaction.amount_remain) || 0) - depositAmount)

            const primaryRow = this.form.transaction.payment_items.find(r => r.isPrimary)
            if (primaryRow) primaryRow.var_3 = sisa

            this.recalcTotal()
        },
        recalcTotal () {
            const itemsTotal = this.form.transaction.payment_items.reduce((sum, r) => sum + (parseFloat(r.var_3) || 0), 0)
            this.form.transaction.amount_payment = itemsTotal
        },
        vdChanged($event){
            const disc = parseFloat($event) || 0
            this.form.transaction.amount_discount = disc
            // pakai originalAmount supaya tidak akumulasi/compounding saat diskon diubah berkali-kali
            this.form.transaction.amount_remain = this.originalAmount - disc
            this.updateRegularAmount()
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
            this.form.transaction.payment_items.push({ ...this.newRow });
        },
        deleteRow(index) {
            this.form.transaction.payment_items.splice(index, 1);
            this.recalcTotal()
        },
        getData (id) {
            this.$store.dispatch('payment/getByInvoiceId', id).then(data => {
                this.regData = data[0]
                this.form.transaction.registration_id = data[0].registration_id
                this.form.transaction.invoice_id = data[0].id
                this.form.transaction.amount_discount = 0
                this.originalAmount = parseFloat(data[0].var_3) || 0
                this.form.transaction.amount_remain = data[0].var_3
                this.form.transaction.amount_payment = data[0].var_3
                this.form.transaction.payment_items[0].var_3 = data[0].var_3
                this.form.transaction.cashier_id = data[0].cashier_id

                this.getDeposit(data[0].registration_id)
            })
        }
    },
    mounted () {
        this.$store.dispatch('paymentmethod/getParam', 1)
        this.$store.dispatch('payment/getByInvoiceId', this.$route.params.id)
        if (this.$route.params.id) {
            this.id = this.$route.params.id
            this.getData(this.$route.params.id)
        }
    }
};
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>