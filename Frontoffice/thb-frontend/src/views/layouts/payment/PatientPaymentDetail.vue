<template>
    <div>
        <div class="card border-0 bg-neutral-100 mt-32">
            <div class="card-body">
                <p class="text-bold-18">Data Rincian Tagihan Pasien</p>
                <div class="row mt-24">
                    <div class="col col-lg-6 px-0 px-lg-4">
                        <b-table-simple class="text-bold-14">
                            <b-tr>
                                <b-td class="border-0" style="width: 30%;">No Reg / RM</b-td>
                                <b-td class="border-0 text-center" style="width: 10%;">:</b-td>
                                <b-td class="border-0">{{regData.code}}</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td class="border-0" style="width: 30%;">Cara Bayar</b-td>
                                <b-td class="border-0 text-center" style="width: 10%;">:</b-td>
                                <b-td class="border-0">
                                    <div v-if="this.regData.registration_insurance">
                                        <div v-for="list in this.regData.registration_insurance">
                                            <template>
                                                {{ list.insurance_name }} - {{ list.company_name }}
                                            </template>
                                            <b-badge v-if="list.is_cob == 1"class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1" variant="warning-200">COB</b-badge>
                                        </div>
                                    </div>
                                </b-td>
                            </b-tr>
                            <b-tr>
                                <b-td class="border-0" style="width: 30%;">Nama Pasien</b-td>
                                <b-td class="border-0 text-center" style="width: 10%;">:</b-td>
                                <b-td class="border-0">{{regData.name}} [{{regData.alias_name}}]</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td class="border-0" style="width: 30%;">Kelamin</b-td>
                                <b-td class="border-0 text-center" style="width: 10%;">:</b-td>
                                <b-td class="border-0">{{regData.sex_name}}</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td class="border-0" style="width: 30%;">Alamat</b-td>
                                <b-td class="border-0 text-center" style="width: 10%;">:</b-td>
                                <b-td class="border-0">{{regData.address}}</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td class="border-0" style="width: 30%;">Poliklinik / Ruangan</b-td>
                                <b-td class="border-0 text-center" style="width: 10%;">:</b-td>
                                <b-td class="border-0">{{regData.health_facility_item_name}}</b-td>
                            </b-tr>

                        </b-table-simple>
                    </div>
                    <div class="col col-lg-6 px-0 px-lg-4">
                        <b-table-simple class="text-semi-14">
                            <b-tr>
                                <b-td class="border-0" style="width: 30%;">Tgl Masuk</b-td>
                                <b-td class="border-0 text-center" style="width: 10%;">:</b-td>
                                <b-td class="border-0">{{regData.register_date}}</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td class="border-0" style="width: 30%;">NORM</b-td>
                                <b-td class="border-0 text-center" style="width: 10%;">:</b-td>
                                <b-td class="border-0">{{regData.norm}}</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td class="border-0" style="width: 30%;">Tanggal Lahir</b-td>
                                <b-td class="border-0 text-center" style="width: 10%;">:</b-td>
                                <b-td class="border-0">{{regData.birth_date}}</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td class="border-0" style="width: 30%;">Usia</b-td>
                                <b-td class="border-0 text-center" style="width: 10%;">:</b-td>
                                <b-td class="border-0">{{parsed_age(regData.birth_date)}}</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td class="border-0" style="width: 30%;">No HP</b-td>
                                <b-td class="border-0 text-center" style="width: 10%;">:</b-td>
                                <b-td class="border-0">{{regData.phone}}</b-td>
                            </b-tr>
                        </b-table-simple>
                    </div>
                </div>
            </div>
        </div>
        <!-- here button menu -->
        <div class="col-lg-5 mt-40">
            <div class="row">
                <div class="col px-1 px-lg-2">
                    <b-button @click="changeTab('tab1')" pill :variant="active.tab === 'tab1' ? 'indigo' : 'outline-indigo'"
                        class="w-100">Rincian</b-button>
                </div>
                <div class="col px-1 px-lg-2">
                    <b-button @click="changeTab('tab4')" pill :variant="active.tab === 'tab2' ? 'indigo' : 'outline-indigo'"
                        class="w-100">Obat Retur</b-button>
                </div>
                <div class="col px-1 px-lg-2">
                    <b-button @click="changeTab('tab2')" pill :variant="active.tab === 'tab2' ? 'indigo' : 'outline-indigo'"
                        class="w-100">Pembayaran</b-button>
                </div>
                <div class="col px-1 px-lg-2">
                    <b-button @click="changeTab('tab3')" pill :variant="active.tab === 'tab3' ? 'indigo' : 'outline-indigo'"
                        class="w-100">Deposit</b-button>
                </div>
            </div>
        </div>
        <!-- here inside menu -->
        <div class="mt-20">
            <section v-if="active.tab === 'tab1'">
                <b-table-simple responsive bordered>
                    <b-thead class="bg-neutral-100 text-bold-14 text-center">
                        <b-tr>
                            <b-th style="vertical-align: middle; min-width: 120px;">Tanggal & Jam</b-th>
                            <b-th style="vertical-align: middle; width: 45%;">Nama Rincian</b-th>
                            <b-th style="vertical-align: middle; min-width: 120px;">Harga</b-th>
                            <b-th style="vertical-align: middle; min-width: 5px;">Qty</b-th>
                            <b-th style="vertical-align: middle; min-width: 120px;">Diskon</b-th>
                            <b-th style="vertical-align: middle; min-width: 120px;">Total</b-th>
                            <b-th style="vertical-align: middle; width: 120px;"> 
                                <div>Buat Tagihan</div>
                                <input type="checkbox" @change="toggleSelectAll($event)" />
                            </b-th>
                        </b-tr>
                    </b-thead>
                    <b-tbody>
                        <template v-for="(item, index) in combinedItems">
                            <b-tr :key="index" :class="item.transaction_id === item.id ? 'bg-info-200' : item.checked ? 'bg-warning-200' : '' " >                                
                                <b-td :rowspan="item.transaction_from === 'itemsPres' ? 2 : 1" class="text-center">{{ item.date_added }}</b-td>
                                <b-td>{{ item.transaction_from !== 'itemsPres' ? (item.transaction_from === 'itemsDoctor' ? item.costing_name+' '+item.doctor_in_charge_name : item.costing_name ) : item.no_order }}<br>
                                    {{ item.transaction_from === 'itemsBed' ? item.checkin_date+' s/d '+item.checkout_date : '' }}
                                    <b-button v-if="item.transaction_from === 'itemsBed'" @click="onModalBed(item)" size="sm" variant="success" class="mr-2" >Edit Tarif Kamar</b-button>
                                </b-td>
                                <b-td class="text-right">{{ item.transaction_from === 'itemsNurse' ? item.var_2 : item.var_1 | currency }}</b-td>
                                <b-td class="text-center">{{ item.transaction_from === 'itemsDoctor' || item.transaction_from === 'itemsBlood' || item.transaction_from === 'itemsTool' ? (item.transaction_from === 'itemsBed' || item.transaction_from === 'itemsNurse' ? item.var_1 : item.var_2) :  (item.transaction_from === 'itemsNurse' ? item.var_1  : (item.transaction_from === 'itemsBed') ? item.var_2 : 1) }}</b-td>
                                <b-td class="text-right">
                                    <template v-if="item.transaction_from === 'itemsDoctor'">
                                        <v-select v-model="item.disc_id" :options="DiscOptions" @input="vChangedDisc($event, index)" :reduce="DiscOptions => DiscOptions.value" label="title" placeholder="-- Pilih Diskon --"></v-select>
                                        <b-input-group>
                                            <b-form-input style="width:100px" type="number" @input="vChanged($event, index)" :disabled="item.disc_id === 0 ? true : false" v-model="item.var_4" />
                                        </b-input-group>
                                    </template>
                                </b-td>
                                <b-td class="text-right">{{ item.transaction_from === 'itemsDoctor'|| item.transaction_from === 'itemsBlood'|| item.transaction_from === 'itemsTool' || item.transaction_from === 'itemsBed' || item.transaction_from === 'itemsNurse' ? (item.transaction_from === 'itemsBed' ? (item.var_1-item.var_4)*item.var_2  : item.var_3) : item.var_1 | currency }}</b-td>
                                <b-td :rowspan="item.transaction_from === 'itemsPres' ? 2 : 1" class="text-center">
                                    <input v-if="!item.transaction_id" type="checkbox" v-model="item.checked" @change="updateTotal" />
                                </b-td>
                            </b-tr>      
                            <b-tr v-if="item.transaction_from === 'itemsPres'" :class="item.transaction_id === item.id ? 'bg-info-200' : item.checked ? 'bg-warning-200' : '' ">
                                <td colspan="5">
                                    <b-table-simple responsive bordered>
                                        <b-thead class="bg-neutral-100 text-bold-14 text-center">
                                            <b-tr :class="item.checked ? 'bg-warning-500' : '' ">
                                                <b-th style="width: 42%;">Obat/Alkes</b-th>
                                                <b-th style="width: 120px;">Harga</b-th>
                                                <b-th style="width: 5px;">Qty</b-th>
                                                <b-th style="width: 50px;">Diskon</b-th>
                                                <b-th style="width: 120px;">Total</b-th>
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
                                                    <b-td>{{ pres_item.qty-(pres_item.is_retur_full === 1 && pres_item.pharmacy_catalog_id_retur ? 0 : pres_item.qty_retur) }}</b-td>
                                                    <b-td>0,00 </b-td>
                                                    <b-td>{{ pres_item.var_1*(pres_item.qty-(pres_item.is_retur_full === 1 && pres_item.pharmacy_catalog_id_retur ? 0 : pres_item.qty_retur)) | currency }}</b-td>
                                                </b-tr>
                                                <b-tr v-if="pres_item.qty_retur > 0 || pres_item.pharmacy_catalog_id_retur" :class="pres_item.qty_retur > 0 || pres_item.pharmacy_catalog_id_retur ? 'bg-danger-500' : '' ">
                                                    <b-td class="text-left">
                                                        <div class="row">
                                                            <div class="col-lg">{{pres_item.pharmacy_catalog_name}}</div>
                                                        </div>
                                                    </b-td>
                                                    <b-td>{{ pres_item.var_1 | currency }}</b-td>
                                                    <b-td>{{ (pres_item.is_retur_full === 1 && pres_item.pharmacy_catalog_id_retur ? 0 : pres_item.qty_retur) }}</b-td>
                                                    <b-td>0,00 </b-td>
                                                    <b-td>{{ pres_item.var_1*((pres_item.is_retur_full === 1 && pres_item.pharmacy_catalog_id_retur ? 0 : pres_item.qty_retur)) | currency }}</b-td>
                                                </b-tr>
                                            </template>
                                        </b-tbody>
                                    </b-table-simple>
                                </td>
                            </b-tr>
                        </template>
                        <b-tr class="text-right font-weight-bold">
                            <b-td colspan="4">Total Item yang Dipilih</b-td>
                            <b-td colspan="3">{{toInvoice.grand_total | currency}}</b-td>
                        </b-tr>
                        <b-tr class="text-right font-weight-bold">
                            <!-- <b-td colspan="4">Diskon</b-td>
                            <b-td colspan="3">
                            <b-input-group>
                                <b-form-input style="width:100px" type="number" @change="vChanged($event, index)" v-model="toInvoice.var_4" />
                            </b-input-group></b-td> -->
                        </b-tr>
                        <b-tr class="text-right font-weight-bold">
                            <b-td colspan="4">Total</b-td>
                            <b-td colspan="3">{{toInvoice.grand_total | currency}}</b-td>
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
                    <!-- <div class="col-lg-1">
                        <div class="row">
                            <div class="col-lg px-lg-2 my-1 my-lg-0">
                                <router-link :to="{ name: 'PatientPaymentEklaimView' }">
                                    <b-button pill variant="outline-indigo" size="sm" class="w-100">
                                        <b-icon icon="file-earmark" /> E-Klaim
                                    </b-button>
                                </router-link>
                            </div>
                            <div class="col-lg px-lg-2 my-1 my-lg-0">
                                <router-link :to="{ name: 'PatientPaymentKwitansiPreviewView' }" target="_blank">
                                    <b-button pill variant="success-600" size="sm" class="w-100">
                                        <b-icon icon="eye" /> Preview Kwitansi
                                    </b-button>
                                </router-link>
                            </div>
                            <div class="col-lg px-lg-2 my-1 my-lg-0">
                                <router-link :to="{ name: 'PatientPaymentBillPreviewDotView' }" target="_blank">
                                    <b-button pill variant="success-600" size="sm" class="w-100">
                                        <b-icon icon="eye" /> Preview Tagihan
                                    </b-button>
                                </router-link>
                            </div>
                        </div>
                    </div> -->
                </div>

            </section>
            <section v-else-if="active.tab === 'tab2'">
                <b-table small bordered responsive head-variant="light" :fields="invoiceFields" :items="invoiceItems"
                    class="mt-20 text-center" style="vertical-align: middle;">
                    <template #cell(no)="data">
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
                            <router-link :to="{ path: '/payment/repayment/'+ row.item.id}" v-if="row.item.payment_id === null">
                                <b-button size="sm" variant="orange-500" class="m-1"><b-icon icon="pencil" /> Pelunasan</b-button>
                            </router-link>
                            <b-button size="sm" @click="deleteInvoice(row.item.id)" variant="danger-500" class="m-1" v-if="row.item.payment_id === null"> <b-icon icon="x" /> Batal Invoice</b-button>
                            <!-- <b-button size="sm" variant="success-600" class="m-1"><b-icon icon="check" />
                                Verifikasi</b-button> -->
                            <!-- <b-button size="sm" variant="info-800" class="m-1"><b-icon icon="printer" /> Cetak Invoice
                                Kop</b-button> -->
                            <router-link :to="{ path: '/payment/invoice/'+ row.item.id}" target="_blank" >
                                <b-button size="sm" variant="info-800" class="m-1"><b-icon icon="printer" /> Cetak Invoice</b-button>
                            </router-link>
                            <router-link :to="{ path: '/payment/kwitansi/'+ row.item.id}" target="_blank" v-if="row.item.payment_id !== null">
                                <b-button size="sm" variant="success-600" class="m-1"><b-icon icon="printer" /> Kwitansi</b-button>
                            </router-link>
                            <b-button size="sm" @click="deletePayment(row.item.id)" variant="danger-500" class="m-1" v-if="row.item.payment_id !== null"> <b-icon icon="x" /> Batal Pembayaran</b-button>
                            <b-button size="sm" @click="downloadReports(row.item.id)" :disabled="disabled_export" variant="success-600" class="m-1"><b-icon icon="journal-text" /> Export
                                Excel</b-button>
                        </div>
                    </template>
                </b-table>
            </section>
            <section v-else-if="active.tab === 'tab4'">
                <p>Obat Retur</p>
                <b-table-simple responsive bordered>
                    <b-thead class="bg-neutral-100 text-bold-14 text-center">
                        <b-tr>
                            <b-th style="width: 42%;">Obat/Alkes</b-th>
                            <b-th style="width: 120px;">Harga</b-th>
                                <b-th style="width: 5px;">Qty</b-th>
                                <b-th style="width: 50px;">Diskon</b-th>
                                <b-th style="width: 120px;">Total</b-th>
                            </b-tr>
                        </b-thead>
                        <template v-for="(item, index) in combinedItems">
                            <template v-for="(pres_item,index) in item.form_prescription" v-if="item.transaction_from === 'itemsPres' && pres_item.qty_retur > 0 || pres_item.pharmacy_catalog_id_retur">
                                <b-tr :class="pres_item.qty_retur > 0 || pres_item.pharmacy_catalog_id_retur ? 'bg-danger-500' : '' ">
                                    <b-td class="text-left"> {{pres_item.pharmacy_catalog_name}} </b-td>
                                    <b-td>{{ pres_item.var_1 | currency }}</b-td>
                                    <b-td>{{ (pres_item.is_retur_full === 1 && pres_item.pharmacy_catalog_id_retur ? 0 : pres_item.qty_retur) }}</b-td>
                                    <b-td>0,00 </b-td>
                                    <b-td>{{ pres_item.var_1*((pres_item.is_retur_full === 1 && pres_item.pharmacy_catalog_id_retur ? 0 : pres_item.qty_retur)) | currency }}</b-td>
                                </b-tr>
                            </template>
                        </template>
                    </b-table-simple>
            </section>
            <div v-else>
                <div class="d-flex justify-content-lg-end">
                    <router-link :to="{ path: '/payment/detail/add-dp/'+ id}" >
                        <b-button size="sm" variant="info-600" class="px-3">
                            <b-icon icon="plus" /> Tambah Deposit
                        </b-button>
                    </router-link>
                </div>
                <b-table small bordered responsive head-variant="light" :fields="dpFields" :items="dpItems" class="mt-20 text-center" style="vertical-align: middle;">
                    <template #cell(var_1)="data">
                        <div class="px-3" style="min-width: 200px;">
                            <p class="text-regular-14 text-right">{{ data.value | currency }}</p>
                        </div>
                    </template>
                    <template #cell(time)="data">
                        <div class="px-3" style="min-width: 200px;">
                            <span class="text-regular-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(date)="data">
                        <div class="px-3" style="min-width: 200px;">
                            <span class="text-regular-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(action)="row">
                        <div class="px-3" style="min-width: 280px;">
                            <router-link :to="{ path: '/payment/kwitansi/dp/'+ row.item.id}" target="_blank" >
                                <b-button size="sm" variant="info-800" class="m-1"><b-icon icon="printer" /> Cetak Kwitansi</b-button>
                            </router-link>
                        </div>
                    </template>
                </b-table>
            </div>
        </div>
        <b-modal v-model="active.modal_bed" size="md" title="Edit Tarif Kamar" hide-footer no-close-on-backdrop>
            <b-form-group label="Tarif" label-cols-sm="2" class="mt-12">
                <v-select v-model="form_bed.costing_id" aria-readonly="true" :options="OptionsCosting" :reduce="(OptionsCosting) => OptionsCosting.value" label="title" placeholder="-- Pilih --"></v-select>
            </b-form-group>
            <div class="modal-footer d-flex justify-content-end mt-24">
                <b-button :disabled="isDisabledBed" @click="validationBed" variant="primary">Update</b-button>
            </div>
        </b-modal>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import moment from 'moment'
import vSelect from 'vue-select'
import ExcelJS from 'exceljs';

export default {
    components: {
        vSelect 
    },
    data() {
        return {
            isDisabledBed:false,
            invoiceItems:[],
            form_bed:{
                id:'',
                costing_id:'',
                var_1:''
            },
            toInvoice: {
                registration_id: '',  
                grand_total: 0,
            },
            DiscOptions:[
                { value: 1, title: '%'},
                { value: 2, title: 'Nominal'}
            ],
            OptionsCosting: [
            { value: '408b5e6c-daba-400a-8615-b5b326c0fcdd', title: 'Kamar Perawatan Kelas 1' },
            { value: '45f71e22-4f51-4ca6-8313-e83213332086', title: 'Kamar Perawatan Kelas 3' },
            { value: '6515900a-cc1d-4e16-80f7-7672d79d7c7c', title: 'Kamar Perawatan Kelas 2' },
            { value: '892aef6d-f765-403d-971d-067720ecb07c', title: 'Kamar Perawatan Kelas President Suit' },
            { value: 'bb0988f7-72a4-4e0e-ab07-8202949a7aad', title: 'Kamar Perawatan Kelas VIP' },
            { value: '3abc776f-6528-4ec7-9595-6d05135b30fd', title: 'Sewa Kamar Ibu dan Anak President Suite'},
            { value: 'cb0d2556-737f-4e32-8607-a033b5fbd629', title: 'Sewa Kamar Ibu dan Anak VIP'},
            { value: 'cbb07b10-e55e-4958-91df-4407f6323f33', title: 'Sewa Kamar Ibu dan Anak Kelas 2'},
            { value: 'd110bf9c-9f0d-48df-a0b2-22274f0fb024', title: 'Sewa Kamar Ibu dan Anak Kelas 1'},
            { value: 'f1caa977-85cf-4b06-965b-51d12bf1ed01', title: 'Sewa Kamar Ibu dan Anak Kelas 3'},
            ],
            checktransaction:[],
            combinedItems:[],
            form:{
                transaction:{
                    allSelected: false,
                    registration_id:0,
                    amount_remain:0,
                    amount_total_selected:0,
                    amount_total:0,
                    administration:[],
                    nurse:[],
                    lab:[],
                    rad:[],
                    pres:[],
                    phy:[],
                    doctor:[],
                    tool:[],
                    blood:[],
                    bed:[],
                    bhp:[]
                }
            },
            itemsPres:[],
            regData:{},
            open_modal: {
                invoice: false,
                details: false,
            },
            active: {
                tab: 'tab1',
                modal_bed:false
            },
            purpose:'',
            handle:false,

            disabled_export:false,
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
    watch: {
        'form.transaction.nurse' (obj) {
            this.form.transaction.amount_total_selected = 0
            this.form.transaction.amount_total = 0
            let total = 0
            for (let i = 0; i < obj.length; i++) {
                this.form.transaction.amount_total_selected += obj[i].var_3
                this.form.transaction.amount_total += obj[i].var_3
            }
            total = this.form.transaction.amount_total_selected
            // total = this.form.transaction.amount_remain - total 
        },
        'form.transaction.administration' (obj) {
            this.form.transaction.amount_total_selected = 0
            this.form.transaction.amount_total = 0
            let total = 0
            for (let i = 0; i < obj.length; i++) {
                this.form.transaction.amount_total_selected += obj[i].var_1
                this.form.transaction.amount_total += obj[i].var_1
            }
            total = this.form.transaction.amount_total_selected
            // total = this.form.transaction.amount_remain - total 
        },
        'form.transaction.doctor' (obj) {
            // this.form.transaction.amount_total_selected = 0
            // this.form.transaction.amount_total = 0
            let total = 0
            for (let i = 0; i < obj.length; i++) {
                this.form.transaction.amount_total_selected += obj[i].var_3
                this.form.transaction.amount_total += obj[i].var_3
            }
            total = this.form.transaction.amount_total_selected
            // total = this.form.transaction.amount_remain - total 
        },
        'form.transaction.lab' (obj) {
            // this.form.transaction.amount_total_selected = 0
            // this.form.transaction.amount_total = 0
            let total = 0
            for (let i = 0; i < obj.length; i++) {
                this.form.transaction.amount_total_selected += obj[i].var_1
                this.form.transaction.amount_total += obj[i].var_1
            }
            total = this.form.transaction.amount_total_selected
            // total = this.form.transaction.amount_remain - total 
        },
        'form.transaction.rad' (obj) {
            // this.form.transaction.amount_total_selected = 0
            // this.form.transaction.amount_total = 0
            let total = 0
            for (let i = 0; i < obj.length; i++) {
                this.form.transaction.amount_total_selected += obj[i].var_1
                this.form.transaction.amount_total += obj[i].var_1
            }
            total = this.form.transaction.amount_total_selected
            // total = this.form.transaction.amount_remain - total 
        },
        'form.transaction.pres' (obj) {
            // this.form.transaction.amount_total_selected = 0
            // this.form.transaction.amount_total = 0
            let total = 0
            for (let i = 0; i < obj.length; i++) {
                this.form.transaction.amount_total_selected += obj[i].var_1
                this.form.transaction.amount_total += obj[i].var_1
            }
            total = this.form.transaction.amount_total_selected
            // total = this.form.transaction.amount_remain - total 
        },
        'form.transaction.phy' (obj) {
            // this.form.transaction.amount_total_selected = 0
            // this.form.transaction.amount_total = 0
            let total = 0
            for (let i = 0; i < obj.length; i++) {
                this.form.transaction.amount_total_selected += obj[i].var_1
                this.form.transaction.amount_total += obj[i].var_1
            }
            total = this.form.transaction.amount_total_selected
            // total = this.form.transaction.amount_remain - total 
        },
        'form.transaction.tool' (obj) {
            // this.form.transaction.amount_total_selected = 0
            // this.form.transaction.amount_total = 0
            let total = 0
            for (let i = 0; i < obj.length; i++) {
                this.form.transaction.amount_total_selected += obj[i].var_1
                this.form.transaction.amount_total += obj[i].var_1
            }
            total = this.form.transaction.amount_total_selected
            // total = this.form.transaction.amount_remain - total 
        },
        'form.transaction.blood' (obj) {
            // this.form.transaction.amount_total_selected = 0
            // this.form.transaction.amount_total = 0
            // console.log(obj,'blood')
            let total = 0
            for (let i = 0; i < obj.length; i++) {
                // this.form.transaction.amount_total_selected += obj[i].var_1
                // this.form.transaction.amount_total += obj[i].var_1
                this.form.transaction.amount_total_selected += obj[i].var_3
                this.form.transaction.amount_total += obj[i].var_3
            }
            total = this.form.transaction.amount_total_selected
            // total = this.form.transaction.amount_remain - total 
        },
        'form.transaction.bhp' (obj) {
            // this.form.transaction.amount_total_selected = 0
            // this.form.transaction.amount_total = 0
            let total = 0
            for (let i = 0; i < obj.length; i++) {
                this.form.transaction.amount_total_selected += obj[i].var_1
                this.form.transaction.amount_total += obj[i].var_1
            }
            total = this.form.transaction.amount_total_selected
            // total = this.form.transaction.amount_remain - total 
        },
        'form.transaction.bed' (obj) {
            // this.form.transaction.amount_total_selected = 0
            // this.form.transaction.amount_total = 0
            let total = 0
            for (let i = 0; i < obj.length; i++) {
                this.form.transaction.amount_total_selected += obj[i].var_1
                this.form.transaction.amount_total += obj[i].var_1
            }
            total = this.form.transaction.amount_total_selected
            // total = this.form.transaction.amount_remain - total 
        }
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'IDR' }).format(value)
        }
    },
    computed: {
        ...mapState({ 
            // tab 1
            itemsNurse: state => state.nurse.itemstransaction, 
            itemsAdministration: state => state.administration.itemstransaction, 
            itemsDoctor: state => state.soap.itemstransaction, 
            itemsLab: state => state.laboratory.itemstransaction, 
            itemsRad: state => state.radiology.itemstransaction, 
            // itemsPres: state => state.prescription.itemstransaction, 
            itemsPhy: state => state.physiotherapy.itemstransaction, 
            itemsTool: state => state.tool.itemstransaction, 
            itemsBlood: state => state.blood.itemstransaction, 
            itemsBhp: state => state.bhp.itemstransaction, 
            itemsBed: state => state.bed.itemstransaction, 
            
            // tab 2
            invoiceFields: state => state.payment.invoice.fields, 
            // tab 3
            dpItems: state => state.payment.dp.items, 
            dpFields: state => state.payment.dp.fields, 

            // export excel
            TypeCostingOptions: state => state.helper.options.typecostingc
        }),

        combinedItemx() {
            return [
                ...(this.itemsNurse.length ? this.itemsNurse.map(item => ({
                    ...item,
                    checked: false,
                    transaction_from: 'itemsNurse',
                    var_3: (item.var_2 || 0) * (item.var_1 || 0) - (item.var_4 || 0)
                })) : []),
                ...(this.itemsAdministration.length ? this.itemsAdministration.map(item => ({ ...item, checked: false, transaction_from: 'itemsAdministration' })) : []),
                ...(this.itemsDoctor.length ? this.itemsDoctor.map(item => ({
                    ...item,
                    checked: false,
                    transaction_from: 'itemsDoctor',
                    var_3: (item.var_1 || 0) * (item.var_2 || 0) - (item.var_4 || 0)
                })) : []),
                ...(this.itemsLab.length ? this.itemsLab.map(item => ({ ...item, checked: false, transaction_from: 'itemsLab' })) : []),
                ...(this.itemsRad.length ? this.itemsRad.map(item => ({ ...item, checked: false, transaction_from: 'itemsRad' })) : []),
                ...(this.itemsPres.length ? this.itemsPres.map(item => ({ ...item, checked: false, transaction_from: 'itemsPres' })) : []),
                ...(this.itemsPhy.length ? this.itemsPhy.map(item => ({ ...item, checked: false, transaction_from: 'itemsPhy' })) : []),
                ...(this.itemsTool.length ? this.itemsTool.map(item => ({
                    ...item,
                    checked: false,
                    transaction_from: 'itemsTool',
                    var_3: (item.var_1 || 0) * (item.var_2 || 0) - (item.var_4 || 0)
                })) : []),
                ...(this.itemsBlood.length ? this.itemsBlood.map(item => ({
                    ...item,
                    checked: false,
                    transaction_from: 'itemsBlood',
                    var_3: (item.var_1 || 0) * (item.var_2 || 0) - (item.var_4 || 0)
                })) : []),
                ...(this.itemsBhp.length ? this.itemsBhp.map(item => ({ ...item, checked: false, transaction_from: 'itemsBhp' })) : []),
                ...(this.itemsBed.length ? this.itemsBed.map(item => ({ ...item, checked: false, transaction_from: 'itemsBed' })) : []),
            ];
        },

    },
    methods: {
        async validationBed() {
            // this.form_checkout.date_checkout = `${this.temp.date || moment(new Date()).format('YYYY-MM-DD')} ${this.temp.time || moment(new Date()).format('HH:mm')}`;
            // const formData = {
            //     id: this.regData.registration_destination_id,
            //     date_checkout: this.form_checkout.date_checkout,
            //     date_control: this.form_checkout.date_control,
            // }

            try {
                this.active.isDisabledBed = true
                await this.$store.dispatch('bed/formPostPutUpdate', this.form_bed)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_bed = false
                        location.reload()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.isDisabledBed = false
            }   
        },
        onModalBed(data){
            this.active.modal_bed = !this.active.modal_bed
            if (this.active.modal_bed == true) {
                this.form_bed.id = data.id
                this.form_bed.costing_id = data.costing_id
                this.form_bed.var_1 = data.var_1
            } else {
                this.form_bed.id = ''
                this.form_bed.costing_id = ''
                this.form_bed.var_1 = 1
            }
        },
        vChangedDisc($event, index){
            let disc = $event
            console.log(this.combinedItems[index],'vChangedDisc')
            this.combinedItems[index].disc_id = disc
            this.toInvoice.grand_total = this.combinedItems.reduce((sum, item) => {
                if (item.transaction_from == 'itemsDoctor') {
                    return sum + item.var_3;
                }
                if (item.transaction_from == 'itemsBlood') {
                    return sum + item.var_3;
                }
                return item.checked ? sum + item.var_1 : sum;
            }, 0);
            this.toInvoice.grand_total = 0
        },
        vChanged($event, index){
            let disc = $event
            if (this.combinedItems[index].disc_id === 1) {
                let var_1 = this.combinedItems[index].var_1
                let var_2 = this.combinedItems[index].var_2
                this.combinedItems[index].var_4 = disc
                this.combinedItems[index].var_3 = (var_1-(var_1*disc)/100)*var_2
            } else if (this.combinedItems[index].disc_id === 2) {
                let var_1 = this.combinedItems[index].var_1
                let var_2 = this.combinedItems[index].var_2
                this.combinedItems[index].var_4 = disc
                this.combinedItems[index].var_3 = (var_1-disc)*var_2
            }
            this.toInvoice.grand_total = this.combinedItems.reduce((sum, item) => {
                if (item.transaction_from == 'itemsDoctor') {
                    return sum + item.var_3;
                }
                if (item.transaction_from == 'itemsBlood') {
                    return sum + item.var_3;
                }
                return item.checked ? sum + item.var_1 : sum;
            }, 0);
            this.toInvoice.grand_total = 0
        },
        updateTotal() {
            this.toInvoice.grand_total = this.combinedItems.reduce((sum, item) => {
                // console.log('transaction from:', item.transaction_from )
                if (item.transaction_from == 'itemsNurse') {
                    return item.checked ? sum + item.var_3 : sum;
                } else if (item.transaction_from == 'itemsDoctor') {
                    return item.checked ? sum + item.var_3 : sum;
                } else if (item.transaction_from == 'itemsBlood') {
                    return item.checked ? sum + item.var_3 : sum;
                } else if (item.transaction_from == 'itemsBed' ) {
                    return item.checked ? sum + item.var_1*item.var_2 : sum;
                }
                return item.checked ? sum + item.var_1 : sum;
            }, 0);
        },
        toggleSelectAll(event) {
            const isChecked = event.target.checked;
            this.combinedItems.forEach(item => {
                if (!item.transaction_id) {
                    item.checked = isChecked;
                }
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
            
            if (this.handle === false){
                this.handleSubmit()
            }
        },
        handleSubmit () {
            this.handle = true

            let formData = {}
            formData = {
                toInvoice: this.toInvoice,
                transaction: this.combinedItems,
            }

            this.$store.dispatch('payment/formPostPutInvoice', formData)
            .then(res => {
                this.open_modal.invoice = false
                this.handle = false
                this.getData(this.$route.params.id)
                this.getTransactionData()
                this.notification('Success','CheckIcon','success','You have successfully')

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
                        this.getData(this.$route.params.id)
                        this.getTransactionData()
                        this.active.tab = 'tab2'
                        this.notification('Success','CheckIcon','success','You have successfully')
                    },
                    error => {
                        this.$store.dispatch('payment/getByAllInvoice', this.$route.params.id).then(res => {
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
                        this.getData(this.$route.params.id)
                        this.getTransactionData()
                        this.active.tab = 'tab2'
                        this.notification('Success','CheckIcon','success','You have successfully')
                    },
                    error => {
                        this.$store.dispatch('payment/getByAllInvoice', this.$route.params.id).then(res => {
                            this.invoiceItems = res
                        })
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.log(error);
                    }) 
                }
            })
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        changeTab(tab) {
            this.active.tab = tab
        },
        parsed_age(input_date) {
            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();

            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = currentDate.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = currentDate.diff(birthDate, 'days');

            return `${years} th ${months} bln ${days} hr`;
        },
        getData (id) {
            this.$store.dispatch('registration/getByRegistrationId', id).then(data => {
                this.regData = data[0]
                // console.log('regData: ', this.regData);
                this.toInvoice.registration_id = data[0].registration_id
                // this.form.transaction.registration_id = data[0].registration_id
                // this.form.transaction.amount_remain = 0
                // for (let i = 0; i < this.itemsAdministration.length; i++) {
                //     this.form.transaction.amount_remain += this.itemsAdministration[i].var_1
                // }
                // for (let i = 0; i < this.itemsNurse.length; i++) {
                //     this.form.transaction.amount_remain += this.itemsNurse[i].var_3
                // }
                // for (let i = 0; i < this.itemsDoctor.length; i++) {
                //     this.form.transaction.amount_remain += this.itemsDoctor[i].var_1
                // }
                // for (let i = 0; i < this.itemsLab.length; i++) {
                //     this.form.transaction.amount_remain += this.itemsLab[i].var_1
                // }
                // for (let i = 0; i < this.itemsRad.length; i++) {
                //     this.form.transaction.amount_remain += this.itemsRad[i].var_1
                // }
                // for (let i = 0; i < this.itemsPhy.length; i++) {
                //     this.form.transaction.amount_remain += this.itemsPhy[i].var_1
                // }
                // for (let i = 0; i < this.itemsPres.length; i++) {
                //     this.form.transaction.amount_remain += this.itemsPres[i].var_1
                // }
                // for (let i = 0; i < this.itemsTool.length; i++) {
                //     this.form.transaction.amount_remain += this.itemsTool[i].var_1
                // }
                // for (let i = 0; i < this.itemsBlood.length; i++) {
                //     this.form.transaction.amount_remain += this.itemsBlood[i].var_1
                // }
                // for (let i = 0; i < this.itemsBhp.length; i++) {
                //     this.form.transaction.amount_remain += this.itemsBhp[i].var_1
                // }
            })
        },
        async getTransactionData () {
            // ITEM TRANSACTION
            await this.$store.dispatch('payment/getByAllInvoice', this.$route.params.id).then(async res => {
                this.invoiceItems = res
            })
            await this.$store.dispatch('payment/getByAllDP', this.$route.params.id)
            await this.$store.dispatch('administration/getByRegistrationIdTransaction', this.$route.params.id)
            await this.$store.dispatch('nurse/getByRegistrationIdTransaction', this.$route.params.id)
            await this.$store.dispatch('soap/getByRegistrationIdTransaction', this.$route.params.id)
            await this.$store.dispatch('laboratory/getByRegistrationIdTransaction', this.$route.params.id)
            await this.$store.dispatch('radiology/getByRegistrationIdTransaction', this.$route.params.id)
            await this.$store.dispatch('prescription/getByRegistrationIdTransaction', this.$route.params.id).then(async res => {
                this.itemsPres = res.sort((a, b) => new Date(a.date_added) - new Date(b.date_added))
            })
            await this.$store.dispatch('physiotherapy/getByRegistrationIdTransaction', this.$route.params.id)
            await this.$store.dispatch('tool/getByRegistrationIdTransaction', this.$route.params.id)
            await this.$store.dispatch('bhp/getByRegistrationIdTransaction', this.$route.params.id)
            await this.$store.dispatch('bed/getByRegistrationIdTransaction', this.$route.params.id)
            await this.$store.dispatch('blood/getByRegistrationIdTransaction', this.$route.params.id)
            // ITEM TRANSACTION
            
            // CHECK TRANSACTION
            // setTimeout(() => {
                this.$store.dispatch('payment/getCheckTransaction', this.$route.params.id).then(res => {
                    if (res){
                        this.checktransaction = res
                        let temp = []
                        this.combinedItemx.forEach(x => {
                            let merged = {}
                            this.checktransaction.forEach(y => {
                                // if (x.id === y.transaction_id) {
                                    merged = {
                                        transaction_id: y.transaction_id
                                    }
                                // }
                            })
                            temp.push({ ...x, ...merged })
                        })
                        this.combinedItems = temp
                    } else {
                        this.combinedItems = this.combinedItemx
                    }
                })
            // }, 8000);
            // CHECK TRANSACTION
        },
        async downloadReports(id) {
            try {
                this.disabled_export = true
                await this.$store.dispatch('payment/getByInvoiceId', id).then(async data => {
                    if (data.length > 0) {
                        this.invoices = data[0]
                        await this.mappingArray(this.invoices.transactions)
                        // console.log('this.invoices: ', this.invoices);
                        
                        await this.exportToExcel()

                    }
                }, 
                error => {
                    console.error('Error while GET payment/getByInvoiceId at: ', error);
                })
            } catch (error) {
                console.error('error at: ', error);
            } finally {
                this.disabled_export = false
            }
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
        },

        async exportToExcel() {
            const workbook = new ExcelJS.Workbook();
            const worksheet = workbook.addWorksheet("Invoice");

            // ----------- HEADER ----------
            worksheet.mergeCells('A1:N1');

            worksheet.getCell('A2').value = "No Nota";
            worksheet.getCell('C2').value = ":";
            worksheet.getCell('D2').value = this.invoices.code;

            worksheet.getCell('A3').value = "No RM";
            worksheet.getCell('C3').value = ":";
            worksheet.getCell('D3').value = this.regData.norm;

            worksheet.getCell('H3').value = "Cara Bayar";
            worksheet.getCell('I3').value = ":";
            if(this.regData.registration_insurance){
                let caraBayar = ""
                this.regData.registration_insurance.forEach(list => {
                    caraBayar = `${list.insurance_name} - ${list.company_name}`;
                    if(list.is_cob == 1){
                        caraBayar += " (COB)"
                    }
                    worksheet.getCell('J3').value = caraBayar;
                })
            }

            worksheet.getCell('L3').value = "TMRS";
            worksheet.getCell('M3').value = ":";
            worksheet.getCell('N3').value = this.regData.register_date;
            
            worksheet.getCell('A4').value = "Nama Pasien";
            worksheet.getCell('C4').value = ":";
            worksheet.getCell('D4').value = this.regData.name;

            worksheet.getCell('H4').value = "Ruangan/Poli";
            worksheet.getCell('I4').value = ":";
            worksheet.getCell('J4').value = this.regData.health_facility_item_name;

            worksheet.getCell('L4').value = "TKRS";
            worksheet.getCell('M4').value = ":";
            if(this.regData.checkout_date){
                worksheet.getCell('N4').value = this.regData.checkout_date;
            } else {
                if(this.regData.health_facility_id === '3cb626f5-9148-4fb1-8663-3d7f028b01a6' || 
                this.regData.health_facility_id === '00e8fed1-a85d-4f42-96dd-bfff63086945'){
                    worksheet.getCell('N4').value = this.regData.register_date;
                }
            }

            worksheet.getCell('A5').value = "Alamat";
            worksheet.getCell('C5').value = ":";
            worksheet.getCell('D5').value = this.regData.address;

            worksheet.getCell('H5').value = "Kelas";
            worksheet.getCell('I5').value = ":";
            if(this.regData.registration_insurance){
                let kelas = ""
                this.regData.registration_insurance.forEach(list => {
                    kelas = `${list.service_class_name}`;
                    if(list.is_cob == 1){
                        kelas += " (COB)"
                    }
                    worksheet.getCell('J5').value = kelas;
                })
            }

            worksheet.getCell('A6').value = "NIK";
            worksheet.getCell('C6').value = ":";
            worksheet.getCell('D6').value = `'${this.regData.idcard}`;

            worksheet.getCell('A7').value = "Dokter";
            worksheet.getCell('C7').value = ":";
            worksheet.getCell('D7').value = this.regData.doctor_in_charge_name;
          

            worksheet.mergeCells('A8:N8');

            // === Format colon cells to center ===
            ['C2','C3','C4','C5','C6','C7','I3','I4','I5','M3','M4'].forEach(cell => {
                worksheet.getCell(cell).alignment = { horizontal: 'center' };
            });

            // Define the range that should have the outside border
            const startRow = 1;
            const endRow = 8;
            const startCol = 1;  // A
            const endCol = 14;  // N

            const borderStyle = { style: 'thin' };

            for (let row = startRow; row <= endRow; row++) {
            for (let col = startCol; col <= endCol; col++) {
                const cell = worksheet.getCell(row, col);

                // Add only outside borders
                if (row === startRow) {
                cell.border = { ...cell.border, top: borderStyle };
                }
                if (row === endRow) {
                cell.border = { ...cell.border, bottom: borderStyle };
                }
                if (col === startCol) {
                cell.border = { ...cell.border, left: borderStyle };
                }
                if (col === endCol) {
                cell.border = { ...cell.border, right: borderStyle };
                }
            }
            }

            // ----------- DEFINE COLUMNS ----------
            worksheet.columns = [
                { key: 'tanggal', width: 15 }, 
                { key: 'tindakan', width: 40 },
                { key: 'col_h', width: 5 },  
                { key: 'col_i', width: 5 },     
                { key: 'qty', width: 8 },
                { key: 'harga', width: 15 },
                { key: 'col_j', width: 5 },      
                { key: 'diskon', width: 12 },
                { key: 'col_l', width: 5 },     
                { key: 'subtotal', width: 18 },
                { key: 'col_k', width: 5 },     
                { key: 'lokasi', width: 25 },
                { key: 'col_m', width: 5 },    
                { key: 'col_n', width: 5 },                   
            ];

            const headerRow = worksheet.addRow([
                'Tanggal',
                'Tindakan',
                '',
                '',
                'Qty',
                'Harga',
                '',
                'Diskon',
                '',
                'Subtotal',
                '',
                'Lokasi',
                '',
                '',
            ]);

            worksheet.mergeCells('B9:D9');
            worksheet.mergeCells('F9:G9');
            worksheet.mergeCells('H9:I9');
            worksheet.mergeCells('J9:K9');
            worksheet.mergeCells('L9:N9');

            // Style headers
            headerRow.eachCell((cell) => {
                cell.font = { bold: true };
                cell.alignment = { horizontal: 'center', vertical: 'middle' };
                cell.fill = {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: { argb: 'E2EFDA' }, // light green
                };
                cell.border = {
                    top: { style: 'thin' },
                    left: { style: 'thin' },
                    bottom: { style: 'thin' },
                    right: { style: 'thin' },
                };
            });

            // ----------- HELPER: Apply border to every row -----------
            const applyBorderToRow = (row) => {
                row.eachCell((cell) => {
                    cell.border = {
                        top: { style: 'thin' },
                        left: { style: 'thin' },
                        bottom: { style: 'thin' },
                        right: { style: 'thin' }
                    };
                });
            };

            // ========= TRANSAKSI 1 =========
            if (this.TypeCostingOptions && this.group_trans_1.length > 0) {
                let totalRow = worksheet.addRow([]);
                worksheet.mergeCells(`A${totalRow.number}:E${totalRow.number}`);
                worksheet.getCell(`A${totalRow.number}`).value = "Keperawatan";
                worksheet.mergeCells(`F${totalRow.number}:G${totalRow.number}`);
                worksheet.getCell(`F${totalRow.number}`).value = "";
                worksheet.mergeCells(`H${totalRow.number}:I${totalRow.number}`);
                worksheet.getCell(`H${totalRow.number}`).value = "";

                worksheet.mergeCells(`J${totalRow.number}:K${totalRow.number}`);
                worksheet.getCell(`J${totalRow.number}`).value = this.trans_1_total;
                worksheet.mergeCells(`L${totalRow.number}:N${totalRow.number}`);
                totalRow.getCell(14).value = "";

                totalRow.eachCell(cell => {
                    cell.font = { bold: true };
                    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FEF8D8' } };
                });
                applyBorderToRow(totalRow);

                this.group_trans_1.forEach(typec => {
                    let sectionRow = worksheet.addRow([]);
                    worksheet.mergeCells(`A${sectionRow.number}:E${sectionRow.number}`);
                    worksheet.getCell(`A${sectionRow.number}`).value = typec.title;
                    worksheet.getCell(`A${sectionRow.number}`).font = { bold: true };
                    worksheet.getCell(`A${sectionRow.number}`).alignment = { horizontal: 'left' };

                    worksheet.mergeCells(`F${sectionRow.number}:G${sectionRow.number}`);
                    worksheet.getCell(`F${sectionRow.number}`).value = "";
                    worksheet.mergeCells(`H${sectionRow.number}:I${sectionRow.number}`);
                    worksheet.getCell(`H${sectionRow.number}`).value = "";

                    worksheet.mergeCells(`J${sectionRow.number}:K${sectionRow.number}`);
                    worksheet.getCell(`J${sectionRow.number}`).value = "";
                    worksheet.getCell(`J${sectionRow.number}`).alignment = { horizontal: 'right' };

                    worksheet.mergeCells(`L${sectionRow.number}:N${sectionRow.number}`);
                    worksheet.getCell(`N${sectionRow.number}`).value = "";
                    
                    applyBorderToRow(sectionRow);
                    
                    this.trans_1.forEach(item => {
                        if (item.classification_three_type_id === typec.value) {
                            let row = worksheet.addRow([
                                item.date_added,
                                item.costing_name,
                                '',
                                '',
                                item.var_1,
                                item.var_2,
                                '',
                                0,
                                '',
                                item.var_1 * item.var_2,
                                '',
                                item.health_facility_item_name,
                                '',
                                '',
                            ]);
                            worksheet.getCell(`A${row.number}`).alignment = { horizontal: 'center' };
                            worksheet.mergeCells(`B${row.number}:D${row.number}`);
                            worksheet.getCell(`B${row.number}`).alignment = { horizontal: 'left' };
                            worksheet.getCell(`E${row.number}`).alignment = { horizontal: 'center' };
                            worksheet.mergeCells(`F${row.number}:G${row.number}`);
                            worksheet.getCell(`F${row.number}`).alignment = { horizontal: 'right' };
                            worksheet.mergeCells(`H${row.number}:I${row.number}`);
                            worksheet.getCell(`H${row.number}`).alignment = { horizontal: 'right' };
                            worksheet.mergeCells(`J${row.number}:K${row.number}`);
                            worksheet.getCell(`J${row.number}`).alignment = { horizontal: 'right' };
                            worksheet.mergeCells(`L${row.number}:N${row.number}`);
                            worksheet.getCell(`L${row.number}`).alignment = { horizontal: 'center' };
                            applyBorderToRow(row);
                        }
                    });
                });
            }

            // ========= TRANSAKSI 5 =========
            if (this.trans_5.length > 0) {
                let totalRow = worksheet.addRow([]);
                worksheet.mergeCells(`A${totalRow.number}:E${totalRow.number}`);
                worksheet.getCell(`A${totalRow.number}`).value = "Pemakaian Alat";
                
                worksheet.mergeCells(`F${totalRow.number}:G${totalRow.number}`);
                worksheet.getCell(`F${totalRow.number}`).value = "";
                worksheet.mergeCells(`H${totalRow.number}:I${totalRow.number}`);
                worksheet.getCell(`H${totalRow.number}`).value = "";

                worksheet.mergeCells(`J${totalRow.number}:K${totalRow.number}`);
                worksheet.getCell(`J${totalRow.number}`).value = this.trans_5_total;
                worksheet.mergeCells(`L${totalRow.number}:N${totalRow.number}`);
                totalRow.getCell(14).value = "";
                totalRow.eachCell(cell => {
                    cell.font = { bold: true };
                    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FEF8D8' } };
                });
                applyBorderToRow(totalRow);

                this.trans_5.forEach(item => {
                    let row = worksheet.addRow([
                        item.date_added,
                        `${item.costing_name}\n${item.doctor_in_charge_name}`,
                        '',
                        '',
                        1,
                        item.var_1,
                        '',
                        0,
                        '',
                        item.var_1,
                        '',
                        item.health_facility_item_name,
                        '',
                        '',
                    ]);
                    worksheet.getCell(`A${row.number}`).alignment = { horizontal: 'center' };
                    worksheet.mergeCells(`B${row.number}:D${row.number}`);
                    worksheet.getCell(`B${row.number}`).alignment = { horizontal: 'left' };
                    worksheet.getCell(`E${row.number}`).alignment = { horizontal: 'center' };
                    worksheet.mergeCells(`F${row.number}:G${row.number}`);
                    worksheet.getCell(`F${row.number}`).alignment = { horizontal: 'right' };
                    worksheet.mergeCells(`H${row.number}:I${row.number}`);
                    worksheet.getCell(`H${row.number}`).alignment = { horizontal: 'right' };
                    worksheet.mergeCells(`J${row.number}:K${row.number}`);
                    worksheet.getCell(`J${row.number}`).alignment = { horizontal: 'right' };
                    worksheet.mergeCells(`L${row.number}:N${row.number}`);
                    worksheet.getCell(`L${row.number}`).alignment = { horizontal: 'center' };
                    applyBorderToRow(row);
                });

            }

            // ========= TRANSAKSI 7 =========
            if (this.trans_7.length > 0) {
                let totalRow = worksheet.addRow([]);
                worksheet.mergeCells(`A${totalRow.number}:E${totalRow.number}`);
                worksheet.getCell(`A${totalRow.number}`).value = "";
                worksheet.mergeCells(`F${totalRow.number}:G${totalRow.number}`);
                worksheet.getCell(`F${totalRow.number}`).value = "";
                worksheet.mergeCells(`H${totalRow.number}:I${totalRow.number}`);
                worksheet.getCell(`H${totalRow.number}`).value = "";
                worksheet.mergeCells(`J${totalRow.number}:K${totalRow.number}`);
                worksheet.getCell(`J${totalRow.number}`).value = this.trans_7_total;
                worksheet.mergeCells(`L${totalRow.number}:N${totalRow.number}`);
                totalRow.getCell(14).value = "";
                totalRow.eachCell(cell => {
                    cell.font = { bold: true };
                    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FEF8D8' } };
                });
                applyBorderToRow(totalRow)

                this.trans_7.forEach(item => {
                    let row = worksheet.addRow([
                        item.date_added,
                        `${item.costing_name}\n${item.doctor_in_charge_name}`,
                        '',
                        '',
                        item.var_2,
                        item.var_1,
                        '',
                        0,
                        '',
                        item.var_1 * item.var_2,
                        '',
                        item.health_facility_item_name,
                        '',
                        '',
                    ]);
                    worksheet.getCell(`A${row.number}`).alignment = { horizontal: 'center' };
                    worksheet.mergeCells(`B${row.number}:D${row.number}`);
                    worksheet.getCell(`B${row.number}`).alignment = { horizontal: 'left' };
                    worksheet.getCell(`E${row.number}`).alignment = { horizontal: 'center' };
                    worksheet.mergeCells(`F${row.number}:G${row.number}`);
                    worksheet.getCell(`F${row.number}`).alignment = { horizontal: 'right' };
                    worksheet.mergeCells(`H${row.number}:I${row.number}`);
                    worksheet.getCell(`H${row.number}`).alignment = { horizontal: 'right' };
                    worksheet.mergeCells(`J${row.number}:K${row.number}`);
                    worksheet.getCell(`J${row.number}`).alignment = { horizontal: 'right' };
                    worksheet.mergeCells(`L${row.number}:N${row.number}`);
                    worksheet.getCell(`L${row.number}`).alignment = { horizontal: 'center' };
                    applyBorderToRow(row)
                });
            }

            // ========= TRANSAKSI 2 =========
            // ---------------- TINDAKAN / KONSUL DOKTER (trans_2) ----------------
            if (this.TypeCostingOptions && this.group_trans_2.length > 0) {
                // Add the TOTAL row at the end
                let totalRow = worksheet.addRow([]);

                worksheet.mergeCells(`A${totalRow.number}:E${totalRow.number}`);
                worksheet.getCell(`A${totalRow.number}`).value = "Tindakan/Konsul Dokter";
                worksheet.getCell(`A${totalRow.number}`).alignment = { horizontal: 'left' };

                worksheet.mergeCells(`F${totalRow.number}:G${totalRow.number}`);
                worksheet.getCell(`F${totalRow.number}`).value = "";
                worksheet.mergeCells(`H${totalRow.number}:I${totalRow.number}`);
                worksheet.getCell(`H${totalRow.number}`).value = "";

                worksheet.mergeCells(`J${totalRow.number}:K${totalRow.number}`);
                worksheet.getCell(`J${totalRow.number}`).value = this.trans_2_total;
                worksheet.getCell(`J${totalRow.number}`).alignment = { horizontal: 'right' };

                worksheet.mergeCells(`L${totalRow.number}:N${totalRow.number}`);
                worksheet.getCell(`N${totalRow.number}`).value = "";

                totalRow.eachCell((cell) => {
                    cell.font = { bold: true };
                    cell.fill = {
                        type: 'pattern',
                        pattern: 'solid',
                        fgColor: { argb: 'FEF8D8' }, // same background as your HTML
                    };
                });

                applyBorderToRow(totalRow)

                this.group_trans_2.forEach((typec) => {
                    // Section title row
                    let sectionRow = worksheet.addRow([]);
                    worksheet.mergeCells(`A${sectionRow.number}:E${sectionRow.number}`);
                    worksheet.getCell(`A${sectionRow.number}`).value = typec.title;
                    worksheet.getCell(`A${sectionRow.number}`).font = { bold: true };
                    worksheet.getCell(`A${sectionRow.number}`).alignment = { horizontal: 'left' };

                    worksheet.mergeCells(`F${sectionRow.number}:G${sectionRow.number}`);
                    worksheet.getCell(`F${sectionRow.number}`).value = "";
                    worksheet.mergeCells(`H${sectionRow.number}:I${sectionRow.number}`);
                    worksheet.getCell(`H${sectionRow.number}`).value = "";

                    worksheet.mergeCells(`J${sectionRow.number}:K${sectionRow.number}`);
                    worksheet.getCell(`J${sectionRow.number}`).value = "";
                    worksheet.getCell(`J${sectionRow.number}`).alignment = { horizontal: 'right' };

                    worksheet.mergeCells(`L${sectionRow.number}:N${sectionRow.number}`);
                    worksheet.getCell(`N${sectionRow.number}`).value = "";
                    applyBorderToRow(sectionRow)

                    // Items inside this group
                    this.trans_2.forEach((item) => {
                        if (item.classification_three_type_id === typec.value) {
                            let row = worksheet.addRow([
                                item.date_added,                          // A
                                `${item.costing_name}\n${item.doctor_in_charge_name}`, // B
                                '',
                                '',
                                item.var_2,                               // C
                                item.var_1,                               // D
                                '',
                                item.var_4 ? item.var_4 : 0,              // E
                                '',
                                item.count_total,                         // F
                                '',
                                item.health_facility_item_name,           // G
                                '',
                                '',
                            ]);

                            // Style & alignment
                            worksheet.getCell(`A${row.number}`).alignment = { horizontal: 'center' };
                            worksheet.mergeCells(`B${row.number}:D${row.number}`);
                            worksheet.getCell(`B${row.number}`).alignment = { horizontal: 'left', wrapText: true };
                            worksheet.getCell(`E${row.number}`).alignment = { horizontal: 'center' };
                            worksheet.mergeCells(`F${row.number}:G${row.number}`);
                            worksheet.getCell(`F${row.number}`).alignment = { horizontal: 'right' };
                            worksheet.mergeCells(`H${row.number}:I${row.number}`);
                            worksheet.getCell(`H${row.number}`).alignment = { horizontal: 'right' };
                            worksheet.mergeCells(`J${row.number}:K${row.number}`);
                            worksheet.getCell(`J${row.number}`).alignment = { horizontal: 'right' };
                            worksheet.mergeCells(`L${row.number}:N${row.number}`);
                            worksheet.getCell(`L${row.number}`).alignment = { horizontal: 'center' };
                            applyBorderToRow(row)
                        }
                    });
                });
            }

            // ---------------- LABORATORIUM (trans_11) ----------------
            if (this.trans_11.length > 0) {
                // Total row
                let totalRow = worksheet.addRow([]);
                worksheet.mergeCells(`A${totalRow.number}:E${totalRow.number}`);
                worksheet.getCell(`A${totalRow.number}`).value = "LABORATORIUM";
                worksheet.mergeCells(`F${totalRow.number}:G${totalRow.number}`);
                worksheet.getCell(`F${totalRow.number}`).value = "";
                worksheet.mergeCells(`H${totalRow.number}:I${totalRow.number}`);
                worksheet.getCell(`H${totalRow.number}`).value = "";
                worksheet.mergeCells(`J${totalRow.number}:K${totalRow.number}`);
                worksheet.getCell(`J${totalRow.number}`).value = this.trans_11_total;
                worksheet.mergeCells(`L${totalRow.number}:N${totalRow.number}`);
                worksheet.getCell(`N${totalRow.number}`).value = "";

                totalRow.eachCell((cell) => {
                    cell.font = { bold: true };
                    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FEF8D8' } };
                });
                applyBorderToRow(totalRow)
                this.trans_11.forEach((item) => {
                    let row = worksheet.addRow([
                        item.date_added,        
                        item.costing_name, 
                        '',
                        '',     
                        1,                      
                        item.var_1,
                        '',             
                        1, 
                        '',                     
                        item.var_1,
                        '',             
                        "LABORATORIUM", 
                        '',
                        '',        
                    ]);

                    worksheet.getCell(`A${row.number}`).alignment = { horizontal: 'center' };
                    worksheet.mergeCells(`B${row.number}:D${row.number}`);
                    worksheet.getCell(`B${row.number}`).alignment = { horizontal: 'left' };
                    worksheet.getCell(`E${row.number}`).alignment = { horizontal: 'center' };
                    worksheet.mergeCells(`F${row.number}:G${row.number}`);
                    worksheet.getCell(`F${row.number}`).alignment = { horizontal: 'right' };
                    worksheet.mergeCells(`H${row.number}:I${row.number}`);
                    worksheet.getCell(`H${row.number}`).alignment = { horizontal: 'right' };
                    worksheet.mergeCells(`J${row.number}:K${row.number}`);
                    worksheet.getCell(`J${row.number}`).alignment = { horizontal: 'right' };
                    worksheet.mergeCells(`L${row.number}:N${row.number}`);
                    worksheet.getCell(`L${row.number}`).alignment = { horizontal: 'center' };
                    applyBorderToRow(row)
                });
            }

            // ---------------- RADIOLOGI (trans_12) ----------------
            if (this.trans_12.length > 0) {
                // Total row
                let totalRow = worksheet.addRow([]);
                worksheet.mergeCells(`A${totalRow.number}:E${totalRow.number}`);
                worksheet.getCell(`A${totalRow.number}`).value = "RADIOLOGI";
                worksheet.mergeCells(`F${totalRow.number}:G${totalRow.number}`);
                worksheet.getCell(`F${totalRow.number}`).value = "";
                worksheet.mergeCells(`H${totalRow.number}:I${totalRow.number}`);
                worksheet.getCell(`H${totalRow.number}`).value = "";
                worksheet.mergeCells(`J${totalRow.number}:K${totalRow.number}`);
                worksheet.getCell(`J${totalRow.number}`).value = this.trans_12_total;
                worksheet.mergeCells(`L${totalRow.number}:N${totalRow.number}`);
                worksheet.getCell(`N${totalRow.number}`).value = "";
                totalRow.eachCell((cell) => {
                    cell.font = { bold: true };
                    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FEF8D8' } };
                });
                applyBorderToRow(totalRow)
                this.trans_12.forEach((item) => {
                    let row = worksheet.addRow([
                        item.date_added,        
                        item.costing_name, 
                        '',
                        '',        
                        1,                      
                        item.var_1,  
                        '',              
                        0,    
                        '',                     
                        item.var_1,
                        '',                
                        "RADIOLOGI",
                        '',   
                        '',               
                    ]);
                    worksheet.getCell(`A${row.number}`).alignment = { horizontal: 'center' };
                    worksheet.mergeCells(`B${row.number}:D${row.number}`);
                    worksheet.getCell(`B${row.number}`).alignment = { horizontal: 'left' };
                    worksheet.getCell(`E${row.number}`).alignment = { horizontal: 'center' };
                    worksheet.mergeCells(`F${row.number}:G${row.number}`);
                    worksheet.getCell(`F${row.number}`).alignment = { horizontal: 'right' };
                    worksheet.mergeCells(`H${row.number}:I${row.number}`);
                    worksheet.getCell(`H${row.number}`).alignment = { horizontal: 'right' };
                    worksheet.mergeCells(`J${row.number}:K${row.number}`);
                    worksheet.getCell(`J${row.number}`).alignment = { horizontal: 'right' };
                    worksheet.mergeCells(`L${row.number}:N${row.number}`);
                    worksheet.getCell(`L${row.number}`).alignment = { horizontal: 'center' };
                    applyBorderToRow(row)
                });

            }

            // ---------------- (trans_15) ----------------
            if (this.trans_15.length > 0) {
                // Total row
                let totalRow = worksheet.addRow([]);
                worksheet.mergeCells(`A${totalRow.number}:E${totalRow.number}`);
                worksheet.getCell(`A${totalRow.number}`).value = "";
                worksheet.mergeCells(`F${totalRow.number}:G${totalRow.number}`);
                worksheet.getCell(`F${totalRow.number}`).value = "";
                worksheet.mergeCells(`H${totalRow.number}:I${totalRow.number}`);
                worksheet.getCell(`H${totalRow.number}`).value = "";
                worksheet.mergeCells(`J${totalRow.number}:K${totalRow.number}`);
                worksheet.getCell(`J${totalRow.number}`).value = this.trans_15_total;
                worksheet.mergeCells(`L${totalRow.number}:N${totalRow.number}`);
                worksheet.getCell(`N${totalRow.number}`).value = "";
                totalRow.eachCell((cell) => {
                    cell.font = { bold: true };
                    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FEF8D8' } };
                });
                applyBorderToRow(totalRow)
                this.trans_15.forEach((item) => {
                    let row = worksheet.addRow([
                        item.date_added,
                        item.costing_name,
                        '',
                        '',  
                        1,
                        item.var_1,
                        '',
                        0,
                        '',
                        item.var_1,
                        '',
                        this.regData.health_facility_item_name,
                        '',
                        '',
                    ]);
                    worksheet.getCell(`A${row.number}`).alignment = { horizontal: 'center' };
                    worksheet.mergeCells(`B${row.number}:D${row.number}`);
                    worksheet.getCell(`B${row.number}`).alignment = { horizontal: 'left' };
                    worksheet.getCell(`E${row.number}`).alignment = { horizontal: 'center' };
                    worksheet.mergeCells(`F${row.number}:G${row.number}`);
                    worksheet.getCell(`F${row.number}`).alignment = { horizontal: 'right' };
                    worksheet.mergeCells(`H${row.number}:I${row.number}`);
                    worksheet.getCell(`H${row.number}`).alignment = { horizontal: 'right' };
                    worksheet.mergeCells(`J${row.number}:K${row.number}`);
                    worksheet.getCell(`J${row.number}`).alignment = { horizontal: 'right' };
                    worksheet.mergeCells(`L${row.number}:N${row.number}`);
                    worksheet.getCell(`L${row.number}`).alignment = { horizontal: 'center' };
                    applyBorderToRow(row)
                });

            }

            // ---------------- (trans_10) ----------------
            if (this.trans_10.length > 0) {
                // Total row
                let totalRow = worksheet.addRow([]);
                worksheet.mergeCells(`A${totalRow.number}:E${totalRow.number}`);
                worksheet.getCell(`A${totalRow.number}`).value = "";
                worksheet.mergeCells(`F${totalRow.number}:G${totalRow.number}`);
                worksheet.getCell(`F${totalRow.number}`).value = "";
                worksheet.mergeCells(`H${totalRow.number}:I${totalRow.number}`);
                worksheet.getCell(`H${totalRow.number}`).value = "";
                worksheet.mergeCells(`J${totalRow.number}:K${totalRow.number}`);
                worksheet.getCell(`J${totalRow.number}`).value = this.trans_10_total;
                worksheet.mergeCells(`L${totalRow.number}:N${totalRow.number}`);
                worksheet.getCell(`N${totalRow.number}`).value = "";
                totalRow.eachCell((cell) => {
                    cell.font = { bold: true };
                    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FEF8D8' } };
                });
                applyBorderToRow(totalRow)
                this.trans_10.forEach((item) => {
                    let row = worksheet.addRow([
                        item.date_added,
                        item.costing_name,
                        '',
                        '', 
                        item.var_2,
                        item.var_1,
                        '', 
                        0,
                        '', 
                        item.var_1 * item.var_2,
                        '', 
                        item.health_facility_item_name,
                        '', 
                        '', 
                    ]);
                    worksheet.getCell(`A${row.number}`).alignment = { horizontal: 'center' };
                    worksheet.mergeCells(`B${row.number}:D${row.number}`);
                    worksheet.getCell(`B${row.number}`).alignment = { horizontal: 'left' };
                    worksheet.getCell(`E${row.number}`).alignment = { horizontal: 'center' };
                    worksheet.mergeCells(`F${row.number}:G${row.number}`);
                    worksheet.getCell(`F${row.number}`).alignment = { horizontal: 'right' };
                    worksheet.mergeCells(`H${row.number}:I${row.number}`);
                    worksheet.getCell(`H${row.number}`).alignment = { horizontal: 'right' };
                    worksheet.mergeCells(`J${row.number}:K${row.number}`);
                    worksheet.getCell(`J${row.number}`).alignment = { horizontal: 'right' };
                    worksheet.mergeCells(`L${row.number}:N${row.number}`);
                    worksheet.getCell(`L${row.number}`).alignment = { horizontal: 'center' };
                    applyBorderToRow(row)
                });

            }

            // ---------------- FARMASI (trans_14) ----------------
            if (this.trans_14.length > 0) {
                // Total row
                let totalRow = worksheet.addRow([]);
                worksheet.mergeCells(`A${totalRow.number}:E${totalRow.number}`);
                worksheet.getCell(`A${totalRow.number}`).value = "FARMASI";
                worksheet.mergeCells(`F${totalRow.number}:G${totalRow.number}`);
                worksheet.getCell(`F${totalRow.number}`).value = "";
                worksheet.mergeCells(`H${totalRow.number}:I${totalRow.number}`);
                worksheet.getCell(`H${totalRow.number}`).value = "";
                worksheet.mergeCells(`J${totalRow.number}:K${totalRow.number}`);
                worksheet.getCell(`J${totalRow.number}`).value = this.trans_14_total;
                worksheet.mergeCells(`L${totalRow.number}:N${totalRow.number}`);
                worksheet.getCell(`N${totalRow.number}`).value = "";
                totalRow.eachCell((cell) => {
                    cell.font = { bold: true };
                    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FEF8D8' } };
                });
                applyBorderToRow(totalRow)

                this.trans_14.forEach((item) => {
                    if (item.prescription_item && item.prescription_item.length > 0) {
                        item.prescription_item.forEach((list) => {
                            const qty = (list.is_retur_full === 1 && list.pharmacy_catalog_id_retur) ? 0
                                : list.qty - list.qty_retur;

                            if (qty !== 0 || list.pharmacy_catalog_id_retur) {
                                let row = worksheet.addRow([
                                    item.date_added,
                                    list.pharmacy_catalog_name,
                                    '',
                                    '', 
                                    qty,
                                    list.var_1,
                                    '', 
                                    0,
                                    '', 
                                    list.var_1 * qty,
                                    '', 
                                    "FARMASI",
                                    '', 
                                    '', 
                                ]);
                                worksheet.getCell(`A${row.number}`).alignment = { horizontal: 'center' };
                                worksheet.mergeCells(`B${row.number}:D${row.number}`);
                                worksheet.getCell(`B${row.number}`).alignment = { horizontal: 'left' };
                                worksheet.getCell(`E${row.number}`).alignment = { horizontal: 'center' };
                                worksheet.mergeCells(`F${row.number}:G${row.number}`);
                                worksheet.getCell(`F${row.number}`).alignment = { horizontal: 'right' };
                                worksheet.mergeCells(`H${row.number}:I${row.number}`);
                                worksheet.getCell(`H${row.number}`).alignment = { horizontal: 'right' };
                                worksheet.mergeCells(`J${row.number}:K${row.number}`);
                                worksheet.getCell(`J${row.number}`).alignment = { horizontal: 'right' };
                                worksheet.mergeCells(`L${row.number}:N${row.number}`);
                                worksheet.getCell(`L${row.number}`).alignment = { horizontal: 'center' };
                                applyBorderToRow(row)
                            }
                        });
                    }
                });
            }

            // ========= ADMINISTRASI (trans_13) =========
            if (this.trans_13.length > 0) {
                // let headerRow = worksheet.addRow(['ADMINISTRASI']);
                // worksheet.mergeCells(`A${headerRow.number}:G${headerRow.number}`);
                // headerRow.font = { bold: true };

                let totalRow = worksheet.addRow([]);
                worksheet.mergeCells(`A${totalRow.number}:E${totalRow.number}`);
                worksheet.getCell(`A${totalRow.number}`).value = "Administrasi";
                worksheet.mergeCells(`F${totalRow.number}:G${totalRow.number}`);
                worksheet.getCell(`F${totalRow.number}`).value = "";
                worksheet.mergeCells(`H${totalRow.number}:I${totalRow.number}`);
                worksheet.getCell(`H${totalRow.number}`).value = "";
                worksheet.mergeCells(`J${totalRow.number}:K${totalRow.number}`);
                worksheet.getCell(`J${totalRow.number}`).value = this.trans_13_total;
                worksheet.mergeCells(`L${totalRow.number}:N${totalRow.number}`);
                totalRow.getCell(14).value = "";
                totalRow.eachCell(cell => {
                    cell.font = { bold: true };
                    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FEF8D8' } };
                });
                applyBorderToRow(totalRow)

                this.trans_13.forEach(item => {
                    let row = worksheet.addRow([
                        item.date_added, 
                        item.costing_name,
                        '',
                        '', 
                        1,
                        item.var_1,
                        '',
                        item.var_4,
                        '',
                        item.count_total,
                        '',
                        this.regData.health_facility_item_name,
                        '',
                        '',
                    ]);
                    worksheet.getCell(`A${row.number}`).alignment = { horizontal: 'center' };
                    worksheet.mergeCells(`B${row.number}:D${row.number}`);
                    worksheet.getCell(`B${row.number}`).alignment = { horizontal: 'left' };
                    worksheet.getCell(`E${row.number}`).alignment = { horizontal: 'center' };
                    worksheet.mergeCells(`F${row.number}:G${row.number}`);
                    worksheet.getCell(`F${row.number}`).alignment = { horizontal: 'right' };
                    worksheet.mergeCells(`H${row.number}:I${row.number}`);
                    worksheet.getCell(`H${row.number}`).alignment = { horizontal: 'right' };
                    worksheet.mergeCells(`J${row.number}:K${row.number}`);
                    worksheet.getCell(`J${row.number}`).alignment = { horizontal: 'right' };
                    worksheet.mergeCells(`L${row.number}:N${row.number}`);
                    worksheet.getCell(`L${row.number}`).alignment = { horizontal: 'center' };
                    applyBorderToRow(row)
                });
            } 

            // ----------- GRAND TOTAL ----------
            let grandTotalRow = worksheet.addRow([]);

            // Merge label cells
            worksheet.mergeCells(`A${grandTotalRow.number}:E${grandTotalRow.number}`);
            worksheet.getCell(`A${grandTotalRow.number}`).value = "Grand Total";
            worksheet.getCell(`A${grandTotalRow.number}`).alignment = { horizontal: 'right' };

            worksheet.mergeCells(`F${grandTotalRow.number}:G${grandTotalRow.number}`);
            worksheet.getCell(`F${grandTotalRow.number}`).value = "";
            worksheet.mergeCells(`H${grandTotalRow.number}:I${grandTotalRow.number}`);
            worksheet.getCell(`H${grandTotalRow.number}`).value = "";

            // Put the value in column M (Subtotal column)
            worksheet.mergeCells(`J${grandTotalRow.number}:K${grandTotalRow.number}`);
            worksheet.getCell(`J${grandTotalRow.number}`).value = this.grand_total; 
            worksheet.getCell(`J${grandTotalRow.number}`).alignment = { horizontal: 'right' };

            // Empty out column N
            worksheet.mergeCells(`L${grandTotalRow.number}:N${grandTotalRow.number}`);
            worksheet.getCell(`N${grandTotalRow.number}`).value = "";

            // Style the row
            grandTotalRow.eachCell((cell) => {
                cell.font = { bold: true };
                cell.fill = {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: { argb: 'FFD966' } // gold/yellow highlight
                };
                cell.border = {
                    top: { style: 'thin' },
                    left: { style: 'thin' },
                    bottom: { style: 'thin' },
                    right: { style: 'thin' }
                };
            });

            // ----------- DOWNLOAD ----------
            const buffer = await workbook.xlsx.writeBuffer();
            const blob = new Blob([buffer], { 
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' 
            });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `Invoice_${this.regData.name}_${this.regData.norm}.xlsx`;
            link.click();
        }
    },
    created () {
        if (this.$route.params.id) {
            // this.show = true
            this.id = this.$route.params.id
            this.getData(this.$route.params.id)
            this.getTransactionData ()
            this.$store.dispatch('helper/getParam', 'costing_c_one')
        }
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>