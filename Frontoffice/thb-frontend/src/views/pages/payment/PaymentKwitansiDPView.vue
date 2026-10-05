<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="row align-items-center w-100">
                        <div class="col-2 text-center">
                            <img src="@/@core/assets/images/logo-thb-small.png" width="60%" alt="rs-logo"> <br>
                        </div>
                        <div class="col text-center">
                            <p>RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                        </div>
                        <div class="col-2 text-center">
                            <img src="@/@core/assets/images/lafki-logo.png" width="60%" alt="lafki-logo"> <br>
                        </div>
                    </div>
                    <div class="canvas-body mt-32 px-40">
                        <section>
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
                                            <td></td>
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
                                    </table>
                                </div>
                            </div>
                        </section>
                        <section class="mt-24">
                            <b-table-simple bordered small class="text-center">
                                <b-thead>
                                    <b-tr class="bg-neutral-100">
                                        <b-th>JENIS TINDAKAN</b-th>
                                        <b-th>LOKASI</b-th>
                                        <b-th>QTY</b-th>
                                        <b-th>HARGA</b-th>
                                        <b-th>TOTAL</b-th>
                                    </b-tr>
                                </b-thead>
                                <b-tbody>
                                    <template v-for="(resultInv,i) in invoices.transactions" v-if="resultInv.type_transaction_id === 2 || resultInv.type_transaction_id === 5 || resultInv.type_transaction_id === 11 || resultInv.type_transaction_id === 12">
                                    <b-tr>
                                        <b-td class="text-left">{{resultInv.costing_name}}</b-td>
                                        <b-td>{{resultInv.health_facility_item_name}}</b-td>
                                        <b-td>1</b-td>
                                        <b-td class="text-right">{{resultInv.var_1 | currency}}</b-td>
                                        <b-td class="text-right">{{resultInv.var_1 | currency}}</b-td>
                                    </b-tr>
                                    </template>
                                    <template v-for="(resultInv,i) in invoices.transactions" v-if="resultInv.type_transaction_id === 15">
                                    <b-tr>
                                        <b-td class="text-left">{{resultInv.costing_name}}</b-td>
                                        <b-td>{{regData.health_facility_item_name}}</b-td>
                                        <b-td>1</b-td>
                                        <b-td class="text-right">{{resultInv.var_1 | currency}}</b-td>
                                        <b-td class="text-right">{{resultInv.var_1 | currency}}</b-td>
                                    </b-tr>
                                    </template>
                                    <!-- <b-tr>
                                        <b-td colspan="5" class="text-right font-weight-bold">Rp1.000.000,00</b-td>
                                    </b-tr> -->
                                    <b-tr>
                                        <b-td colspan="5" class="text-left">FARMASI</b-td>
                                    </b-tr>
                                    <template v-for="(resultInv,i) in invoices.transactions" v-if="resultInv.type_transaction_id === 14">
                                    <template v-for="(resultInvItem,i) in resultInv.prescription_item" v-if="resultInv.pharmacy_prescription_id === resultInvItem.pharmacy_prescription_id">
                                        <b-tr>
                                        <b-td class="text-left">{{resultInvItem.pharmacy_catalog_name}}</b-td>
                                        <b-td>{{resultInv.depo_name}}</b-td>
                                        <b-td>{{resultInvItem.qty-(resultInvItem.is_retur_full === 1 && resultInvItem.pharmacy_catalog_id_retur || resultInvItem.pharmacy_catalog_id_ref ? 0 : resultInvItem.qty_retur)}}</b-td>
                                        <b-td class="text-right">{{resultInvItem.var_1 | currency}}</b-td>
                                        <b-td class="text-right">{{resultInvItem.var_1*resultInvItem.qty-(resultInvItem.is_retur_full === 1 && resultInvItem.pharmacy_catalog_id_retur || resultInvItem.pharmacy_catalog_id_ref ? 0 : resultInvItem.qty_retur) | currency}}</b-td>
                                    </b-tr>
                                    </template>
                                </template>
                                    <b-tr>
                                        <b-td colspan="5" class="text-left">ADMINISTRASI</b-td>
                                    </b-tr>
                                    <!-- <b-tr>
                                        <b-td colspan="5" class="text-right font-weight-bold">Rp1.000.000,00</b-td>
                                    </b-tr> -->

                                    <template v-for="(resultInv,i) in invoices.transactions" v-if="resultInv.type_transaction_id === 13">
                                    <b-tr>
                                        <b-td class="text-left">{{resultInv.costing_name}}</b-td>
                                        <b-td>{{resultInv.health_facility_item_name}}</b-td>
                                        <b-td>1</b-td>
                                        <b-td class="text-right">{{resultInv.var_1 | currency}}</b-td>
                                        <b-td class="text-right">{{resultInv.var_1 | currency}}</b-td>
                                    </b-tr>
                                    </template>
                                    <!-- <b-tr>
                                        <b-td colspan="5" class="text-right font-weight-bold">Rp30.000,00</b-td>
                                    </b-tr> -->
                                    <b-tr>
                                        <b-th colspan="4" class="text-right">Biaya</b-th>
                                        <b-td class="text-right">{{invoices.var_3 | currency}}</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-th colspan="4" class="text-right">Diskon</b-th>
                                        <b-td class="text-right"></b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-th colspan="4" class="text-right">Grand Total</b-th>
                                        <b-td class="text-right">{{invoices.var_3 | currency}}</b-td>
                                    </b-tr>
                                    <template v-for="(rowpm,index) in invoices.payment">
                                        <b-tr>
                                            <b-th colspan="3" class="text-right">Cara Bayar</b-th>
                                            <b-td class="text-right">{{ rowpm.payment_method }}</b-td>
                                            <b-td class="text-right">{{ rowpm.var_3 | currency }}</b-td>
                                        </b-tr>
                                    </template>
                                </b-tbody>
                            </b-table-simple>
                        </section>
                        <section class="row mt-64">
                            <div class="col-6 text-center">
                                <p>Pasien / Keluarga Pasien</p>
                                <p class="text-uppercase mt-64">({{ regData.alias_name }} {{ regData.name }})</p>
                            </div>
                            <div class="col-6 text-center">
                                <p>Bekasi, {{ localDate(regData.register_date) }}</p>
                                <!-- <p>Bekasi, {{ date_now }}</p> -->
                                <p>( Kasir )</p>
                                <p class="text-uppercase mt-64">({{ invoices.cashier_name }})</p>
                            </div>

                        </section>
                    </div>
                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
import moment from 'moment';
export default {
    data() {
        return {
            id : '',
            date_now:moment(new Date()).locale('id').format('LL'),
            regData:{},
            invoices:{},
        }
    },
    methods: {
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
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
            this.$store.dispatch('payment/getByDpId', id).then(data => {
                this.invoices = data[0]
                this.$store.dispatch('registration/getByRegistrationId', data[0].registration_id).then(data => {
                    this.regData = data[0]
                    // this.show = false
                })
            })
        }
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'IDR' }).format(value)
        }
    },
    mounted () {
        if (this.$route.params.id) {
            // this.show = true
            this.id = this.$route.params.id
            this.getData(this.$route.params.id)
        }
    }
};

</script>
<style>
@import '@/@core/css/main/main.min.css';
@import url('https://fonts.cdnfonts.com/css/lucida-console');
/* @import url('https://fonts.cdnfonts.com/css/dot-matrix'); */

@media print {
    @page {
        size: A4;
    }
}
body {
    letter-spacing: 1px;
    color: #000000;
    font-weight: 800;
    line-height: 1.5;
    font-family: 'Lucida Console', sans-serif;
    /* font-family: 'Dot Matrix', sans-serif; */
}
td {
    padding: 2px 0;
}

p {
    margin: 0;
    padding: 0;
}

.main-content {
    margin-top: 32px;
    padding-bottom: 32px;
}

.w-40 {
    width: 40%;
}

</style>
