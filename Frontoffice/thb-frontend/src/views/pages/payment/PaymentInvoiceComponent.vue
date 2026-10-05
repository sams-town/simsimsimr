<template>
    <div>
        <b-modal v-model="active.modal.main" scrollable size="xl" hide-footer no-close-on-esc no-close-on-backdrop>
            <div class="main-content">
                <div class="canvas-body mt-32 px-40">
                    <section class="chead">
                        <div class="row mx-0 mt-28">
                            <div class="col-6">
                                <table>
                                    <tr>
                                        <td class="first-cell">No. Nota</td>
                                        <td class="px-3">:</td>
                                        <td>{{ invoices.code }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">No. RM</td>
                                        <td class="px-3">:</td>
                                        <td>{{ regData.norm }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Nama Pasien</td>
                                        <td class="px-3">:</td>
                                        <td>{{ regData.alias_name }} {{ regData.name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Dokter</td>
                                        <td class="px-3">:</td>
                                        <td>{{ regData.doctor_in_charge_name }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-6">
                                <table>
                                    <tr>
                                        <td class="first-cell">Ruangan/Poli</td>
                                        <td class="px-3">:</td>
                                        <td>{{ regData.health_facility_item_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Kelas</td>
                                        <td class="px-3">:</td>
                                        <td>
                                            <div v-if="this.regData.registration_insurance">
                                                <div v-for="list in this.regData.registration_insurance">
                                                    <template>
                                                        {{ list.service_class_name }}
                                                    </template>
                                                    <b-badge v-if="list.is_cob == 1"class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1" variant="warning-200">COB</b-badge>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Jenis Bayar</td>
                                        <td class="px-3">:</td>
                                        <td>
                                            <div v-if="this.regData.registration_insurance">
                                                <div v-for="list in this.regData.registration_insurance">
                                                    <template>
                                                        {{ list.insurance_name }} - {{ list.company_name }}
                                                    </template>
                                                    <b-badge v-if="list.is_cob == 1"class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1" variant="warning-200">COB</b-badge>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="regData.checkout_date">
                                        <td class="first-cell">Tanggal Masuk</td>
                                        <td class="px-3">:</td>
                                        <td>{{ localDateRanap(regData.register_date) }}</td>
                                    </tr>
                                    <tr v-else>
                                        <td class="first-cell">Tanggal Masuk</td>
                                        <td class="px-3">:</td>
                                        <td>{{ localDateRanap(regData.register_date) }}</td>
                                    </tr>
                                    <tr v-if="regData.checkout_date">
                                        <td class="first-cell">Tanggal Keluar</td>
                                        <td class="px-3">:</td>
                                        <td>{{ localDateRanap(regData.checkout_date) }}</td>
                                    </tr>
                                    <tr v-else>
                                        <template v-if="regData.health_facility_id === '3cb626f5-9148-4fb1-8663-3d7f028b01a6' || regData.health_facility_id === '00e8fed1-a85d-4f42-96dd-bfff63086945'">
                                            <td class="first-cell">Tanggal Keluar</td>
                                            <td class="px-3">:</td>
                                            <td>{{ localDateRajal(regData.register_date) }}</td>
                                        </template>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </section>
                    <section class="cmain mt-24">
                        <table class="text-center w-100 fs-16">
                            <thead class="">
                                <tr class="bg-neutral-100">
                                    <th width="20%">TANGGAL</th>
                                    <th>JENIS TINDAKAN</th>
                                    <th>QTY</th>
                                    <th>HARGA</th>
                                    <th>DISKON</th>
                                    <th>SUBTOTAL</th>
                                    <th>LOKASI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-if="trans_1.length > 0" v-for="(item,i) in trans_1">
                                    <tr v-if="i == trans_1.length-1" class="text-right" style="background-color: #FEF8D8">
                                        <td colspan="2" class="text-left">Keperawatan</td>
                                        <td colspan="4">{{ trans_1_total | currency }}</td>
                                        <td colspan="1"></td>
                                    </tr>
                                </template>
                                <template v-if="TypeCostingOptions" v-for="(typec,i) in group_trans_1">
                                    <tr class="text-right">
                                        <td colspan="7" class="text-left">{{typec.title}}</td>
                                    </tr>
                                    <template v-if="trans_1.length > 0" v-for="(item,i) in trans_1">
                                        <tr v-if="item.classification_three_type_id === typec.value">
                                            <td class="text-center">{{ item.date_added }}</td>
                                            <td class="text-left">{{ item.costing_name }}</td>
                                            <td class="text-center">{{ item.var_1 }}</td>
                                            <td class="text-right">{{ item.var_2 | currency }}</td>
                                            <td class="text-right">0</td>
                                            <td class="text-right">{{ item.var_1*item.var_2 | currency }}</td>
                                            <td class="text-center">{{ item.health_facility_item_name }}</td>
                                        </tr>
                                    </template>
                                </template>
                                <template v-if="trans_2.length > 0" v-for="(item,i) in trans_2">
                                    <tr v-if="i == trans_2.length-1" class="text-right" style="background-color: #FEF8D8">
                                        <td colspan="2" class="text-left">Tindakan/Konsul Dokter</td>
                                        <td colspan="4">{{ trans_2_total | currency }}</td>
                                        <td colspan="1"></td>
                                    </tr>
                                </template>
                                <template v-if="TypeCostingOptions" v-for="(typec,i) in group_trans_2">
                                    <tr class="text-right">
                                        <td colspan="7" class="text-left">{{typec.title}}</td>
                                    </tr>
                                    <template v-for="(item,i) in trans_2"  v-if="trans_2.length > 0" >
                                        <tr v-if="item.classification_three_type_id === typec.value">
                                            <td class="text-center">{{ item.date_added }}</td>
                                            <td class="text-left">{{item.costing_name}}<br>{{item.doctor_in_charge_name}}</td>
                                            <td>{{ item.var_2 }}</td>
                                            <td class="text-right">{{ item.var_1 | currency }}</td>
                                            <td class="text-right">{{ item.var_4 ? item.var_4 : 0 | currency }}</td>
                                            <td class="text-right">{{ item.count_total | currency }}</td>
                                            <td>{{item.health_facility_item_name}}</td>
                                        </tr>
                                    </template>
                                </template>
                                <template v-if="trans_5.length > 0" v-for="(item,i) in trans_5">
                                    <tr v-if="i == trans_5.length-1" class="text-right" style="background-color: #FEF8D8">
                                        <td colspan="2" class="text-left">Pemakaian Alat</td>
                                        <td colspan="4">{{ trans_5_total | currency }}</td>
                                        <td colspan="1"></td>
                                    </tr>
                                </template>

                                <template v-if="trans_5.length > 0" v-for="(item,i) in trans_5">
                                    <tr>
                                        <td class="text-center">{{ item.date_added }}</td>
                                        <td class="text-left">{{item.costing_name}}<br>{{item.doctor_in_charge_name}}</td>
                                        <td>1</td>
                                        <td class="text-right">{{ item.var_1 | currency}}</td>
                                        <td class="text-right">0</td>
                                        <td class="text-right">{{ item.var_1 | currency}}</td>
                                        <td>{{item.health_facility_item_name}}</td>
                                    </tr>
                                </template>
                                <template v-if="trans_7.length > 0" v-for="(item,i) in trans_7">
                                    <tr v-if="i == trans_7.length-1" class="text-right" style="background-color: #FEF8D8">
                                        <td colspan="2" class="text-left"></td>
                                        <td colspan="4">{{ trans_7_total | currency }}</td>
                                        <td colspan="1"></td>
                                    </tr>
                                </template>

                                <template v-if="trans_7.length > 0" v-for="(item,i) in trans_7">
                                    <tr>
                                        <td class="text-center">{{ item.date_added }}</td>
                                        <td class="text-left">{{item.costing_name}}<br>{{item.doctor_in_charge_name}}</td>
                                        <td>{{item.var_2}}</td>
                                        <td class="text-right">{{ item.var_1 | currency}}</td>
                                        <td class="text-right">0</td>
                                        <td class="text-right">{{ item.var_1*item.var_2 | currency}}</td>
                                        <td>{{item.health_facility_item_name}}</td>
                                    </tr>
                                </template>
                                <template v-if="trans_11.length > 0" v-for="(item,i) in trans_11">
                                    <tr v-if="i == trans_11.length-1" class="text-right" style="background-color: #FEF8D8">
                                        <td colspan="2" class="text-left">LABORATORIUM</td>
                                        <td colspan="4">{{ trans_11_total | currency }}</td>
                                        <td colspan="1"></td>
                                    </tr>
                                </template>

                                <template v-if="trans_11.length > 0" v-for="(item,i) in trans_11">
                                    <tr>
                                        <td class="text-center">{{ item.date_added }}</td>
                                        <td class="text-left">{{ item.costing_name }}</td>
                                        <td>1</td>
                                        <td class="text-right">{{ item.var_1 | currency }}</td>
                                        <td class="text-right">1</td>
                                        <td class="text-right">{{ item.var_1 | currency }}</td>
                                        <td>LABORATORIUM</td>
                                    </tr>
                                </template>
                                <template v-if="trans_12.length > 0" v-for="(item,i) in trans_12">
                                    <tr v-if="i == trans_12.length-1" class="text-right" style="background-color: #FEF8D8">
                                        <td colspan="2" class="text-left">RADIOLOGI</td>
                                        <td colspan="4">{{ trans_12_total | currency }}</td>
                                        <td colspan="1"></td>
                                    </tr>
                                </template>

                                <template v-if="trans_12.length > 0" v-for="(item,i) in trans_12">
                                    <tr>
                                        <td class="text-center">{{ item.date_added }}</td>
                                        <td class="text-left">{{ item.costing_name }}</td>
                                        <td>1</td>
                                        <td class="text-right">{{ item.var_1 | currency }}</td>
                                        <td class="text-right">0</td>
                                        <td class="text-right">{{ item.var_1 | currency }}</td>
                                        <td>RADIOLOGI</td>
                                    </tr>
                                </template>
                                <template v-if="trans_15.length > 0" v-for="(item,i) in trans_15">
                                    <tr v-if="i == trans_15.length-1" class="text-right" style="background-color: #FEF8D8">
                                        <td colspan="2" class="text-left"></td>
                                        <td colspan="4">{{ trans_15_total | currency }}</td>
                                        <td colspan="1"></td>
                                    </tr>
                                </template>

                                <template v-if="trans_15.length > 0" v-for="(item,i) in trans_15">
                                    <tr>
                                        <td class="text-center">{{ item.date_added }}</td>
                                        <td class="text-left">{{ item.costing_name }}</td>
                                        <td>1</td>
                                        <td class="text-right">{{ item.var_1 | currency }}</td>
                                        <td class="text-right">0</td>
                                        <td class="text-right">{{ item.var_1 | currency }}</td>
                                        <td>{{ regData.health_facility_item_name }}</td>
                                    </tr>
                                </template>
                                <template v-if="trans_10.length > 0" v-for="(item,i) in trans_10">
                                    <tr v-if="i == trans_10.length-1" class="text-right" style="background-color: #FEF8D8">
                                        <td colspan="2" class="text-left"></td>
                                        <td colspan="4">{{ trans_10_total | currency }}</td>
                                        <td colspan="1"></td>
                                    </tr>
                                </template>

                                <template v-if="trans_10.length > 0" v-for="(item,i) in trans_10">
                                    <tr>
                                        <td class="text-center">{{ item.date_added }}</td>
                                        <td class="text-left">{{ item.costing_name }}</td>
                                        <td>{{ item.var_2 }}</td>
                                        <td class="text-right">{{ item.var_1 | currency }}</td>
                                        <td class="text-right">0</td>
                                        <td class="text-right">{{ item.var_1*item.var_2 | currency }}</td>
                                        <td>{{ item.health_facility_item_name }}</td>
                                    </tr>
                                </template>

                                <!-- <tr>
                                    <td colspan="7" class="text-left">FARMASI</td>
                                </tr> -->
                                <template v-if="trans_14.length > 0" v-for="(item,i) in trans_14">
                                    <tr v-if="i == trans_14.length-1" class="text-right" style="background-color: #FEF8D8">
                                        <td colspan="2" class="text-left">FARMASI</td>
                                        <td colspan="4">{{ trans_14_total | currency }}</td>
                                        <td colspan="1"></td>
                                    </tr>
                                </template>
                                <template v-if="trans_14.length > 0" v-for="(item,i) in trans_14">
                                    <template v-for="(list, j) in item.prescription_item">
                                        <tr v-if="(list.is_retur_full !== 1 || list.pharmacy_catalog_id_retur) && list.qty-(list.is_retur_full === 1 && list.pharmacy_catalog_id_retur || list.pharmacy_catalog_id_ref ? 0 : list.qty_retur) !== 0">
                                            <td class="text-center">{{ item.date_added }}</td>
                                            <td class="text-left">{{list.pharmacy_catalog_name}}</td>
                                            <td>{{list.qty-(list.is_retur_full === 1 && list.pharmacy_catalog_id_retur ? 0 : list.qty_retur)}}</td>
                                            <!-- <td>{{ item.depo_name }}</td> -->
                                            <td class="text-right">{{list.var_1 | currency}}</td>
                                            <td class="text-right">0</td>
                                            <td class="text-right">{{ list.var_1*(list.qty-(list.is_retur_full === 1 && list.pharmacy_catalog_id_retur ? 0 : list.qty_retur)) | currency }}</td>
                                            <td class="text-center">FARMASI</td>
                                        </tr>
                                    </template>
                                    <!-- <tr v-if="i == trans_14.length-1" class="text-right bg-warning-100">
                                        <td colspan="6">{{ trans_14_total | currency }}</td>
                                    </tr> -->
                                </template>

                                <tr>
                                    <td colspan="7" class="text-left">ADMINISTRASI</td>
                                </tr>

                                <template v-if="trans_13.length > 0" v-for="(item,i) in trans_13">
                                    <tr>
                                        <td class="text-center">{{ item.date_added }}</td>
                                        <td class="text-left">{{ item.costing_name }}</td>
                                        <td>1</td>
                                        <td class="text-right">{{ item.var_1 | currency}}</td>
                                        <td class="text-right">{{ item.var_4 | currency}}</td>
                                        <td class="text-right">{{ item.count_total | currency}}</td>
                                        <td>{{ regData.health_facility_item_name }}</td>
                                    </tr>
                                    <tr v-if="i == trans_13.length-1" class="text-right bg-warning-100">
                                        <td colspan="6">{{ trans_13_total | currency }}</td>
                                    </tr>
                                </template>

                                <tr>
                                    <th colspan="5" class="text-right">Biaya</th>
                                    <td class="text-right">{{grand_total | currency}}</td>
                                </tr>

                                <tr>
                                    <th colspan="5" class="text-right">Grand Total</th>
                                    <td class="text-right">{{grand_total | currency}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </section>
                    <section class="row mt-64">
                        <div class="col-6 text-center">
                            <p>Pasien / Keluarga Pasien</p>
                            <p class="text-uppercase mt-64">({{ regData.alias_name }} {{ regData.name }})</p>
                        </div>
                        <div class="col-6 text-center">
                            <template v-if="regData.checkout_date">
                                <p>Bekasi, {{ localDate(regData.checkout_date) }}</p>
                            </template>
                            <template v-else>
                                <template v-if="regData.health_facility_id === '3cb626f5-9148-4fb1-8663-3d7f028b01a6' || regData.health_facility_id === '00e8fed1-a85d-4f42-96dd-bfff63086945'">
                                    <p>Bekasi, {{ localDate(regData.register_date) }}</p>
                                </template>
                            </template>
                            <template v-if="invoices.ttd">
                                <b-img :src="urlbe+invoices.ttd" style="width: 120px;"/>
                            </template>
                            <p>( Kasir )</p>
                            <p class="text-uppercase mt-2">({{ invoices.cashier_name }})</p>
                        </div>

                    </section>
                </div>
            </div>
        </b-modal>
    </div>
</template>
<script>
import moment from 'moment';
import { mapState } from 'vuex';
export default {
    data() {
        return {
            active: {
                modal: {
                    main: false,
                }
            },
            urlbe:process.env.VUE_APP_IMAGE,
            date_now:moment(new Date()).locale('id').format('LL'),
            id : '',
            regData:{},
            invoices:{},

            trans_1: [],
            trans_2: [],
            trans_5: [],
            trans_7: [],
            trans_10: [],
            trans_11: [],
            trans_12: [],
            trans_13: [],
            trans_14: [],
            trans_15: [],

            trans_1_total: 0,
            trans_2_total: 0,
            trans_5_total: 0,
            trans_7_total: 0,
            trans_10_total: 0,
            trans_11_total: 0,
            trans_12_total: 0,
            trans_13_total: 0,
            trans_14_total: 0,
            trans_15_total: 0,

            grand_total: 0,

            group_trans_1:[],
            group_trans_2:[]
        }
    },
    computed: {
        ...mapState({
            TypeCostingOptions: state => state.helper.options.typecostingc
        })
    },
    methods: {
        async parentTrigger(id) {
            this.id  =  ''

            this.regData = {}
            this.invoices = {}
            this.trans_1 =  []
            this.trans_2 =  []
            this.trans_5 =  []
            this.trans_7 =  []
            this.trans_10 =  []
            this.trans_11 =  []
            this.trans_12 =  []
            this.trans_13 =  []
            this.trans_14 =  []
            this.trans_15 =  []

            this.trans_1_total =  0
            this.trans_2_total =  0
            this.trans_5_total =  0
            this.trans_7_total =  0
            this.trans_10_total =  0
            this.trans_11_total =  0
            this.trans_12_total =  0
            this.trans_13_total =  0
            this.trans_14_total =  0
            this.trans_15_total =  0

            this.grand_total =  0

            this.group_trans_1 = []
            this.group_trans_2 = []
            await this.openMainModal(id)
        },

        async openMainModal(id) {
            this.active.modal.main = true
            if (id) {
                this.id = id
                this.getData(id)
                this.$store.dispatch('helper/getParam', 'costing_c_one')
            }
        },

        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },
        localDateRanap(input_date) {
            return moment(input_date).locale("id").format('LLLL')
        },
        localDateRajal(input_date) {
            return moment(input_date).add(moment.duration(1, 'hours')).locale("id").format('LLLL')
        },
        handleKeyPress(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault();
                this.printCanvas();
            }
        },
        printCanvas() {
            const canvas = this.$refs.canvas;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = canvas.innerHTML;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload()
        },
        closeWindow(){
            window.close()
        },
        getData (id) {
            this.$store.dispatch('payment/getByInvoiceRegisterId', id).then(data => {

                if (data.length > 0) {
                    this.invoices = data[0]
                    this.mappingArray(this.invoices.transactions)
                    // console.log('this.invoices: ', this.invoices);

                    this.$store.dispatch('registration/getByRegistrationId', data[0].registration_id).then(data => {
                        this.regData = data[0]
                        // this.show = false
                    })
                }
            })
        },
        mappingArray(transactions) {
            if (transactions) {
                this.trans_1_total = 0
                this.trans_2_total = 0
                this.trans_5_total = 0
                this.trans_7_total = 0
                this.trans_10_total = 0
                this.trans_11_total = 0
                this.trans_12_total = 0
                this.trans_13_total = 0
                this.trans_14_total = 0
                this.trans_15_total = 0

                this.grand_total = 0


                for (let index = 0; index < transactions.length; index++) {
                    const element = transactions[index];


                    if (element.type_transaction_id === 1) {
                        let price = element.var_1 != '' ? element.var_1 : 0
                        let qty = element.var_2 != '' ? element.var_2 : 0

                        this.trans_1.push(element)
                        this.trans_1.sort(function(a,b) {return (a.costing_name > b.costing_name) ? 1 : ((b.costing_name > a.costing_name) ? -1 : 0);} );
                        this.trans_1_total += (price*qty)
                        this.trans_1.sort((a, b) => new Date(a.date_added) - new Date(b.date_added));
                        this.group_trans_1.push({title:element.classification_three_type_name,value:element.classification_three_type_id})
                    }

                    if (element.type_transaction_id === 2) {
                        let discount = element.var_4 != '' ? element.var_4 : 0
                        let price = element.var_1 != '' ? element.var_1 : 0
                        let qty = element.var_2 != '' ? element.var_2 : 0
                        let temp_total = (price*qty) - discount

                        this.trans_2.push({ ...element, count_total: temp_total })
                        this.trans_2.sort(function(a,b) {return (a.costing_name > b.costing_name) ? 1 : ((b.costing_name > a.costing_name) ? -1 : 0);} );
                        this.trans_2_total += temp_total
                        this.group_trans_2.push({title:element.classification_three_type_name,value:element.classification_three_type_id})
                        this.trans_2.sort((a, b) => new Date(a.date_added) - new Date(b.date_added));
                    }

                    if (element.type_transaction_id === 5) {
                        this.trans_5.push(element)
                        this.trans_5_total += element.var_1
                        this.trans_5.sort((a, b) => new Date(a.date_added) - new Date(b.date_added));
                    }

                    if (element.type_transaction_id === 10) {
                        let price = element.var_1 != '' ? element.var_1 : 0
                        let qty = element.var_2 != '' ? element.var_2 : 0

                        this.trans_10.push(element)
                        this.trans_10_total += (price*qty)
                        this.trans_10.sort((a, b) => new Date(a.date_added) - new Date(b.date_added));
                    }

                    if (element.type_transaction_id === 11) {
                        this.trans_11.push(element)
                        this.trans_11_total += element.var_1
                        this.trans_11.sort((a, b) => new Date(a.date_added) - new Date(b.date_added));
                    }

                    if (element.type_transaction_id === 12) {
                        this.trans_12.push(element)
                        this.trans_12_total += element.var_1
                        this.trans_12.sort((a, b) => new Date(a.date_added) - new Date(b.date_added));
                    }

                    if (element.type_transaction_id === 13) {
                        let discount = element.var_4 != '' ? element.var_4 : 0
                        let price = element.var_1 != '' ? element.var_1 : 0
                        let temp_total = price - discount

                        this.trans_13.push({ ...element, count_total: temp_total })
                        this.trans_13_total += temp_total
                        this.trans_13.sort((a, b) => new Date(a.date_added) - new Date(b.date_added));
                    }

                    if (element.type_transaction_id === 14) {
                        this.trans_14.push(element)
                    }

                    if (element.type_transaction_id === 15) {
                        this.trans_15.push(element)
                        this.trans_15_total += element.var_1
                        this.trans_15.sort((a, b) => new Date(a.date_added) - new Date(b.date_added));
                    }

                    if (element.type_transaction_id === 7) {
                        let discount = 0
                        let price = element.var_1 != '' ? element.var_1 : 0
                        let qty = element.var_2 != '' ? element.var_2 : 0
                        let temp_total = (price*qty) - discount

                        this.trans_7.push({ ...element, count_total: temp_total })
                        this.trans_7.sort(function(a,b) {return (a.costing_name > b.costing_name) ? 1 : ((b.costing_name > a.costing_name) ? -1 : 0);} );
                        this.trans_7_total += temp_total
                        this.trans_7.sort((a, b) => new Date(a.date_added) - new Date(b.date_added));
                    }
                }
                this.getTotalPrec(this.trans_14)

                const group_trans_1 = this.group_trans_1.filter((item, index, self) =>
                    index === self.findIndex(t => JSON.stringify(t) === JSON.stringify(item))
                );
                this.group_trans_1 = group_trans_1
                this.group_trans_1.sort((a, b) => new Date(a.value) - new Date(b.value));

                const group_trans_2 = this.group_trans_2.filter((item, index, self) =>
                    index === self.findIndex(t => JSON.stringify(t) === JSON.stringify(item))
                );
                this.group_trans_2 = group_trans_2
                this.group_trans_2.sort((a, b) => new Date(a.value) - new Date(b.value));
                this.grand_total = this.trans_1_total  + this.trans_2_total  + this.trans_5_total  + this.trans_7_total  + this.trans_10_total  + this.trans_11_total  + this.trans_12_total + this.trans_14_total  + this.trans_15_total

                if (this.grand_total) {
                    this.trans_13 = []
                    this.trans_13_total = 0
                    for (let index = 0; index < transactions.length; index++) {
                        const element = transactions[index]
                        if (element.type_transaction_id === 13) {
                            let discount = element.var_4 != '' ? element.var_4 : 0
                            let price = element.var_1 != '' ? element.var_1 : 0
                            if (element.costing_name === 'SERVICE CHARGE') {
                                price = 0
                                price = (this.grand_total * 3)/100 > 5000000 ? 5000000 : (this.grand_total * 3)/100
                                element.var_1 = price
                            }
                            let temp_total = price - discount
                            this.trans_13.push({ ...element, count_total: temp_total })
                            this.trans_13_total += temp_total
                            this.trans_13.sort((a, b) => new Date(a.date_added) - new Date(b.date_added));
                        }
                    }
                    this.grand_total = 0
                    this.grand_total = this.trans_1_total  + this.trans_2_total  + this.trans_5_total  + this.trans_7_total  + this.trans_10_total  + this.trans_11_total  + this.trans_12_total  + this.trans_13_total  + this.trans_14_total  + this.trans_15_total
                }
                // console.log('this.trans_14: ', this.trans_14);

            }
        },
        getTotalPrec(param) {
            if (param.length > 0) {
                let temp_total = 0
                for (let index = 0; index < param.length; index++) {
                    const element = param[index].prescription_item;

                    if (element) {
                        for (let j = 0; j < element.length; j++) {
                            const data = element[j];

                            if (data.is_retur_full === 0 || data.pharmacy_catalog_id_retur) {
                                let tmp_price = data.var_1 ? data.var_1 : 0
                                let tmp_qty = data.qty ? data.qty : 0
                                // let tmp_qty_retur = data.qty_retur != '' || data.qty_retur != null ? data.qty_retur : 0
                                let tmp_qty_retur = data.is_retur_full === 1 && data.pharmacy_catalog_id_retur ? 0 : data.qty_retur
                                let final_qty = tmp_qty - tmp_qty_retur

                                temp_total += (tmp_price*final_qty)
                            }
                        }
                    }
                }
                this.trans_14_total = temp_total
                // console.log('temp_total: ', temp_total);
            } else {
                this.trans_14_total = 0
            }
        }
    },
    filters:{
        currency(value) {
            if (value){
                var val = Math.round(Number(value) *100) / 100;
                var parts = val.toString().split(".");
                var num = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",") + (parts[1] ? "." + parts[1] : "");
                return num
                // return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
            } else {
                return 0
            }
            // return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'IDR' }).format(value)
        }
    },
    mounted() {
        window.addEventListener('keydown', this.handleKeyPress);
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },
};
</script>